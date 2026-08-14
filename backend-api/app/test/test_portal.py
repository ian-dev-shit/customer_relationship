from unittest.mock import MagicMock, patch
from fastapi import FastAPI
from fastapi.testclient import TestClient

from app.routes.portal import router

# Gumawa ng temporary FastAPI app para sa testing ng router
app = FastAPI()
app.include_router(router)

client = TestClient(app)


# --- 1. TEST: Missing/Empty Header (400 Bad Request) ---
def test_get_profile_missing_header():
    # Nag-call nang walang 'x-user-id' header
    response = client.get("/api/v1/portal/profile")

    assert response.status_code == 400
    assert response.json()["detail"] == "Header 'x-user-id' is missing or empty."


def test_get_profile_empty_header():
    # Nag-call na spaces lang ang laman ng header
    response = client.get(
        "/api/v1/portal/profile", headers={"x-user-id": "   "}
    )

    assert response.status_code == 400
    assert response.json()["detail"] == "Header 'x-user-id' is missing or empty."


# --- 2. TEST: Profile Found (200 OK) ---
@patch("app.routes.portal.supabase")  
def test_get_profile_success(mock_supabase):
    # Mocking sa response data ng Supabase
    mock_data = [
        {
            "id": "user-123",
            "first_name": "Juan",
            "last_name": "Delacruz",
            "email": "juan@example.com",
        }
    ]

    # I-chain ang mock methods para mag-match sa supabase.table().select().eq().execute()
    mock_execute = MagicMock()
    mock_execute.data = mock_data
    mock_supabase.table.return_value.select.return_value.eq.return_value.execute.return_value = (
        mock_execute
    )

    # Call endpoint
    response = client.get(
        "/api/v1/portal/profile", headers={"x-user-id": "user-123"}
    )

    assert response.status_code == 200
    data = response.json()
    assert data["id"] == "user-123"
    assert data["full_name"] == "Juan Delacruz"


# --- 3. TEST: Profile Not Found (404 Not Found) ---
@patch("app.routes.portal.supabase")
def test_get_profile_not_found(mock_supabase):
    # Mocking na walang nahanap na record (empty list)
    mock_execute = MagicMock()
    mock_execute.data = []
    mock_supabase.table.return_value.select.return_value.eq.return_value.execute.return_value = (
        mock_execute
    )

    response = client.get(
        "/api/v1/portal/profile", headers={"x-user-id": "user-404"}
    )

    assert response.status_code == 404
    assert (
        response.json()["detail"] == "Profile not found for user ID: user-404"
    )


# --- 4. TEST: Supabase/Database Error (500 Internal Server Error) ---
@patch("app.routes.portal.supabase")
def test_get_profile_database_error(mock_supabase):
    # I-simulate ang error kapag nag-query sa Supabase
    mock_supabase.table.side_effect = Exception("Connection Timeout")

    response = client.get(
        "/api/v1/portal/profile", headers={"x-user-id": "user-123"}
    )

    assert response.status_code == 500
    assert "Database query failed: Connection Timeout" in response.json()["detail"]