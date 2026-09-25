# Auth Endpoint

from fastapi import APIRouter, HTTPException, status
from app.schemas.auth import UserLogin, UserRegister, TokenResponse, LoginResponse
from app.supabase_config.supabase import supabase, redis_client, supabase_secondary
import random
import json
from datetime import datetime, timezone, timedelta
from app.middleware.helper import send_otp_email
from app.service.audit_service import log_audit


auth_router = APIRouter(
    prefix="/api/auth",
    tags=["Authentication"]
)

# Helper para malaman kung Customer ang nag-te-test mag-login
def is_customer_account(email: str) -> bool:
    try:
        res = supabase_secondary.table("users").select("id").eq("email", email).execute()
        if res.data and len(res.data) > 0:
            return True
    except Exception as e:
        print(f"DEBUG: Check customer account error: {e}")
    return False


# Helper function para sa pag-record ng failed attempt sa Customer-Relationship DB
def record_failed_attempt(email: str):
    try:
        attempt_res = supabase_secondary.table("restrict_attempt") \
            .select("*") \
            .eq("email", email) \
            .maybe_single() \
            .execute()

        attempt_data = attempt_res.data if attempt_res else None
        current_attempts = (attempt_data.get("failed_attempts") if attempt_data else 0) + 1
        is_now_restricted = current_attempts >= 5

        # Lock ng 30 mins kapag umabot ng 5 attempts
        lock_time = (datetime.now(timezone.utc) + timedelta(minutes=30)).isoformat() if is_now_restricted else None

        if attempt_data:
            supabase_secondary.table("restrict_attempt").update({
                "failed_attempts": current_attempts,
                "is_restricted": is_now_restricted,
                "restricted_until": lock_time,
                "last_attempt_at": datetime.now(timezone.utc).isoformat()
            }).eq("email", email).execute()
        else:
            supabase_secondary.table("restrict_attempt").insert({
                "email": email,
                "failed_attempts": current_attempts,
                "is_restricted": is_now_restricted,
                "restricted_until": lock_time
            }).execute()

        return current_attempts, is_now_restricted
    except Exception as e:
        print(f"DEBUG: Error recording failed attempt: {e}")
        return 0, False


# Helper function para i-reset ang failed attempts kapag successful login
def reset_failed_attempts(email: str):
    try:
        supabase_secondary.table("restrict_attempt").update({
            "failed_attempts": 0,
            "is_restricted": False,
            "restricted_until": None
        }).eq("email", email).execute()
    except Exception as e:
        print(f"DEBUG: Error resetting attempts: {e}")


