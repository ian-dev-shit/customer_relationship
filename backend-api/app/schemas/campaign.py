from pydantic import BaseModel
from typing import Optional
from datetime import datetime

class CampaignCreate(BaseModel):
    title: str
    description: Optional[str] = None
    is_permanent: bool = False
    start_date: Optional[datetime] = None
    end_date: Optional[datetime] = None