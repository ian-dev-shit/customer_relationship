# RBAC File

from fastapi import Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from app.supabase_config.supabase import supabase

security_scheme = HTTPBearer()


# 1. VERIFY JWT TOKEN
def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security_scheme)):
    token = credentials.credentials

    try:
        user_response = supabase.auth.get_user(token)
        if not user_response or not user_response.user:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid or expired token"
            )
        
        return user_response.user

    except Exception as e:
        print(f"Auth token validation error: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Could not validate credentials"
        )


# 2. RBAC GUARD CLASS
class RoleChecker:
    def __init__(self, allowed_roles: list[str]):
        self.allowed_roles = allowed_roles

    def __call__(self, current_user = Depends(get_current_user)):
        try:
            # Gamitin ang maybe_single() para iwas crash kapag walang nahanap
            profile = (
                supabase.table('profiles')
                .select('role')
                .eq('id', current_user.id)
                .maybe_single()
                .execute()
            )
        except Exception as err:
            print(f"RBAC DB query failed: {str(err)}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Error checking user permissions"
            )

        # I-check kung may nahanap na profile data
        if not profile or not profile.data:
            print(f"RBAC Error: No profile found for user_id -> {current_user.id}")
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="User profile not found"
            )

        user_role = profile.data.get("role")
        print(f"RBAC Check: User Role is '{user_role}', Allowed Roles are {self.allowed_roles}")

        # Verify permission
        if user_role not in self.allowed_roles:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="You don't have permission to access this resource."
            )

        return current_user