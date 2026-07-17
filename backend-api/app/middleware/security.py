# RBAC

from fastapi import Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from app.supabase.supabase import supabase

# Gamit ang ang HTTPBearer para mag-expect ng "Authorization: Bearer <TOKEN>" sa headers
security_scheme = HTTPBearer()


# 1. FUNCTION PARA I-VERIFY KUNG VALID ANG JWT TOKEN NG USER VIA SUPABASE
def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security_scheme)):
    token = credentials.credentials

    try:
        # ask si supabase kung valid at active ang token
        user_response = supabase.auth.get_user(token)
        if not user_response.user:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid or expired token"
            )
        
        # Ibalik ang user  object mula sa supabase auth
        return user_response.user
    
    except Exception:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Could not validate credential"
        )
    
# Guard class para sa RBAC
class RoleChecker:
    def __init__(self, allowed_roles: list[str]):
        self.allowed_roles = allowed_roles

    def __call__(self, current_user = Depends(get_current_user)):
        # Gagamitin ang supabase client para i-query ang profiles table
        profile = supabase.table('profiles').select('role').eq('id', current_user.id).single().execute()
        
        if not profile.data:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="User profile not found"
            )
        
        user_role = profile.data.get("role")

        # I verify kung ang role ng user ay kasama sa role table sa supabase
        if user_role not in self.allowed_roles:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="You dont have permission to access this resources."
            )
        
        return current_user



