from fastapi import APIRouter, HTTPException, BackgroundTasks
from typing import Optional, List
import secrets
from app.supabase_config.supabase import supabase_secondary
from app.schemas.admin import CloseWonTicketResponseSchema, CreateCustomerFromTicketSchema, CustomerUserResponse
from app.service.email_service import send_customer_welcome_email

router = APIRouter(
    prefix="/api/v1/admin",
    tags=["Admin Management"]
)

# HELPER: Para sa Tier computation ng Customers table
def calculate_tier(bookings_count: int) -> str:
    if bookings_count >= 20:
        return "PLATINUM"
    elif bookings_count >= 10:
        return "GOLD"
    elif bookings_count >= 5:
        return "SILVER"
    return "BRONZE"


# 1. Kunin ang mga close won tickets na "for account" pa lang ang status
@router.get("/close-won-tickets", response_model=list[CloseWonTicketResponseSchema])
async def get_close_won_tickets():
    try:
        # Kukuha lang ng tickets 
        res = (
            supabase_secondary.table("tickets")
            .select("*")
            .eq("ticket_status", "for account")
            .execute()
        )
        tickets = res.data or []

        return tickets
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# 2. Create customer portal from close won ticket
@router.post("/create-customer-from-ticket")
async def create_customer_from_ticket(
    payload: CreateCustomerFromTicketSchema, 
    background_tasks: BackgroundTasks
):
    try:
        new_user_id = None

        # --- DAGDAG: Check muna sa public.users kung registered na ---
        existing_user = (
            supabase_secondary.table("users")
            .select("id")
            .eq("email", payload.email)
            .execute()
        )

        if existing_user.data:
            # Gamitin ang umiiral na user ID kung gawan uli ng panibagong ticket ang customer
            new_user_id = existing_user.data[0]["id"]
        else:
            # 1. Create Auth account sa Secondary supabase (Orihinal mong code)
            try:
                auth_res = supabase_secondary.auth.admin.create_user({
                    "email": payload.email,
                    "password": payload.password,
                    "email_confirm": True,
                    "user_metadata": {
                        "first_name": payload.first_name,
                        "last_name": payload.last_name,
                        "role": "customer"
                    }
                })

                if not auth_res.user:
                    raise HTTPException(status_code=400, detail="Failed to create Auth account.")

                new_user_id = auth_res.user.id
            except Exception as auth_err:
                raise HTTPException(status_code=400, detail=str(auth_err))

            # 2. Mag-insert sa public.users table (Secondary DB) (Orihinal mong code)
            user_data = {
                "id": new_user_id,
                "email": payload.email,
                "first_name": payload.first_name,
                "last_name": payload.last_name,
                "company_name": payload.company_name,
                "phone_number": payload.phone_number,
                "role": "customer"
            }

            supabase_secondary.table("users").insert(user_data).execute()

        # --- DAGDAG: Insert o Update sa public.customers Table ---
        existing_cust = (
            supabase_secondary.table("customers")
            .select("*")
            .eq("email", payload.email)
            .execute()
        )

        full_contact_name = f"{payload.first_name} {payload.last_name}".strip()

        if existing_cust.data:
            # Kapag nagawan uli ng panibagong ticket, increment total_bookings at compute new tier
            c_rec = existing_cust.data[0]
            new_bookings = (c_rec.get("total_bookings") or 0) + 1
            new_tier = calculate_tier(new_bookings)

            supabase_secondary.table("customers").update({
                "company_name": payload.company_name or c_rec["company_name"],
                "contact_person": full_contact_name or c_rec["contact_person"],
                "phone_number": payload.phone_number or c_rec["phone_number"],
                "total_bookings": new_bookings,
                "tier": new_tier
            }).eq("id", c_rec["id"]).execute()
        else:
            # Kapag kauna-unahang beses gawan ng account
            new_cust_data = {
                "company_name": payload.company_name,
                "contact_person": full_contact_name,
                "email": payload.email,
                "phone_number": payload.phone_number,
                "total_bookings": 1,
                "tier": "BRONZE",
                "created_by_ticket_id": payload.ticket_id
            }
            supabase_secondary.table("customers").insert(new_cust_data).execute()

        # 3. I-update ang ticket: I-set ang customer_id AT baguhin ang ticket_status -> 'created' (Orihinal mong code)
        supabase_secondary.table("tickets").update({
            "customer_id": new_user_id,
            "ticket_status": "created"
        }).eq("id", payload.ticket_id).execute()

        # 4. Background task para sa welcome email (Orihinal mong code)
        background_tasks.add_task(
            send_customer_welcome_email,
            to_email=payload.email,
            first_name=payload.first_name,
            password=payload.password,
            customer_id=new_user_id,
            company_name=payload.company_name or "SwiftFreight Client"
        )

        return {
            "status": "success",
            "message": "Customer portal account created and notification email sent!",
            "user_id": new_user_id
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/customer-accounts", response_model=dict)
async def get_customer_accounts():
    try:
        # Kukunin ang lahat ng rows sa public.users table (Orihinal mong code)
        res = supabase_secondary.table("users").select("*").execute()
        return {
            "status": "success", 
            "data": res.data or []
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))