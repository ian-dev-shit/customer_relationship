from unittest.mock import AsyncMock, MagicMock, patch
import io
import pytest
from fastapi import FastAPI
from fastapi.testclient import TestClient
from app.routes.sales_agent.campaign_router import router

app = FastAPI()
app.include_router(router)

client = TestClient(app)


# ============================================================================
# 1. TEST: POST /api/v1/campaigns/create
# ============================================================================

@patch("app.routes.sales_agent.campaign_router.CampaignService")
def test_create_campaign_post_success(mock_campaign_service):
    # Setup AsyncMock dahil async method ang create_campaign sa service
    mock_campaign_service.create_campaign = AsyncMock(
        return_value={
            "status": "success",
            "message": "Campaign created successfully",
            "campaign_id": "cmp-123",
        }
    )

    # I-simulate ang file upload (multipart/form-data)
    file_payload = {
        "image": ("test_banner.png", io.BytesIO(b"fake image content"), "image/png")
    }

    form_data = {
        "title": "Summer Promo 2026",
        "description": "Get 20% off on all sea freight shipments",
        "is_permanent": "false",
        "start_date": "2026-06-01",
        "end_date": "2026-06-30",
        "agent_id": "agent-001",
    }

    response = client.post(
        "/api/v1/campaigns/create", data=form_data, files=file_payload
    )

    assert response.status_code == 200
    res = response.json()
    assert res["status"] == "success"
    assert res["campaign_id"] == "cmp-123"

    # Verifying na natawag ang CampaignService kasama ang expected parameters
    mock_campaign_service.create_campaign.assert_called_once()


def test_create_campaign_post_missing_required_fields():
    # Sinusubukan mag-send nang walang 'title' at 'image'
    response = client.post("/api/v1/campaigns/create", data={})

    # HTTP 422 Unprocessable Entity 
    assert response.status_code == 422


@patch("app.routes.sales_agent.campaign_router.CampaignService")
def test_create_campaign_post_exception(mock_campaign_service):
    # Simulate internal service error
    mock_campaign_service.create_campaign = AsyncMock(
        side_effect=Exception("S3 Storage upload failed")
    )

    file_payload = {
        "image": ("test_banner.png", io.BytesIO(b"fake image content"), "image/png")
    }

    form_data = {"title": "Error Test Promo"}

    response = client.post(
        "/api/v1/campaigns/create", data=form_data, files=file_payload
    )

    assert response.status_code == 500
    assert response.json()["detail"] == "S3 Storage upload failed"


# ============================================================================
# 2. TEST: GET /api/v1/campaigns/active-posts
# ============================================================================

@patch("app.routes.sales_agent.campaign_router.CampaignService")
def test_get_active_posts_success(mock_campaign_service):
    mock_campaigns = [
        {
            "id": "cmp-100",
            "title": "Year-End Discount",
            "image_url": "https://storage.com/banners/yearend.png",
            "is_permanent": False,
        },
        {
            "id": "cmp-101",
            "title": "Always Active Support",
            "image_url": "https://storage.com/banners/support.png",
            "is_permanent": True,
        },
    ]

    # MagicMock dahil synchronous method ito
    mock_campaign_service.get_active_campaigns_for_customer = MagicMock(
        return_value=mock_campaigns
    )

    response = client.get("/api/v1/campaigns/active-posts")

    assert response.status_code == 200
    res = response.json()
    assert isinstance(res, list)
    assert len(res) == 2
    assert res[0]["title"] == "Year-End Discount"


@patch("app.routes.sales_agent.campaign_router.CampaignService")
def test_get_active_posts_empty(mock_campaign_service):
    mock_campaign_service.get_active_campaigns_for_customer = MagicMock(
        return_value=[]
    )

    response = client.get("/api/v1/campaigns/active-posts")

    assert response.status_code == 200
    assert response.json() == []


@patch("app.routes.sales_agent.campaign_router.CampaignService")
def test_get_active_posts_exception(mock_campaign_service):
    mock_campaign_service.get_active_campaigns_for_customer = MagicMock(
        side_effect=Exception("Failed to fetch campaigns")
    )

    response = client.get("/api/v1/campaigns/active-posts")

    assert response.status_code == 500
    assert response.json()["detail"] == "Failed to fetch campaigns"