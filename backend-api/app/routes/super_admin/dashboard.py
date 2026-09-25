from fastapi import APIRouter, HTTPException, status
from app.supabase_config.supabase import supabase_secondary
from datetime import datetime, timezone

router = APIRouter(prefix="/api/v1/super_admin/dashboard", tags=["Admin Dashboard"])

@router.get("/stats")
async def get_dashboard_stats():
    try:
        # 1. Bilang ng Restricted Accounts
        restricted_res = supabase_secondary.table("restrict_attempt") \
            .select("id", count="exact") \
            .eq("is_restricted", True) \
            .execute()
        restricted_count = restricted_res.count or 0

        # 2. Top 5 Restricted Accounts para sa Dashboard Table
        recent_restricted = supabase_secondary.table("restrict_attempt") \
            .select("*") \
            .eq("is_restricted", True) \
            .order("last_attempt_at", desc=True) \
            .limit(5) \
            .execute()

        # 3. Bilang ng Total Audit Logs
        audit_res = supabase_secondary.table("audit_logs") \
            .select("id", count="exact") \
            .execute()
        audit_count = audit_res.count or 0

        # 4. Top 5 Recent Audit Logs
        recent_audits = supabase_secondary.table("audit_logs") \
            .select("*") \
            .order("created_at", desc=True) \
            .limit(5) \
            .execute()

        # 5. Today's Successful Logins Count
        today_start = datetime.now(timezone.utc).strftime("%Y-%m-%dT00:00:00Z")
        logins_res = supabase_secondary.table("audit_logs") \
            .select("id", count="exact") \
            .eq("action", "LOGIN_SUCCESS") \
            .gte("created_at", today_start) \
            .execute()
        today_logins = logins_res.count or 0

        return {
            "restricted_count": restricted_count,
            "today_logins": today_logins,
            "audit_count": audit_count,
            "recent_restricted": recent_restricted.data or [],
            "recent_audits": recent_audits.data or []
        }

    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch dashboard stats: {str(e)}"
        )