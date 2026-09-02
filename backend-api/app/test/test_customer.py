import pytest
from uuid import uuid4
from unittest.mock import MagicMock, patch
from fastapi import FastAPI
from fastapi.testclient import TestClient

from app.routes.customers.customers import router

app = FastAPI()
app.include_router(router)
client = TestClient(app)

TEST_CUSTOMER_ID = str(uuid4())

# Mock Payload data para sa BookingCreate schema
VALID_BOOKING_PAYLOAD = {
    "customer_id": TEST_CUSTOMER_ID,
    "service_type": "Freight Shipping",
    "origin": "Manila Port",
    "destination": "Quezon City Warehouse",
    "pickup_datetime": "2026-09-01T10:00:00",
    "agreed_amount": 1500.00,
    "cargo_details": "Fragile items",
    "booking_status": "New Booking"
}


# ==========================================
# 1. TESTS FOR GET /api/v1/customers/stats
# ==========================================

@patch("app.routes.customers.customers.supabase_secondary")
def test_get_customer_stats_success(mock_supabase):
    mock_response = MagicMock()
   
    mock_response.data = [
        {"tier": "bronze"},
        {"tier": "silver"},
        {"tier": "gold"},
        {"tier": "platinum"},
        {"tier": "bronze"},  
    ]
    
    mock_supabase.table.return_value.select.return_value.execute.return_value = mock_response

    response = client.get("/api/v1/customers/stats")

    assert response.status_code == 200
    assert response.json() == {
        "status": "success",
        "data": {
            "all": 5,
            "bronze": 2,
            "silver": 1,
            "gold": 1,
            "platinum": 1
        }
    }


@patch("app.routes.customers.customers.supabase_secondary")
def test_get_customer_stats_error_handling(mock_supabase):
    mock_supabase.table.side_effect = Exception("Database connection failed")

    response = client.get("/api/v1/customers/stats")

    assert response.status_code == 500
    assert "Database connection failed" in response.json()["detail"]


# ==========================================
# 2. TESTS FOR GET /api/v1/customers
# ==========================================

@patch("app.routes.customers.customers.supabase_secondary")
def test_get_all_customers_no_filters(mock_supabase):
    mock_response = MagicMock()
    mock_response.data = [
        {"id": TEST_CUSTOMER_ID, "company_name": "Company A", "tier": "GOLD"}
    ]

    mock_query = mock_supabase.table.return_value.select.return_value
    mock_query.order.return_value.execute.return_value = mock_response

    response = client.get("/api/v1/customers")

    assert response.status_code == 200
    assert response.json() == {
        "status": "success",
        "data": [
            {"id": TEST_CUSTOMER_ID, "company_name": "Company A", "tier": "GOLD"}
        ]
    }


@patch("app.routes.customers.customers.supabase_secondary")
def test_get_all_customers_with_tier_and_search_filter(mock_supabase):
    mock_response = MagicMock()
    mock_response.data = [
        {"id": TEST_CUSTOMER_ID, "company_name": "Acme Corp", "tier": "GOLD"}
    ]

    mock_select = mock_supabase.table.return_value.select.return_value
    mock_eq = MagicMock()
    mock_or = MagicMock()
    
    mock_select.eq.return_value = mock_eq
    mock_eq.or_.return_value = mock_or
    mock_or.order.return_value.execute.return_value = mock_response

    response = client.get("/api/v1/customers?tier=gold&search=Acme")

    assert response.status_code == 200
    assert len(response.json()["data"]) == 1
    mock_select.eq.assert_called_once_with("tier", "GOLD")


# ==========================================
# 3. TESTS FOR GET /api/v1/customers/{customer_id}
# ==========================================

@patch("app.routes.customers.customers.supabase_secondary")
def test_get_customer_by_id_success(mock_supabase):
    mock_response = MagicMock()
    # Idinagdag ang kulang na CustomerResponse required fields
    mock_response.data = [
        {
            "id": TEST_CUSTOMER_ID,
            "company_name": "Tech Corp",
            "contact_person": "Juan Dela Cruz",
            "email": "juan@techcorp.com",
            "tier": "PLATINUM",
            "total_bookings": 0,
            "created_at": "2026-01-01T00:00:00Z",
            "updated_at": "2026-01-01T00:00:00Z"
        }
    ]

    mock_supabase.table.return_value.select.return_value.eq.return_value.execute.return_value = mock_response

    response = client.get(f"/api/v1/customers/{TEST_CUSTOMER_ID}")

    assert response.status_code == 200
    assert response.json()["id"] == TEST_CUSTOMER_ID
    assert response.json()["email"] == "juan@techcorp.com"


