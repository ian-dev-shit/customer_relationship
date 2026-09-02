import pytest
from unittest.mock import patch, MagicMock
from fastapi import FastAPI
from fastapi.testclient import TestClient

from app.routes.admin.admin import router

app = FastAPI()
app.include_router(router)
client = TestClient(app)

# Helper para i-setup ang mock response ng Supabase table chain
def mock_supabase_table_chain(data=None):
    mock_execute = MagicMock()
    mock_execute.data = data if data is not None else []
    
    chain = MagicMock()
    chain.select.return_value = chain
    chain.eq.return_value = chain
    chain.insert.return_value = chain
    chain.update.return_value = chain
    chain.execute.return_value = mock_execute
    return chain


@patch("app.routes.admin.admin.send_customer_welcome_email")
@patch("app.routes.admin.admin.supabase_secondary")
def test_create_customer_from_ticket_success(mock_supabase, mock_send_email):
    # 1. Setup Table Chain Mocks
    mock_supabase.table.return_value = mock_supabase_table_chain(data=[])

    # 2. Setup Auth Admin Mock
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

    response = client.post("/api/v1/admin/create-customer-from-ticket", json=payload)

    assert response.status_code == 200
    assert response.json()["status"] == "success"
    assert response.json()["user_id"] == "mock-new-user-uuid-1234"


@patch("app.routes.admin.admin.supabase_secondary")
def test_create_customer_from_ticket_auth_failed(mock_supabase):
    # 1. Setup Table Chain Mocks
    mock_supabase.table.return_value = mock_supabase_table_chain(data=[])

    # 2. Setup Auth failure (walang returned user)
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

    response = client.post("/api/v1/admin/create-customer-from-ticket", json=payload)

    assert response.status_code == 400
    assert "Failed to create Auth account." in response.json()["detail"]


@patch("app.routes.admin.admin.supabase_secondary")
def test_create_customer_from_ticket_server_error(mock_supabase):
    # 1. Setup Table Chain Mocks
    mock_supabase.table.return_value = mock_supabase_table_chain(data=[])

    # 2. Setup Exception on Auth
    mock_supabase.auth.admin.create_user.side_effect = Exception("Unexpected Supabase Auth Error")

    payload = {
        "ticket_id": "123e4567-e89b-12d3-a456-426614174000",
        "email": "error@abc.com",
        "password": "CustomerPassword123!",
        "first_name": "Juan",
        "last_name": "Dela Cruz",
    }

    response = client.post("/api/v1/admin/create-customer-from-ticket", json=payload)

    assert response.status_code == 400
    assert "Unexpected Supabase Auth Error" in response.json()["detail"]