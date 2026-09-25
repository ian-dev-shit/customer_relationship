import pytest
from fastapi.testclient import TestClient
from unittest.mock import patch, MagicMock
from app.main import app  
import sys

sys.modules["weasyprint"] = MagicMock()

client = TestClient(app)

# ==========================================
# TEST: GET /api/v1/admin/analytics/cards
# ==========================================

@patch("app.routes.admin.analytics.supabase_secondary")
@patch("app.routes.admin.analytics.get_admin_dashboard_kpis")
def test_fetch_admin_cards_success(mock_get_kpis, mock_supabase):
    # Mock Supabase response
    mock_execute = MagicMock()
    mock_execute.data = [{"id": 1, "title": "Test Inquiry"}]
    mock_supabase.table.return_value.select.return_value.execute.return_value = mock_execute

    # Mock service function output
    mock_get_kpis.return_value = {"total_inquiries": 1, "pending": 1}

    response = client.get("/api/v1/admin/analytics/cards")

    assert response.status_code == 200
    assert response.json() == {"total_inquiries": 1, "pending": 1}
    mock_supabase.table.assert_called_once_with("inquiries")
    mock_get_kpis.assert_called_once_with([{"id": 1, "title": "Test Inquiry"}])


# ==========================================
# TEST: GET /api/v1/admin/analytics
# ==========================================

@patch("app.routes.admin.analytics.get_kanban_board_data")
def test_fetch_kanban_board_success(mock_get_kanban):
    mock_get_kanban.return_value = {"todo": [], "in_progress": []}

    response = client.get("/api/v1/admin/analytics")

    assert response.status_code == 200
    assert response.json() == {
        "status": "success",
        "data": {"todo": [], "in_progress": []}
    }

@patch("app.routes.admin.analytics.get_kanban_board_data")
def test_fetch_kanban_board_error(mock_get_kanban):
    # Simulates internal server error
    mock_get_kanban.side_effect = Exception("Database error")

    response = client.get("/api/v1/admin/analytics")

    assert response.status_code == 500
    assert "Failed to fetch kanban board: Database error" in response.json()["detail"]


# ==========================================
# TEST: PATCH /api/v1/admin/analytics/move
# ==========================================

@patch("app.routes.admin.analytics.update_card_status")
def test_move_kanban_card_success(mock_update_status):
    mock_update_status.return_value = True

    # Gumamit ng string para sa card_id ayon sa MoveCardRequest schema
    payload = {"card_id": "101", "new_status": "done"}
    response = client.patch("/api/v1/admin/analytics/move", json=payload)

    assert response.status_code == 200
    assert response.json() == {
        "status": "success",
        "message": "Inquiry status updated successfully"
    }
    mock_update_status.assert_called_once_with("101", "done")


@patch("app.routes.admin.analytics.update_card_status")
def test_move_kanban_card_failure(mock_update_status):
    mock_update_status.return_value = False

    # String pa rin ang card_id
    payload = {"card_id": "999", "new_status": "invalid_status"}
    response = client.patch("/api/v1/admin/analytics/move", json=payload)

    assert response.status_code == 400
    assert response.json()["detail"] == "Failed to update inquiry status"