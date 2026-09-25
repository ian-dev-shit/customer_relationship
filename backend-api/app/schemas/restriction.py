from pydantic import BaseModel, EmailStr
from typing import Optional, List
from datetime import datetime

# Schema para sa pag-display ng Restricted User Details
class RestrictedUserResponse(BaseModel):
    id: Optional[int] = None
    email: EmailStr
    failed_attempts: int
    is_restricted: bool
    last_attempt_at: Optional[datetime] = None
    restricted_until: Optional[datetime] = None
    created_at: Optional[datetime] = None

    class Config:
        from_attributes = True

# Schema Response para sa Unrestrict / Reset action
class UnrestrictResponse(BaseModel):
    status: str
    message: str
    email: EmailStr