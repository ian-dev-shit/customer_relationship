from pydantic import BaseModel, EmailStr
from typing import Optional, List
from datetime import datetime

# Schema para sa pag list ng close won tickets
class CloseWonTicketResponseSchema(BaseModel):
    id: str
    inquiry_id: Optional[str] = None
    company_name: Optional[str] = None
    contact_person: Optional[str] = None
    email: EmailStr
    phone_number: Optional[str] = None
    agreed_amount: Optional[float] = 0.0
    created_at: Optional[str] = None
    customer_id: Optional[str] = None       
    ticket_status: Optional[str] = "for account" # Default status: 'for account' o 'created'
    pickup_datetime: Optional[str] = None
    pickup_address: Optional[str] = None

    class Config:
        from_attributes = True

# Schema para sa Admin account creation form
class CreateCustomerFromTicketSchema(BaseModel):
    ticket_id: str  # Id ng closed won ticket table
    email: EmailStr # Pre-filled email address ng customer
    password: str
    first_name: str
    last_name: str
    company_name: Optional[str] = None
    phone_number: Optional[str] = None

class CustomerUserResponse(BaseModel):
    id: str
    email: str
    first_name: Optional[str] = None
    last_name: Optional[str] = None
    company_name: Optional[str] = None
    phone_number: Optional[str] = None
    created_at: Optional[str] = None

    class Config:
        from_attributes = True