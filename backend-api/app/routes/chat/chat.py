import os
from datetime import datetime, timezone
from fastapi import APIRouter, WebSocket, WebSocketDisconnect, HTTPException
from app.supabase_config.supabase import supabase_secondary
from app.service.websocket_manager import manager
from app.service.sintement import analyze_message_and_extracted_details

router = APIRouter(
    prefix="",
    tags=["Customer Chat"]
)

@router.websocket("/customer/v1/chat/ws/chat/{conversation_id}")
async def websocket_chat_endpoint(websocket: WebSocket, conversation_id: str):
    await manager.connect(conversation_id, websocket)

    try:
        while True:
            data = await websocket.receive_json()
            sender_type = data.get("sender_type") # customer or agent
            sender_id = data.get("sender_id")
            user_message = data.get("message")

            # 1. Fetch current conversation status sa conversations table
            conv_response = supabase_secondary.table("conversations")\
                .select("*")\
                .eq("id", conversation_id)\
                .single()\
                .execute()

            conv_data = conv_response.data or {}
            current_status = conv_data.get("status", "ai_handling")
            customer_id = conv_data.get("customer_id")
            current_details = conv_data.get("extracted_details") or {}

            # 2. Save message sa message table
            saved_msg = supabase_secondary.table("messages").insert({
                "conversation_id": conversation_id,
                "sender_type": sender_type,
                "sender_id": sender_id if sender_type == "customer" else None,
                "message": user_message
            }).execute().data[0]

            await manager.broadcast(conversation_id, saved_msg)

            # 3. Scene: Customer nag chat at AI agent ang mag-handle
            if sender_type == "customer" and current_status == "ai_handling":
                ai_result = await analyze_message_and_extracted_details(user_message, current_details)

                ai_reply = ai_result.get("reply")
                sentiment = ai_result.get("sentiment")
                updated_details = ai_result.get("update_details")
                is_complete = ai_result.get("is_complete")
                force_handoff = ai_result.get("force_handoff")

                # Update extracted details & sentiment sa conversation table
                supabase_secondary.table("conversations")\
                    .update({
                        "extracted_details": updated_details,
                        "sentiment": sentiment,
                        "updated_at": datetime.now(timezone.utc).isoformat()
                    })\
                    .eq("id", conversation_id)\
                    .execute()

                # Save & Broadcast AI Reply
                ai_msg = supabase_secondary.table("messages").insert({
                    "conversation_id": conversation_id,
                    "sender_type": "ai",
                    "message": ai_reply
                }).execute().data[0]
                await manager.broadcast(conversation_id, ai_msg)

                # 4. HANDOFF & BOOKING CREATION TRIGGER
                if is_complete or force_handoff:
                    supabase_secondary.table("conversations")\
                        .update({"status": "pending_sales"})\
                        .eq("id", conversation_id)\
                        .execute()

                    supabase_secondary.table("bookings").insert({
                        "customer_id": customer_id,
                        "service_type": updated_details.get("service_type") if updated_details else None,
                        "origin": updated_details.get("origin") if updated_details else None,
                        "destination": updated_details.get("destination") if updated_details else None,
                        "cargo_details": updated_details.get("cargo_details") if updated_details else None,
                        "booking_status": "Booking",
                        "assigned_agent_id": None
                    }).execute()

                    handoff_sys_msg = supabase_secondary.table("messages").insert({
                        "conversation_id": conversation_id,
                        "sender_type": "system",
                        "message": "Thank you for the info! I have handed this over to a sales agent for accurate pricing."
                    }).execute().data[0]
                    await manager.broadcast(conversation_id, handoff_sys_msg)

            # Scene Kapag si sales agent ang nag reply
            elif sender_type in ["sales_agent", "agent"]:
                supabase_secondary.table("conversations")\
                    .update({
                        "status": "agent_handling",
                        "updated_at": datetime.now(timezone.utc).isoformat()
                    })\
                    .eq("id", conversation_id)\
                    .execute()

    except WebSocketDisconnect:
        manager.disconnect(conversation_id, websocket)



# FETCH MESSAGES BY CONVERSATION ID 
@router.get("/agent/v1/chat/messages/{conversation_id}")
async def get_chat_messages(conversation_id: str):
    try:
        response = supabase_secondary.table("messages")\
            .select("*")\
            .eq("conversation_id", conversation_id)\
            .order("created_at", desc=False)\
            .execute()

        messages = response.data or []
        formatted_messages = []

        for msg in messages:
            created_at = msg.get("created_at")
            formatted_time = ""
            
            if created_at:
                try:
                    time_part = created_at.split("T")[1][:5]
                    formatted_time = time_part
                except Exception:
                    formatted_time = created_at

            formatted_messages.append({
                "id": msg.get("id"),
                "conversation_id": msg.get("conversation_id"),
                "sender_type": msg.get("sender_type"),
                "sender_id": msg.get("sender_id"),
                "message": msg.get("message"),
                "created_at": created_at,
                "formatted_time": formatted_time
            })

        return formatted_messages

    except Exception as e:
        print(f"Error fetching messages: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Database error: {str(e)}")


# Customer conversations list para sa customer portal
@router.get("/customer/v1/chat/conversations/{customer_id}")
async def get_customer_conversations(customer_id: str):
    try:
        response = supabase_secondary.table("conversations")\
            .select("*")\
            .eq("customer_id", customer_id)\
            .execute()
        return response.data or []
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# Agent conversations list
@router.get("/agent/v1/chat/conversations")
async def get_agent_conversations():
    try:
        conv_response = supabase_secondary.table("conversations")\
            .select("*")\
            .order("updated_at", desc=True)\
            .execute()
        
        conversations = conv_response.data or []
        formatted_conversations = []

        for conv in conversations:
            conv_id = conv.get("id")
            customer_id = conv.get("customer_id")
            
            customer_display_name = "Unknown Customer"
            avatar_url = None
            customer_initial = "C"

            if customer_id:
                # Naka-comment out muna si profile_pic hangga't wala sa DB
                cust_res = supabase_secondary.table("customers")\
                    .select("contact_person, company_name")\
                    .eq("id", customer_id)\
                    .maybe_single()\
                    .execute()
                
                if cust_res and cust_res.data:
                    c_data = cust_res.data
                    contact = c_data.get("contact_person")
                    company = c_data.get("company_name")
                    
                    if contact and company:
                        customer_display_name = f"{contact} ({company})"
                    else:
                        customer_display_name = contact or company or "Unknown Customer"
                    
                    target_name = (contact or company or "").strip()
                    if target_name:
                        customer_initial = target_name[0].upper()

            msg_res = supabase_secondary.table("messages")\
                .select("message, created_at")\
                .eq("conversation_id", conv_id)\
                .order("created_at", desc=True)\
                .limit(1)\
                .execute()
            
            last_msg_data = msg_res.data[0] if (msg_res and msg_res.data) else {}
            raw_time = last_msg_data.get("created_at") or conv.get("updated_at")

            formatted_conversations.append({
                "id": conv_id,
                "customer_name": customer_display_name,
                "customer_initial": customer_initial,
                "avatar_url": avatar_url,
                "status": conv.get("status", "ai_handling"),
                "last_message": last_msg_data.get("message", "No messages yet"),
                "last_message_time": raw_time
            })

        return formatted_conversations
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))