from pydantic import BaseModel, Field
from typing import Optional, List
from datetime import datetime
from uuid import UUID

# 1. Customer Nested Data (para sa Join Response)
class CustomerInfo(BaseModel):
    company_name: Optional[str] = None
    contact_person: Optional[str] = None
    email: Optional[str] = None
    phone_number: Optional[str] = None
    tier: Optional[str] = "BRONZE"

    class Config:
        from_attributes = True


# 2. Base Booking Model
class BookingBase(BaseModel):
    service_type: Optional[str] = None
    origin: Optional[str] = None
    destination: Optional[str] = None
    pickup_address: Optional[str] = None
    pickup_datetime: Optional[datetime] = None
    cargo_details: Optional[str] = None
    agreed_amount: Optional[float] = 0.0
    booking_status: Optional[str] = "pending"


# 3. Request Payload para sa pag-create ng Booking (mula sa Chat/Sales)
class BookingCreate(BookingBase):
    customer_id: UUID
    ticket_id: Optional[UUID] = None
    assigned_agent_id: Optional[UUID] = None


# 4. Request Payload para sa Update Status o Price ni Sales
class BookingUpdate(BaseModel):
    agreed_amount: Optional[float] = None
    booking_status: Optional[str] = None
    assigned_agent_id: Optional[UUID] = None


# 5. Full Single Booking Response Schema (kasama ang Joined Customer)
class BookingResponse(BookingBase):
    id: UUID
    booking_code: Optional[str] = None
    customer_id: Optional[UUID] = None
    ticket_id: Optional[UUID] = None
    assigned_agent_id: Optional[UUID] = None
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None
    
    # Joined Customer Object mula sa Supabase select("*, customers(...)")
    customers: Optional[CustomerInfo] = None

    class Config:
        from_attributes = True


# 6. Pagination Metadata Schema
class PaginationMeta(BaseModel):
    total: int
    page: int
    limit: int
    total_pages: int


# 7. Paginated Response Wrapper
class PaginatedBookingResponse(BaseModel):
    status: str = "success"
    data: List[BookingResponse]
    meta: PaginationMeta