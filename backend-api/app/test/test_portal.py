from unittest.mock import MagicMock, patch
from fastapi import FastAPI
from fastapi.testclient import TestClient

from app.routes.portal import router

app = FastAPI()
app.include_router(router)

client = TestClient(app)


# --- 1. TEST: Missing/Empty Header (400 Bad Request) ---
def test_get_profile_missing_header():
    response = client.get("/api/v1/portal/profile")

    assert response.status_code == 400
    assert response.json()["detail"] == "Header 'x-user-id' is missing or empty."


def test_get_profile_empty_header():
    response = client.get(
        "/api/v1/portal/profile", headers={"x-user-id": "   "}
    )

    assert response.status_code == 400
    assert response.json()["detail"] == "Header 'x-user-id' is missing or empty."


# --- 2. TEST: Profile Found in Secondary DB (Customer - Priority 1) ---
@patch("app.routes.portal.supabase")
@patch("app.routes.portal.supabase_secondary")
def test_get_profile_secondary_success(mock_supabase_sec, mock_supabase_pri):
    mock_data = [
        {
            "id": "user-123",
            "first_name": "Juan",
            "last_name": "Delacruz",
            "email": "juan@example.com",
        }
    ]

    mock_execute = MagicMock()
    mock_execute.data = mock_data
    mock_supabase_sec.table.return_value.select.return_value.eq.return_value.execute.return_value = (
        mock_execute
    )

    response = client.get(
        "/api/v1/portal/profile", headers={"x-user-id": "user-123"}
    )

    assert response.status_code == 200
    data = response.json()
    assert data["id"] == "user-123"
    assert data["full_name"] == "Juan Delacruz"
    mock_supabase_sec.table.assert_called_once_with("users")


# --- 3. TEST: Profile Fallback to Primary DB (Admin/Sales - Priority 2) ---
@patch("app.routes.portal.supabase")
@patch("app.routes.portal.supabase_secondary")
def test_get_profile_primary_fallback_success(
    mock_supabase_sec, mock_supabase_pri
):
    # Secondary DB returns empty list
    mock_execute_sec = MagicMock()
    mock_execute_sec.data = []
    mock_supabase_sec.table.return_value.select.return_value.eq.return_value.execute.return_value = (
        mock_execute_sec
    )

    # Primary DB returns the user profile
    mock_data_pri = [
        {
            "id": "admin-456",
            "first_name": "Maria",
            "last_name": "Clara",
            "email": "maria@example.com",
        }
    ]
    mock_execute_pri = MagicMock()
    mock_execute_pri.data = mock_data_pri
    mock_supabase_pri.table.return_value.select.return_value.eq.return_value.execute.return_value = (
        mock_execute_pri
    )

    response = client.get(
        "/api/v1/portal/profile", headers={"x-user-id": "admin-456"}
    )

    assert response.status_code == 200
    data = response.json()
    assert data["id"] == "admin-456"
    assert data["full_name"] == "Maria Clara"
    mock_supabase_pri.table.assert_called_once_with("profiles")


# --- 4. TEST: Profile Not Found in Both DBs (404 Not Found) ---
@patch("app.routes.portal.supabase")
@patch("app.routes.portal.supabase_secondary")
def test_get_profile_not_found(mock_supabase_sec, mock_supabase_pri):
    mock_execute_empty = MagicMock()
    mock_execute_empty.data = []

    mock_supabase_sec.table.return_value.select.return_value.eq.return_value.execute.return_value = (
        mock_execute_empty
    )
    mock_supabase_pri.table.return_value.select.return_value.eq.return_value.execute.return_value = (
        mock_execute_empty
    )

    response = client.get(
        "/api/v1/portal/profile", headers={"x-user-id": "user-404"}
    )

    assert response.status_code == 404
    assert (
        response.json()["detail"] == "Profile not found for user ID: user-404"
    )


# --- 5. TEST: Supabase Error Handled as Not Found ---
@patch("app.routes.portal.supabase")
@patch("app.routes.portal.supabase_secondary")
def test_get_profile_database_error(mock_supabase_sec, mock_supabase_pri):
    mock_supabase_sec.table.side_effect = Exception("Connection Timeout")
    mock_supabase_pri.table.side_effect = Exception("Connection Timeout")

    response = client.get(
        "/api/v1/portal/profile", headers={"x-user-id": "user-123"}
    )

    assert response.status_code == 404
    assert response.json()["detail"] == "Profile not found for user ID: user-123"