@patch("app.routes.customers.customers.supabase_secondary")
def test_get_customer_by_id_not_found(mock_supabase):
    mock_response = MagicMock()
    mock_response.data = []

    mock_supabase.table.return_value.select.return_value.eq.return_value.execute.return_value = mock_response

    response = client.get(f"/api/v1/customers/{TEST_CUSTOMER_ID}")

    assert response.status_code == 404
    assert response.json()["detail"] == "Customer not found."


def test_get_customer_by_invalid_uuid():
    response = client.get("/api/v1/customers/invalid-uuid-string")
    assert response.status_code == 422



# ===================================================
# TESTS FOR POST /api/v1/customers/{customer_id}/bookings
# ===================================================

@patch("app.routes.customers.customers.supabase_secondary")
def test_create_booking_success(mock_supabase):
    # Step A Mock: Customer check returns existing customer
    mock_customer_res = MagicMock()
    mock_customer_res.data = [{"id": TEST_CUSTOMER_ID}]

    # Step C Mock: Booking insert succeeds
    mock_insert_res = MagicMock()
    mock_insert_res.data = [
        {
            "id": str(uuid4()),
            "customer_id": TEST_CUSTOMER_ID,
            **VALID_BOOKING_PAYLOAD
        }
    ]

    # Setup database call chain mocks
    # Table 1: customers table check
    mock_customers_table = MagicMock()
    mock_customers_table.select.return_value.eq.return_value.execute.return_value = mock_customer_res

    # Table 2: bookings table insert
    mock_bookings_table = MagicMock()
    mock_bookings_table.insert.return_value.execute.return_value = mock_insert_res

    # Route table() calls dynamically based on table name
    def table_router(table_name):
        if table_name == "customers":
            return mock_customers_table
        elif table_name == "bookings":
            return mock_bookings_table
        return MagicMock()

    mock_supabase.table.side_effect = table_router

    response = client.post(
        f"/api/v1/customers/{TEST_CUSTOMER_ID}/bookings",
        json=VALID_BOOKING_PAYLOAD
    )

    assert response.status_code == 201
    assert response.json()["status"] == "success"
    assert response.json()["message"] == "Booking created successfully"
    assert response.json()["data"]["customer_id"] == TEST_CUSTOMER_ID


@patch("app.routes.customers.customers.supabase_secondary")
def test_create_booking_customer_not_found(mock_supabase):
    # Step A Mock: Customer check returns empty list
    mock_customer_res = MagicMock()
    mock_customer_res.data = []

    mock_supabase.table.return_value.select.return_value.eq.return_value.execute.return_value = mock_customer_res

    response = client.post(
        f"/api/v1/customers/{TEST_CUSTOMER_ID}/bookings",
        json=VALID_BOOKING_PAYLOAD
    )

    assert response.status_code == 404
    assert response.json()["detail"] == "Cannot create booking. Customer not found."


@patch("app.routes.customers.customers.supabase_secondary")
def test_create_booking_insert_failed(mock_supabase):
    mock_customer_res = MagicMock()
    mock_customer_res.data = [{"id": TEST_CUSTOMER_ID}]

    # Step C Mock: Insert returns empty data
    mock_insert_res = MagicMock()
    mock_insert_res.data = []

    mock_customers_table = MagicMock()
    mock_customers_table.select.return_value.eq.return_value.execute.return_value = mock_customer_res

    mock_bookings_table = MagicMock()
    mock_bookings_table.insert.return_value.execute.return_value = mock_insert_res

    def table_router(table_name):
        if table_name == "customers":
            return mock_customers_table
        elif table_name == "bookings":
            return mock_bookings_table
        return MagicMock()

    mock_supabase.table.side_effect = table_router

    response = client.post(
        f"/api/v1/customers/{TEST_CUSTOMER_ID}/bookings",
        json=VALID_BOOKING_PAYLOAD
    )

    assert response.status_code == 400
    assert response.json()["detail"] == "Failed to create booking."


@patch("app.routes.customers.customers.supabase_secondary")
def test_create_booking_database_exception(mock_supabase):
    # Simulate an unexpected database error
    mock_supabase.table.side_effect = Exception("Database connection timeout")

    response = client.post(
        f"/api/v1/customers/{TEST_CUSTOMER_ID}/bookings",
        json=VALID_BOOKING_PAYLOAD
    )

    assert response.status_code == 500
    assert "Database connection timeout" in response.json()["detail"]


def test_create_booking_invalid_uuid():
    # Pass an invalid UUID string to test FastAPI validation automatically
    response = client.post(
        "/api/v1/customers/invalid-uuid-string/bookings",
        json=VALID_BOOKING_PAYLOAD
    )

    assert response.status_code == 422