from fastapi import APIRouter, HTTPException, Query
from typing import Optional, List
from app.supabase_config.supabase import supabase_secondary

router = APIRouter(prefix="/api/v1/super_admin/audit", tags=["Audit Trail"])

@router.get("/logs")
async def get_audit_logs(
    role_filter: Optional[str] = Query(None, description="Filter by user_role (e.g. customer, sales_agent, super_admin)"),
    module_filter: Optional[str] = Query(None, description="Filter by module")
):
    try:
        query = supabase_secondary.table("audit_logs").select("*")

        if role_filter:
            query = query.eq("user_role", role_filter)
            
        if module_filter:
            query = query.eq("module", module_filter)

        res = query.order("created_at", desc=True).limit(200).execute()
        return res.data or []
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))