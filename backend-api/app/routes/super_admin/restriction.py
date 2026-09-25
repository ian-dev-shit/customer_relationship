from fastapi import APIRouter, HTTPException, status, Query
from typing import List, Optional
from app.supabase_config.supabase import supabase_secondary
from app.schemas.restriction import RestrictedUserResponse, UnrestrictResponse
from datetime import datetime, timezone

router = APIRouter(
    prefix="/api/v1/super_admin/restrictions",
    tags=["Security & Account Restrictions"]
)


# 1. Endpoint para kunin ang lahat ng Restricted Users
@router.get("/restricted-users", response_model=List[RestrictedUserResponse])
async def get_restricted_users(only_restricted: bool = Query(True, description="Filter only users who are currently restricted")):
    try:
        query = supabase_secondary.table("restrict_attempt").select("*")
        
        # Kapag only_restricted=True, iyong mga nakalock (is_restricted=True) lang ang ilalabas
        if only_restricted:
            query = query.eq("is_restricted", True)

        res = query.order("last_attempt_at", desc=True).execute()
        
        return res.data or []
    
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch restricted users: {str(e)}"
        )


# 2. Endpoint para i-unrestrict o i-reset ang account ng user (Manual Unlock)
@router.post("/unrestrict-user", response_model=UnrestrictResponse)
async def unrestrict_user(email: str):
    email_clean = email.strip().lower()
    
    try:
        # Tignan muna kung umiiral ang record
        existing = supabase_secondary.table("restrict_attempt") \
            .select("*") \
            .eq("email", email_clean) \
            .maybe_single() \
            .execute()

        if not existing.data:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail=f"No restriction record found for email: {email_clean}"
            )

        # I-reset ang failed attempts at tanggalin ang restriction
        supabase_secondary.table("restrict_attempt").update({
            "failed_attempts": 0,
            "is_restricted": False,
            "restricted_until": None
        }).eq("email", email_clean).execute()

        return {
            "status": "success",
            "message": f"Successfully unrestricted account for {email_clean}",
            "email": email_clean
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to unrestrict user: {str(e)}"
        )