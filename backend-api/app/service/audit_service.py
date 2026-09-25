from app.supabase_config.supabase import supabase_secondary
from typing import Optional

def log_audit(
    user_email: str,
    user_role: str,
    action: str,         
    module: str,         
    details: Optional[str] = None,
    user_id: Optional[str] = None,
    ip_address: Optional[str] = None
):
    try:
        data = {
            "user_id": user_id,
            "user_email": user_email,
            "user_role": user_role,
            "action": action,
            "module": module,
            "details": details,
            "ip_address": ip_address
        }
        supabase_secondary.table("audit_logs").insert(data).execute()
    except Exception as e:
        print(f"DEBUG: Failed to insert audit log: {e}")