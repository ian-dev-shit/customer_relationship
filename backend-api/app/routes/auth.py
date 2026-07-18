# Auth Endpoint

from fastapi import APIRouter, HTTPException, status
from app.schemas.auth import UserLogin, UserRegister, TokenResponse, LoginResponse
from app.supabase.supabase import supabase, redis_client
import random
import json
from app.middleware.helper import send_otp_email


auth_router = APIRouter(
    prefix="/api/auth",
    tags=["Authentication"]
)

# 1. Sign up endpoint Optional 
@auth_router.post("/signup", status_code=status.HTTP_201_CREATED)
def signup(user_data: UserRegister):

    try:
        # I-register ang user  sa supabase auth
        response = supabase.auth.sign_up({
            "email": user_data.email,
            "password": user_data.password,
            "option": {
                "data": {
                    "first_name": user_data.first_name,
                    "last_name": user_data.last_name
                }
            }
        })

        # Kapag successful gagana automatic ang database trigger
        return{
            "message": "Registration successful!",
            "user_id": response.user.id
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=f"Registration failed: {str(e)}"
        )
    
# 2. Login endpoint
@auth_router.post("/login", response_model=LoginResponse)
def login(user_data: UserLogin):
    try:
        #Login ang user gamit ang email at password
        auth_response = supabase.auth.sign_in_with_password({
            "email": user_data.email,
            "password": user_data.password
        })

        user_id = auth_response.user.id

        # Kunin ang role ng user sa profile table
        profile_response = supabase.table("profiles").select("role").eq("id", user_id).maybe_single().execute()

        # Ligtas na pagkuha ng role
        if profile_response and hasattr(profile_response, 'data') and profile_response.data:
            user_role = profile_response.data.get("role", "customer")
        else:
            user_role = "customer"

        otp_code = f"{random.randint(100000, 999999)}"

        temp_session = {
            "otp": otp_code,
            "access_token": auth_response.session.access_token,
            "refresh_token": auth_response.session.refresh_token,
            "user_id": user_id,
            "email": auth_response.user.email,
            "role": user_role
        }

        # Ipasok sa redis na 5 minutes limmit ang code
        redis_key =f"pre_auth:{user_data.email}"
        redis_client.setex(redis_key, 300, json.dumps(temp_session))

        # Sending to email
        send_otp_email(user_data.email, otp_code)

        # For checking lang
        print(f"Email Sent to {user_data.email} with OTP: {otp_code}")

        # Soon sa totong email na send ang code
        return {"status":"otp_sent", "message": "OTP has been sent to your registered channel.", "email": user_data.email}

    
    except Exception as e:
        print(f"Login error detail: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Wrong email or password"
        )
    

# Verification OTP
@auth_router.post("/login-verify", response_model=TokenResponse)
def login_verify(email: str, otp_code: str):
    redis_key = f"pre_auth:{email}"

    # 1. Kunin ang pansamantalang session sa redis
    cached_data = redis_client.get(redis_key)

    if not cached_data:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Expired na o walang nahanap na OTP request para sa email na ito."
        )
    

    if isinstance(cached_data, str):
        session_data = json.loads(cached_data)
    else:
        session_data = cached_data
    # ---------------------------

    # 2. I-verify kung tugma ang OTP na in-input ng user
    if str(session_data.get("otp")) != str(otp_code):
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Wrong OTP code"
        )
    
    # 3. Kung tama ang OTP, burahin na ito sa redis
    redis_client.delete(redis_key)

    # 4. Ibalik sa supabase token sa PHP frontend
    return {
        "access_token": session_data["access_token"],
        "refresh_token": session_data["refresh_token"],
        "token_type": "bearer",
        "user_id": session_data["user_id"],
        "email": session_data["email"],
        "role": session_data["role"]
    }