# 2. Login endpoint 
@auth_router.post("/login", response_model=LoginResponse)
def login(user_data: UserLogin):
    email = user_data.email.strip().lower()

    # ==================== STEP 0: CHECK RESTRICTION STATUS ====================
    try:
        attempt_res = supabase_secondary.table("restrict_attempt") \
            .select("*") \
            .eq("email", email) \
            .maybe_single() \
            .execute()

        attempt_data = attempt_res.data if attempt_res else None

        if attempt_data and attempt_data.get("is_restricted"):
            restricted_until = attempt_data.get("restricted_until")
            if restricted_until:
                until_dt = datetime.fromisoformat(restricted_until.replace("Z", "+00:00"))
                if datetime.now(timezone.utc) < until_dt:
                    raise HTTPException(
                        status_code=status.HTTP_403_FORBIDDEN,
                        detail="Account temporarily restricted due to 5 failed login attempts. Please try again later."
                    )
                else:
                    reset_failed_attempts(email)
            else:
                raise HTTPException(
                    status_code=status.HTTP_403_FORBIDDEN,
                    detail="Account is restricted due to too many failed login attempts."
                )
    except HTTPException as http_ex:
        raise http_ex
    except Exception as e:
        print(f"DEBUG: Restriction pre-check error: {e}")
    # =========================================================================

    auth_response = None
    target_supabase = None
    is_secondary = False

    # 1. Subukang i-login sa Primary Supabase (Admin / Sales / Super Admin)
    try:
        auth_response = supabase.auth.sign_in_with_password({
            "email": email,
            "password": user_data.password
        })
        target_supabase = supabase
    except Exception as primary_err:
        print(f"DEBUG: Primary Auth failed: {primary_err}")

        # 2. Subukang i-login sa Secondary Supabase (Customer Portal)
        try:
            auth_response = supabase_secondary.auth.sign_in_with_password({
                "email": email,
                "password": user_data.password
            })
            target_supabase = supabase_secondary
            is_secondary = True
        except Exception as secondary_err:
            print(f"DEBUG: Secondary Auth also failed: {secondary_err}")

            # KAPAG KAPWA NAG-FAIL AT HINDI CUSTOMER ACCOUNT = STAFF ATTEMPT FAILED!
            if not is_customer_account(email):
                attempts, is_restricted = record_failed_attempt(email)
                
                if is_restricted:
                    raise HTTPException(
                        status_code=status.HTTP_403_FORBIDDEN,
                        detail="Maximum failed login attempts (5) reached. Account is now restricted."
                    )
                
                remaining = 5 - attempts
                raise HTTPException(
                    status_code=status.HTTP_401_UNAUTHORIZED,
                    detail=f"Invalid credentials. {remaining} attempt(s) left before account restriction."
                )

            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid email or password"
            )

    # Siguraduhing may valid session
    if not auth_response or not auth_response.session:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid credentials"
        )

    user_id = auth_response.user.id
    access_token = auth_response.session.access_token

    # 3. Fetch Role mula sa DB Table
    user_role = "customer" if is_secondary else "sales"

    try:
        if is_secondary:
            profile_response = target_supabase.table("users").select("role").eq("id", user_id).execute()
        else:
            profile_response = target_supabase.table("profiles").select("role").eq("id", user_id).execute()

        if profile_response.data and len(profile_response.data) > 0:
            user_role = profile_response.data[0].get("role", user_role)
    except Exception as profile_err:
        print(f"DEBUG: Error fetching profile role: {str(profile_err)}")

    # Clear/Reset attempts kapag Staff at matagumpay na nakapag-login
    if not is_secondary:
        reset_failed_attempts(email)



    # 4. Generate OTP
    otp_code = f"{random.randint(100000, 999999)}"

    temp_session = {
        "otp": otp_code,
        "access_token": access_token,
        "refresh_token": auth_response.session.refresh_token,
        "user_id": user_id,
        "email": auth_response.user.email,
        "role": user_role
    }

    # 5. Store session in Redis
    redis_key = f"pre_auth:{email}"
    redis_client.setex(redis_key, 120, json.dumps(temp_session))

    # 6. Send OTP Email
    send_otp_email(email, otp_code)

    # ==================== DEBUG PRINTS  ====================

    print(f"DEBUG: Login target project -> {'Secondary (Customer)' if is_secondary else 'Primary (Admin/Sales)'}")

    print(f"DEBUG: Found profile role -> {user_role}")

    print(f"Email Sent to {user_data.email} with OTP: {otp_code}")

    return {
        "status": "otp_sent", 
        "message": "OTP has been sent to your registered channel.", 
        "email": email
    }

# Verification OTP
@auth_router.post("/verify-otp", response_model=TokenResponse)
def login_verify(email: str, otp_code: str):
    redis_key = f"pre_auth:{email}"

    # 1. Kunin ang pansamantalang session sa redis
    cached_data = redis_client.get(redis_key)

    if not cached_data:
        # AUDIT TRAIL: Expired o walang OTP Session
        log_audit(
            user_email=email,
            user_role="unknown",
            action="LOGIN_FAILED",
            module="AUTH",
            details="Failed OTP verification: Expired or missing session in Redis."
        )
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Expired na o walang nahanap na OTP request para sa email na ito."
        )
    
    if isinstance(cached_data, str):
        session_data = json.loads(cached_data)
    else:
        session_data = cached_data

    # 2. I-verify kung tugma ang OTP na in-input ng user
    if str(session_data.get("otp")) != str(otp_code):
        log_audit(
            user_email=email,
            user_role=session_data.get("role", "unknown"),
            action="LOGIN_FAILED",
            module="AUTH",
            details="Failed OTP verification: Invalid OTP code provided.",
            user_id=session_data.get("user_id")
        )
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Wrong OTP code"
        )
    
    # 3. Kung tama ang OTP, burahin na ito sa redis
    redis_client.delete(redis_key)

    log_audit(
        user_email=session_data["email"],
        user_role=session_data["role"],
        action="LOGIN_SUCCESS",
        module="AUTH",
        details=f"User successfully verified OTP and logged in as {session_data['role']}.",
        user_id=session_data["user_id"]
    )

    # 4. Ibalik sa supabase token sa PHP frontend
    return {
        "access_token": session_data["access_token"],
        "refresh_token": session_data["refresh_token"],
        "token_type": "bearer",
        "user_id": session_data["user_id"],
        "email": session_data["email"],
        "role": session_data["role"]
    }