from unittest.mock import MagicMock, patch
from fastapi import FastAPI
from fastapi.testclient import TestClient

from app.routes.admin import router

app = FastAPI()
app.include_router(router)

client = TestClient(app)

# Mock Ticket Object na sumusunod sa CloseWonTicketResponseSchema
MOCK_TICKET = {
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "inquiry_id": "123e4567-e89b-12d3-a456-426614174001",
    "company_name": "ABC Logistics",
    "contact_person": "Juan Dela Cruz",
    "email": "juan@abc.com",
    "phone_number": "09171234567",
    "agreed_amount": 15000.0,
    "created_at": "2026-08-21T00:00:00Z",
    "ticket_status": "for account",
}

# ==========================================
# 1. TEST: GET /api/v1/admin/close-won-tickets
# ==========================================


@patch("app.routes.admin.supabase_secondary")
def test_get_close_won_tickets_success(mock_supabase):
    mock_execute = MagicMock()
    mock_execute.data = [MOCK_TICKET]

    # Inayos ang call chain para mag-match sa .table().select().eq().execute()
    mock_supabase.table.return_value.select.return_value.eq.return_value.execute.return_value = (
        mock_execute
    )

    response = client.get("/api/v1/admin/close-won-tickets")

    assert response.status_code == 200
    res_json = response.json()
    assert isinstance(res_json, list)
    assert len(res_json) == 1
    assert res_json[0]["id"] == "123e4567-e89b-12d3-a456-426614174000"
    assert res_json[0]["company_name"] == "ABC Logistics"
    mock_supabase.table.assert_called_once_with("tickets")


@patch("app.routes.admin.supabase_secondary")
def test_get_close_won_tickets_server_error(mock_supabase):
    mock_supabase.table.side_effect = Exception("Database fetch error")

    response = client.get("/api/v1/admin/close-won-tickets")

    assert response.status_code == 500
    assert "Database fetch error" in response.json()["detail"]


# ==========================================
# 2. TEST: POST /api/v1/admin/create-customer-from-ticket
# ==========================================


@patch("app.routes.admin.send_customer_welcome_email")
@patch("app.routes.admin.supabase_secondary")
def test_create_customer_from_ticket_success(mock_supabase, mock_send_email):
    mock_user = MagicMock()
    mock_user.id = "mock-new-user-uuid-1234"

    mock_auth_res = MagicMock()
    mock_auth_res.user = mock_user

    mock_supabase.auth.admin.create_user.return_value = mock_auth_res

    payload = {
        "ticket_id": "123e4567-e89b-12d3-a456-426614174000",
        "email": "juan@abc.com",
        "password": "CustomerPassword123!",
        "first_name": "Juan",
        "last_name": "Dela Cruz",
        "company_name": "ABC Logistics",
        "phone_number": "09171234567",
    }

    response = client.post(
        "/api/v1/admin/create-customer-from-ticket", json=payload
    )

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["status"] == "success"
    assert res_json["user_id"] == "mock-new-user-uuid-1234"

    assert mock_supabase.table.call_count >= 2
    mock_supabase.table.assert_any_call("users")
    mock_supabase.table.assert_any_call("tickets")


@patch("app.routes.admin.supabase_secondary")
def test_create_customer_from_ticket_auth_failed(mock_supabase):
    mock_auth_res = MagicMock()
    mock_auth_res.user = None

    mock_supabase.auth.admin.create_user.return_value = mock_auth_res

    payload = {
        "ticket_id": "123e4567-e89b-12d3-a456-426614174000",
        "email": "failed@abc.com",
        "password": "CustomerPassword123!",
        "first_name": "Juan",
        "last_name": "Dela Cruz",
    }

    response = client.post(
        "/api/v1/admin/create-customer-from-ticket", json=payload
    )

    assert response.status_code == 400
    assert response.json()["detail"] == "Failed to create Auth account."


def test_create_customer_from_ticket_validation_failed():
    payload = {
        "ticket_id": "123e4567-e89b-12d3-a456-426614174000",
        "first_name": "Juan",
    }

    response = client.post(
        "/api/v1/admin/create-customer-from-ticket", json=payload
    )

    assert response.status_code == 422


@patch("app.routes.admin.supabase_secondary")
def test_create_customer_from_ticket_server_error(mock_supabase):
    mock_supabase.auth.admin.create_user.side_effect = Exception(
        "Unexpected Supabase Auth Error"
    )

    payload = {
        "ticket_id": "123e4567-e89b-12d3-a456-426614174000",
        "email": "error@abc.com",
        "password": "CustomerPassword123!",
        "first_name": "Juan",
        "last_name": "Dela Cruz",
    }

    response = client.post(
        "/api/v1/admin/create-customer-from-ticket", json=payload
    )

    assert response.status_code == 500
    assert "Unexpected Supabase Auth Error" in response.json()["detail"]


# ==========================================
# 3. TEST: GET /api/v1/admin/customer-accounts
# ==========================================


@patch("app.routes.admin.supabase_secondary")
def test_get_customer_accounts_success(mock_supabase):
    mock_data = [
        {
            "id": "mock-new-user-uuid-1234",
            "email": "juan@abc.com",
            "first_name": "Juan",
            "last_name": "Dela Cruz",
            "company_name": "ABC Logistics",
            "phone_number": "09171234567",
            "role": "customer",
        }
    ]

    mock_execute = MagicMock()
    mock_execute.data = mock_data

    mock_supabase.table.return_value.select.return_value.execute.return_value = (
        mock_execute
    )

    response = client.get("/api/v1/admin/customer-accounts")

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["status"] == "success"
    assert len(res_json["data"]) == 1
    assert res_json["data"][0]["email"] == "juan@abc.com"
    mock_supabase.table.assert_called_once_with("users")


@patch("app.routes.admin.supabase_secondary")
def test_get_customer_accounts_server_error(mock_supabase):
    mock_supabase.table.side_effect = Exception("Users table query error")

    response = client.get("/api/v1/admin/customer-accounts")

    assert response.status_code == 500
    assert "Users table query error" in response.json()["detail"]