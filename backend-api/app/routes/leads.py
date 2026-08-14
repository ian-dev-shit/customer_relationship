from fastapi import APIRouter, HTTPException, Query, Path
from typing import Optional
from app.supabase_config.supabase import supabase_secondary
from app.schemas.lead import (
    LeadResponseSchema, 
    PaginatedLeadResponseSchema, 
    LeadStatsResponseSchema, 
    StatusUpdateSchema
)

router = APIRouter(
    prefix="/api/v1/leads",
    tags=["Sales Agent Leads"]
)

@router.get("/", response_model=PaginatedLeadResponseSchema)
async def get_leads(
    status: Optional[str] = Query(None, description="Filter by status e.g. new_inquiry, qualifying, quote_sent"),
    search: Optional[str] = Query(None, description="Search by company_name or contact_person"),
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100)
):
    try:
        offset = (page - 1) * limit
        query = supabase_secondary.table("inquiries").select("*", count="exact")

        if status and status.lower() != "all":
            query = query.eq("status", status)

        if search:
           
            query = query.or_(f"company_name.ilike.%{search}%,contact_person.ilike.%{search}%")

        query = query.order("created_at", desc=True).range(offset, offset + limit - 1)
        response = query.execute()

        return {
            "total": response.count if response.count is not None else len(response.data),
            "data": response.data
        }
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/stats", response_model=LeadStatsResponseSchema)
async def get_lead_stats():
    try:
        res = supabase_secondary.table("inquiries").select("status").execute()
        data = res.data or []

        return {
            "all": len(data),
            "new_inquiry": sum(1 for item in data if item.get("status") == "new_inquiry"),
            "qualifying": sum(1 for item in data if item.get("status") == "qualifying"),
            "quote_sent": sum(1 for item in data if item.get("status") == "quote_sent"),
            "negotiation": sum(1 for item in data if item.get("status") == "negotiation"),
            "closed_won": sum(1 for item in data if item.get("status") == "closed_won"),
            "closed_lost": sum(1 for item in data if item.get("status") == "closed_lost"),
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.patch("/{lead_id}/status", response_model=LeadResponseSchema)
async def update_lead_status(
    payload: StatusUpdateSchema,
    lead_id: str = Path(..., description="UUID ng lead record")
):
    try:
        response = (
            supabase_secondary.table("inquiries")
            .update({"status": payload.status})
            .eq("id", lead_id)
            .execute()
        )

        if not response.data:
            raise HTTPException(status_code=404, detail="Lead ID not found.")

        return response.data[0]

    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))