from fastapi import APIRouter, Header, HTTPException, Depends
from typing import Optional
from app.supabase_config.supabase import supabase

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
        # 2. Query sa Supabase
        response = supabase.table("profiles").select("*").eq("id", x_user_id).execute()
        
        # 3. Kapag walang nahanap na record
        if not response.data:
            raise HTTPException(
                status_code=404, 
                detail=f"Profile not found for user ID: {x_user_id}"
            )
            
        profile = response.data[0]
        
        # Helper field para sa full name
        first_name = profile.get("first_name", "") or ""
        last_name = profile.get("last_name", "") or ""
        profile["full_name"] = f"{first_name} {last_name}".strip()
        
        return profile

    except HTTPException as http_err:
        # I-pasa pabalik ang 400 o 404 errors sa halip na gawing 500
        raise http_err
    except Exception as e:
        print(f"[Supabase Error]: {str(e)}")
        raise HTTPException(
            status_code=500, 
            detail=f"Database query failed: {str(e)}"
        )

