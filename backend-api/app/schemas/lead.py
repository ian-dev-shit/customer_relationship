from pydantic import BaseModel, EmailStr
from typing import Optional, Any, List, Literal
from datetime import datetime

# Cleaned up Sales Stage Status Enum
StatusType = Literal[
    "new_inquiry",
    "qualifying",
    "quote_sent",
    "negotiation",
    "closed_won",
    "closed_lost"
]

class StatusUpdateSchema(BaseModel):
    status: StatusType
    estimated_amount: Optional[float] = None

    # Dagdag 
    cargo_details: Optional[str] = None

    # Dagdag: Pick-up deatials
    pickup_address: Optional[str] = None
    pickup_datetime: Optional[datetime] = None

    # Dagdag: Sales agent info
    agent_id: Optional[str] = None
    agent_name: Optional[str] = None
    agent_email: Optional[str] = None

class LeadResponseSchema(BaseModel):
    id: str
    inquiry_code: Optional[str] = None
    created_at: datetime
    
    company_name: Optional[str] = None
    contact_person: Optional[str] = None
    email: Optional[str] = None
    phone_number: Optional[str] = None
    
    platform_used: Optional[str] = None
    service_type: Optional[str] = None
    origin: Optional[str] = None
    destination: Optional[str] = None
    cargo_details: Optional[Any] = None
    initial_inquiry_text: Optional[str] = None
    
    status: Optional[str] = "new_inquiry"
    estimated_amount: Optional[float] = None
    
    assigned_agent_id: Optional[str] = None
    assigned_agent_name: Optional[str] = None
    assigned_agent_email: Optional[str] = None

    pickup_address: Optional[str] = None
    pickup_datetime: Optional[datetime] = None

    class Config:
        from_attributes = True

class PaginatedLeadResponseSchema(BaseModel):
    total: int
    data: List[LeadResponseSchema]

class LeadStatsResponseSchema(BaseModel):
    all: int
    new_inquiry: int = 0
    qualifying: int = 0
    quote_sent: int = 0
    negotiation: int = 0
    closed_won: int = 0
    closed_lost: int = 0


class LeadCreateSchema(BaseModel):
    company_name: str
    contact_person: str
    email: EmailStr
    phone_number: str
    service_type: str
    origin: str
    destination: str

    # Default Value
    platform_used: Optional[str] = "Manual Entry"
    status: Optional[str] = "new_inquiry"