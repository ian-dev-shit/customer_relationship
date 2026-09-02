from google import genai
from google.genai import types
from app.supabase_config.supabase import gemini_client, supabase_secondary
from app.supabase_config.database import chat_collection
from app.service.promt import LOGISTICS_SALES_SYSTEM_INSTRUCTION
from datetime import datetime, timezone, timedelta


# Fuction tool ito tatawagin ng Gemini kapag complete na ang information
def create_customer_booking(customer_id: str, service_type: str, origin: str, destination: str):
    # Save sa bookings table kapag complete na ang details

    try:
        booking_data ={
            "customer_id": customer_id,
            "service_type": service_type,
            "origin": origin,
            "destination": destination,
            "booking_status": "New Book"
        }

        res = supabase_secondary.table("bookings").insert(booking_data).execute()
        return {
            "status": "success",
            "booking_id": res.data[0]["id"]
        }

    except Exception as e:
        return {
            "status": "error",
            "message": str(e)
        }


async def process_customer_chat(customer_id: str, user_message: str, sender_role: str = "customer"):
    try:
        now = datetime.now(timezone.utc)
        session = await chat_collection.find_one({"customer_id": customer_id})
        
        current_status = session.get("status", "ai_active") if session else "ai_active"
        last_updated = session.get("last_updated") if session else None

        # 1. 5-HOUR COUNTDOWN CHECK:
        # Kung naka-human takeover PERO higit 5 oras na ang nakalipas mula sa huling message
        if current_status == "human_takeover" and last_updated:
            # Siguraduhing timezone-aware ang timezone ng last_updated
            if last_updated.tzinfo is None:
                last_updated = last_updated.replace(tzinfo=timezone.utc)
                
            time_difference = now - last_updated
            if time_difference > timedelta(hours=5):
                current_status = "ai_active" # Auto-reset pabalik kay AI

        # 2. KAPAG SI SALES AGENT ANG NAG-CHAT:
        if sender_role == "sales_agent":
            new_convo = [{"role": "model", "parts": [{"text": user_message}]}]
            await chat_collection.update_one(
                {"customer_id": customer_id},
                {
                    "$set": {
                        "status": "human_takeover",
                        "last_updated": now # I-update ang timestamp tuwing nag-reply si Sales
                    },
                    "$push": {"history": {"$each": new_convo}}
                },
                upsert=True
            )
            return user_message

        # 3. KAPAG SI CUSTOMER ANG NAG-CHAT AT NAKA-HUMAN TAKEOVER PA WITHOUT 5H TIMEOUT
        if current_status == "human_takeover":
            new_convo = [{"role": "user", "parts": [{"text": user_message}]}]
            await chat_collection.update_one(
                {"customer_id": customer_id},
                {
                    "$set": {"last_updated": now},
                    "$push": {"history": {"$each": new_convo}}
                }
            )
            return None # Tatahimik si AI 

        # 4. KAPAG AI ACTIVE BAGO ANG SESSION O NAG-RESET NA MATAPOS ANG 5 HOURS
        raw_history = session.get("history", []) if session else []

        chat_history = []
        for item in raw_history:
            chat_history.append(
                types.Content(
                    role=item["role"],
                    parts=[types.Part.from_text(text=item["parts"][0]["text"])]
                )
            )

        dynamic_instruction = f"{LOGISTICS_SALES_SYSTEM_INSTRUCTION}\n\nCurrent Customer ID in Session: {customer_id}"

        chat = gemini_client.chats.create(
            model='gemini-3.6-flash',
            history=chat_history,
            config=types.GenerateContentConfig(
                system_instruction=dynamic_instruction,
                tools=[create_customer_booking],
                temperature=0.2
            )
        )

        response = chat.send_message(user_message)
        ai_reply = response.text

        new_convo = [
            {"role": "user", "parts": [{"text": user_message}]},
            {"role": "model", "parts": [{"text": ai_reply}]}
        ]

        await chat_collection.update_one(
            {"customer_id": customer_id},
            {
                "$set": {
                    "status": "ai_active",
                    "last_updated": now
                },
                "$push": {"history": {"$each": new_convo}}
            },
            upsert=True
        )

        return ai_reply

    except Exception as e:
        print(f"Error in process_customer_chat: {e}")
        raise e