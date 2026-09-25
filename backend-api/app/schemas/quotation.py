

from pydantic import BaseModel, EmailStr, Field, ConfigDict
from typing import List, Optional
from datetime import datetime
from uuid import UUID

class QuotationItemCreate(BaseModel):
    description: str = Field(..., example="20ft FCL Sea Freight Charge")
    quantity: float = Field(default=1.0, gt=0, example=1)
    unit_price: float = Field(..., ge=0, example=45000.00)

class QuotationCreate(BaseModel):
    inquiry_id: UUID
    customer_name: str
    customer_email: EmailStr
    company_name: Optional[str] = None
    origin: str
    destination: str
    service_type: Optional[str] = None
    tax_amount: float = Field(default=0.0, ge=0)
    discount_amount: float = Field(default=0.0, ge=0)
    valid_until: Optional[datetime] = None
    created_by: Optional[UUID] = None  # ID ng Sales Agent
    items: List[QuotationItemCreate]

class QuotationResponse(BaseModel):
    id: UUID
    inquiry_id: UUID
    quote_number: str
    subtotal: float
    tax_amount: float
    discount_amount: float
    total_amount: float
    status: str
    pdf_url: Optional[str] = None
    created_at: datetime

    class Config:
        from_attributes = True

# Base Model para sa Quotation Details
class QuotationItemSchema(BaseModel):
    description: str
    quantity: int
    unit_price: float
    total_price: float

# Response Model para sa List Table View
class QuotationListResponse(BaseModel):
    id: str
    quotation_number: str
    inquiry_code: Optional[str] = None
    company_name: str
    contact_person: str
    email: str
    service_type: str
    total_amount: float
    issued_date: datetime
    valid_until: datetime
    is_valid: bool
    status: str  # e.g., 'SENT', 'ACCEPTED', 'EXPIRED', 'REJECTED'
    pdf_file_name: Optional[str] = None

    model_config = ConfigDict(from_attributes=True)

# Paginated Response Wrapper
class QuotationPaginatedResponse(BaseModel):
    data: List[QuotationListResponse]
    total: int
    page: int
    limit: int
    total_pages: int