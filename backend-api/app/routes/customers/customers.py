import math
from fastapi import APIRouter, HTTPException, Query, status
from typing import Optional
from uuid import UUID
from app.schemas.customer import CustomerResponse, PaginatedResponse, PaginationMeta, BookingCreate
from app.supabase_config.supabase import supabase_secondary

router = APIRouter(
    prefix="/api/v1/customers",
    tags=["Customers Directory"]
)

# 1. Stats endpoint
@router.get("/stats", response_model=dict)
async def get_customer_stats():
    try:
        res = supabase_secondary.table("customers").select("tier").execute()
        rows = res.data or []

        stats = {
            "all": len(rows),
            "bronze": 0,
            "silver": 0,
            "gold": 0,
            "platinum": 0
        }

        for row in rows:
            # Format value to lowercase 
            tier_val = str(row.get("tier") or "BRONZE").strip().lower()
            if tier_val in stats:
                stats[tier_val] += 1

        return {
            "status": "success",
            "data": stats
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# 2. Get All Customers
@router.get("", response_model=dict)
async def get_all_customers(
    tier: Optional[str] = Query(None, description="Filter by: BRONZE, SILVER, GOLD, PLATINUM"),
    search: Optional[str] = Query(None, description="Search by company name or contact person")
):
    try:
        query = supabase_secondary.table("customers").select("*")

        if tier and tier.lower() != "all":
            query = query.eq("tier", tier.upper())

        if search:
            query = query.or_(f"company_name.ilike.%{search}%,contact_person.ilike.%{search}%")

        res = query.order("created_at", desc=True).execute()

        return {
            "status": "success",
            "data": res.data or []
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# 3. Get Single Customer
@router.get("/{customer_id}", response_model=CustomerResponse)
async def get_customer_by_id(customer_id: UUID):
    try:
        res = (
            supabase_secondary.table("customers")
            .select("*")
            .eq("id", str(customer_id))
            .execute()
        )

        if not res.data:
            raise HTTPException(status_code=404, detail="Customer not found.")

        return res.data[0]

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# 4. Create Booking for an Existing Customer
@router.post("/{customer_id}/bookings", status_code=status.HTTP_201_CREATED)
async def create_booking_for_customer(customer_id: UUID, payload: BookingCreate):
    try:
        # Step A: I-check kung  umiiral ang customer
        customer_check = (
            supabase_secondary.table("customers")
            .select("id")
            .eq("id", str(customer_id))
            .execute()
        )

        if not customer_check.data:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND, 
                detail="Cannot create booking. Customer not found."
            )

        # Step B: Ihanda ang data at i-bind ang customer_id
        booking_data = payload.model_dump(exclude_unset=True)
        booking_data["customer_id"] = str(customer_id)

        # Step C: Isave sa 'bookings' table
        res = supabase_secondary.table("bookings").insert(booking_data).execute()

        if not res.data:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST, 
                detail="Failed to create booking."
            )

        return {
            "status": "success",
            "message": "Booking created successfully",
            "data": res.data[0]
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        raise HTTPException(status_code= status.HTTP_500_INTERNAL_SERVER_ERROR, detail=str(e))