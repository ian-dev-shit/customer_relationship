from fastapi import APIRouter, HTTPException, UploadFile, File, Form
from typing import Optional, Dict, Any, List
from app.service.campaign_service import CampaignService

router = APIRouter(prefix="/api/v1/campaigns", tags=["Campaigns & Events"])

# POST: Para sa pag-upload ng Sales Agent
@router.post("/create")
async def create_campaign_post(
    title: str = Form(...),
    description: Optional[str] = Form(None),
    is_permanent: bool = Form(False),
    start_date: Optional[str] = Form(None),
    end_date: Optional[str] = Form(None),
    agent_id: Optional[str] = Form(None),
    image: UploadFile = File(...)
) -> Dict[str, Any]:
    try:
        return await CampaignService.create_campaign(
            title=title,
            description=description,
            is_permanent=is_permanent,
            start_date=start_date,
            end_date=end_date,
            image_file=image,
            agent_id=agent_id
        )
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# GET: Para sa pagpapakita sa Customer Dashboard
@router.get("/active-posts")
def get_active_posts() -> List[Dict[str, Any]]:
    try:
        return CampaignService.get_active_campaigns_for_customer()
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))