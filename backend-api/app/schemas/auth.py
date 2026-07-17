from pydantic import BaseModel, EmailStr, Field
from typing import Optional

# Schema  para sa register (optinal lang muna to)
class UserRegister(BaseModel):
    email: EmailStr
    password: str = Field(..., min_length=8, description="Password must be at least 8 characters log")
    first_name: str = Field(..., min_length=1, description="Firstname is required")
    last_name: str = Field(..., min_length=1, description="Lastname is required")


# 1. Schema para sa login
class UserLogin(BaseModel):
    email: EmailStr
    password: str


# 2. Schema para sa success response sa login
class TokenResponse(BaseModel):
    access_token: str
    refresh_token: str # session refresh sa PHP
    token_type: str = "bearer"
    user_id: str
    email: str
    role: str

# 3. Schema para sa pag balik ng profile data sa User
class UserProfile(BaseModel):
    id: str
    email: str
    first_name: str
    last_name: str
    role: str

    
# additonal
class LoginResponse(BaseModel):
    status: str
    message: str
    email: str
