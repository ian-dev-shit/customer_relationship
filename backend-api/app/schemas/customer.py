from pydantic import BaseModel, EmailStr, ConfigDict
from typing import Optional, List, Generic, TypeVar, Any
from datetime import datetime
from uuid import UUID


DataType = TypeVar("DataType")

# Request model kapag nag create ng customer mula sa tickets
class CustomerCreate(BaseModel):
    ticket_id: UUID
    company_name: str
    contact_person: str
    email: EmailStr
    phone_number: Optional[str] = None


# Response Model pag nag fetch ng customer data
class CustomerResponse(BaseModel):
    id: UUID
    company_name: str
    contact_person: str
    email: EmailStr
    phone_number: Optional[str] = None
    total_bookings: int
    tier: str
    created_by_ticket_id: Optional[UUID] = None
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True

# Pagination
class PaginationMeta(BaseModel):
    current_page: int
    total_pages: int
    total_items: int
    per_page: int

# Generic Paginated Response Schema
class PaginatedResponse(BaseModel, Generic[DataType]):
    items: List[DataType]
    pagination: PaginationMeta

# Booking Process schema
class BookingCreate(BaseModel):
    customer_id: Optional[str] = None  
    driver_id: Optional[str] = None
    assigned_agent_id: Optional[str] = None
    service_type: str
    origin: str
    destination: str
    pickup_datetime: datetime
    cargo_details: Optional[str] = None
    agreed_amount: float
    booking_status: Optional[str] = "New Booking"

class BookingResponse(BookingCreate):
    id: str
    booking_code: str
    created_at: datetime

    # Inupdate para sa Pydantic V2 (nawala rin ang deprecation warning)
    model_config = ConfigDict(from_attributes=True)

# backlog remove soon
class ChatRequest(BaseModel):
    customer_id: str
    message: str

# Sintement Define json schema
class ExtractedDetails(BaseModel):
    origin: Optional[str] = None
    destination: Optional[str] = None
    service_type: Optional[str] = None
    cargo_details: Optional[str] = None

class AIAnalysisResult(BaseModel):
    reply: str
    sentiment: str # "positive", "neutral", "negative"
    updated_details: ExtractedDetails
    is_complete: bool
    force_handoff: bool