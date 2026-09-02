import math
from fastapi import APIRouter, HTTPException, Query, status
from typing import Optional
from uuid import UUID
from app.supabase_config.supabase import supabase_secondary
from app.schemas.shipment_booking import BookingResponse, PaginatedBookingResponse, BookingCreate, BookingUpdate


router = APIRouter(
    prefix="/api/v1/shipment-bookings",
    tags=["Shipment Booking"]
)

# 1. Stats or status endpoint for filters tabs
@router.get("/stats", response_model=dict)
async def get_shipment_booking_stats():
    try:
        res = supabase_secondary.table("bookings").select("booking_status").execute()
        rows = res.data or []

        stats = {
            "all": len(rows),
            "booking": 0,   # galing sa Ai chat
            "quoted": 0,    # Nabigyan na ng Price
            "confirmed": 0, # Approved na ni customer
            "cancelled": 0  # cancelled transac
        }

        for row in rows:
            st = str(row.get("booking_status") or "booking").strip().lower()
            if st in stats:
                stats[st] += 1
            else:
                stats["booking"] += 1

        return {
            "status": "success",
            "data": stats
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# 2. Get booking list using PaginatedBookingResponse Schema
@router.get("", response_model=PaginatedBookingResponse)
async def get_shipment_bookings(
    status_filter: Optional[str] = Query(None, alias="status", description="Filter by status: pending, quoted, confirmed, cancelled"),
    search: Optional[str] = Query(None, description="Search by booking code, service type, origin, or destination"),
    page: int = Query(1, ge=1, description="Page number"),
    limit: int = Query(5, ge=1, description="Items per page limit (default: 5)")
):

    try:
        # Join bookings -> customers
        query =  supabase_secondary.table("bookings").select(
            "*, customers(company_name, contact_person, email, phone_number, tier)",
            count="exact"
        )

        # Apply status filter
        if status_filter and status_filter.lower() != "all":
            query = query.ilike("booking_status", status_filter.strip())

        # Apply search filter
        if search:
            query = query.or_(
                f"booking_code.ilike.%{search}%,service_type.ilike.%{search}%,origin.ilike.%{search}%,destination.ilike.%{search}%"
            )

        # Pagination Calculation
        start = (page - 1) * limit
        end = start + limit - 1

        res = query.order("created_at", desc=True).range(start, end).execute()

        total_count = res.count if res.count is not None else len(res.data or [])
        total_pages = math.ceil(total_count / limit) if total_count > 0 else 1

        return {
            "status": "success",
            "data": res.data or [],
            "meta": {
                "total": total_count,
                "page": page,
                "limit": limit,
                "total_pages": total_pages
            }
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# 3. Get single booking by Id
@router.get("/{booking_id}", response_model=BookingResponse)
async def get_booking_by_id(booking_id: UUID):
    try:
        res = (
            supabase_secondary.table("bookings")
            .select("*, customers(company_name, contact_person, email, phone_number, tier)")
            .eq("id", str(booking_id))
            .execute()
        )

        if not res.data:
            raise HTTPException(status_code=404, detail="Shipment booking not found.")

        return res.data[0]

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# 4. Update booking para sa pricing / status change ni sales
@router.patch("/{booking_id}", response_model=dict)
async def update_shipment_booking(booking_id: UUID, payload: BookingUpdate):
    try:
        update_data = payload.model_dump(exclude_unset=True)

        if not update_data:
            raise HTTPException(status_code=400, detail="No fields provided for update.")

        res = (
            supabase_secondary.table("bookings")
            .update(update_data)
            .eq("id", str(booking_id))
            .execute()
        )
            
        

        if not res.data:
            raise HTTPException(status_code=404, detail="Booking update failed or record not found.")

        return {
            "status": "success",
            "message": "Shipment booking updated successfully",
            "data": res.data[0]
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))