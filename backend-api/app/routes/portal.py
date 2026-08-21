from fastapi import APIRouter, Header, HTTPException, Depends
from typing import Optional
from app.supabase_config.supabase import supabase, supabase_secondary

router = APIRouter(
    prefix="/api/v1/portal",
    tags=["Customer Portal"]
)

@router.get("/profile")
def get_profile(x_user_id: Optional[str] = Header(None, alias="x-user-id")):
    # 1. Tiyaking may pumasok na x-user-id header
    if not x_user_id or x_user_id.strip() == "":
        raise HTTPException(
            status_code=400, 
            detail="Header 'x-user-id' is missing or empty."
        )

    try:
        profile = None

        # 2. Priority 1: Query muna sa Secondary Supabase Customer
        try:
            sec_response = supabase_secondary.table("users").select("*").eq("id", x_user_id).execute()
            if sec_response.data and len(sec_response.data) > 0:
                profile = sec_response.data[0]
        except Exception as sec_err:
            print(f"[Secondary DB Check Error]: {str(sec_err)}")

        # 3. Priority 2: Fallback sa Primary Supabase Sale's / Admin
        if not profile:
            try:
                pri_response = supabase.table("profiles").select("*").eq("id", x_user_id).execute()
                if pri_response.data and len(pri_response.data) > 0:
                    profile = pri_response.data[0]
            except Exception as pri_err:
                print(f"[Primary DB Check Error]: {str(pri_err)}")

        # 4. Kapag parehong walang nahanap na record
        if not profile:
            raise HTTPException(
                status_code=404, 
                detail=f"Profile not found for user ID: {x_user_id}"
            )
        
        # 5. Helper field para sa full name
        first_name = profile.get("first_name", "") or ""
        last_name = profile.get("last_name", "") or ""
        profile["full_name"] = f"{first_name} {last_name}".strip()
        
        return profile

    except HTTPException as http_err:
        # I-pasa pabalik ang HTTP errors (400, 404)
        raise http_err
    except Exception as e:
        print(f"[Supabase Error]: {str(e)}")
        raise HTTPException(
            status_code=500, 
            detail=f"Database query failed: {str(e)}"
        )