from unittest.mock import MagicMock, patch
from fastapi import FastAPI
from fastapi.testclient import TestClient
from app.routes.leads import router

app = FastAPI()
app.include_router(router)

client = TestClient(app)

# Dummy item na sumusunod nang eksakto sa LeadResponseSchema
MOCK_LEAD = {
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "inquiry_code": "INQ-001",
    "created_at": "2026-08-15T00:00:00Z",  
    "company_name": "ABC Corp",
    "contact_person": "Juan Dela Cruz",
    "email": "juan@abc.com",
    "status": "new_inquiry",
}


# ==========================================
# 1. TEST: GET /api/v1/leads/
# ==========================================


@patch("app.routes.leads.supabase_secondary")
def test_get_leads_success(mock_supabase):
    mock_data = [MOCK_LEAD, {**MOCK_LEAD, "id": "123e4567-e89b-12d3-a456-426614174001", "status": "qualifying"}]

    mock_execute = MagicMock()
    mock_execute.data = mock_data
    mock_execute.count = 2

    # Mock method chain: .table().select().order().range().execute()
    (
        mock_supabase.table.return_value.select.return_value.order.return_value.range.return_value.execute.return_value
    ) = mock_execute

    response = client.get("/api/v1/leads/")

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["total"] == 2
    assert len(res_json["data"]) == 2


@patch("app.routes.leads.supabase_secondary")
def test_get_leads_with_status_and_search_filter(mock_supabase):
    mock_data = [{**MOCK_LEAD, "status": "qualifying"}]

    mock_execute = MagicMock()
    mock_execute.data = mock_data
    mock_execute.count = 1

    # Mock method chain kapag may .eq() at .or_()
    (
        mock_supabase.table.return_value.select.return_value.eq.return_value.or_.return_value.order.return_value.range.return_value.execute.return_value
    ) = mock_execute

    response = client.get(
        "/api/v1/leads/?status=qualifying&search=ABC&page=1&limit=10"
    )

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["total"] == 1
    assert res_json["data"][0]["company_name"] == "ABC Corp"


@patch("app.routes.leads.supabase_secondary")
def test_get_leads_server_error(mock_supabase):
    mock_supabase.table.side_effect = Exception("Database connection error")

    response = client.get("/api/v1/leads/")

    assert response.status_code == 500
    assert "Database connection error" in response.json()["detail"]


# ==========================================
# 2. TEST: GET /api/v1/leads/stats
# ==========================================


@patch("app.routes.leads.supabase_secondary")
def test_get_lead_stats_success(mock_supabase):
    mock_data = [
        {"status": "new_inquiry"},
        {"status": "new_inquiry"},
        {"status": "qualifying"},
        {"status": "quote_sent"},
        {"status": "closed_won"},
    ]

    mock_execute = MagicMock()
    mock_execute.data = mock_data

    mock_supabase.table.return_value.select.return_value.execute.return_value = (
        mock_execute
    )

    response = client.get("/api/v1/leads/stats")

    assert response.status_code == 200
    stats = response.json()
    assert stats["all"] == 5
    assert stats["new_inquiry"] == 2
    assert stats["qualifying"] == 1
    assert stats["quote_sent"] == 1
    assert stats["negotiation"] == 0
    assert stats["closed_won"] == 1
    assert stats["closed_lost"] == 0


@patch("app.routes.leads.supabase_secondary")
def test_get_lead_stats_error(mock_supabase):
    mock_supabase.table.side_effect = Exception("Stats fetch failed")

    response = client.get("/api/v1/leads/stats")

    assert response.status_code == 500
    assert "Stats fetch failed" in response.json()["detail"]


# ==========================================
# 3. TEST: PATCH /api/v1/leads/{lead_id}/status
# ==========================================


@patch("app.routes.leads.supabase_secondary")
def test_update_lead_status_success(mock_supabase):
    updated_lead = {**MOCK_LEAD, "status": "quote_sent"}

    mock_execute = MagicMock()
    mock_execute.data = [updated_lead]

    (
        mock_supabase.table.return_value.update.return_value.eq.return_value.execute.return_value
    ) = mock_execute

    response = client.patch(
        "/api/v1/leads/123e4567-e89b-12d3-a456-426614174000/status",
        json={"status": "quote_sent"},
    )

    assert response.status_code == 200
    assert response.json()["status"] == "quote_sent"


@patch("app.routes.leads.supabase_secondary")
def test_update_lead_status_invalid_enum(mock_supabase):
    # Test validation error kapag nag-pasa ng maling StatusType
    response = client.patch(
        "/api/v1/leads/123e4567-e89b-12d3-a456-426614174000/status",
        json={"status": "invalid_status_enum"},
    )

    # Dapat mag-throw ng 422 Unprocessable Entity dahil sa Pydantic validation
    assert response.status_code == 422


@patch("app.routes.leads.supabase_secondary")
def test_update_lead_status_not_found(mock_supabase):
    mock_execute = MagicMock()
    mock_execute.data = []

    (
        mock_supabase.table.return_value.update.return_value.eq.return_value.execute.return_value
    ) = mock_execute

    response = client.patch(
        "/api/v1/leads/non-existing-id/status", json={"status": "closed_won"}
    )

    assert response.status_code == 404
    assert response.json()["detail"] == "Lead ID not found."


@patch("app.routes.leads.supabase_secondary")
def test_update_lead_status_server_error(mock_supabase):
    mock_supabase.table.side_effect = Exception("Update query failed")

    response = client.patch(
        "/api/v1/leads/123e4567-e89b-12d3-a456-426614174000/status",
        json={"status": "closed_lost"},
    )

    assert response.status_code == 500
    assert "Update query failed" in response.json()["detail"]