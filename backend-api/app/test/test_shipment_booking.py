from unittest.mock import MagicMock, patch
import uuid
import pytest
from fastapi import FastAPI
from fastapi.testclient import TestClient
from app.routes.sales_agent.shipment_booking import router

app = FastAPI()
app.include_router(router)

client = TestClient(app)

VALID_UUID = str(uuid.uuid4())


# ============================================================================
# 1. TEST: GET /api/v1/shipment-bookings/stats
# ============================================================================

@patch("app.routes.sales_agent.shipment_booking.supabase_secondary")
def test_get_shipment_booking_stats_success(mock_supabase):
    mock_rows = [
        {"booking_status": "quoted"},
        {"booking_status": "confirmed"},
        {"booking_status": "cancelled"},
        {"booking_status": "unknown_status"},  # Mapupunta sa 'booking'
        {"booking_status": None},               # Mapupunta sa 'booking'
    ]

    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.select.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=mock_rows)

    response = client.get("/api/v1/shipment-bookings/stats")

    assert response.status_code == 200
    res = response.json()
    assert res["status"] == "success"
    assert res["data"] == {
        "all": 5,
        "booking": 2,
        "quoted": 1,
        "confirmed": 1,
        "cancelled": 1,
    }


@patch("app.routes.sales_agent.shipment_booking.supabase_secondary")
def test_get_shipment_booking_stats_exception(mock_supabase):
    mock_supabase.table.side_effect = Exception("Database connection error")

    response = client.get("/api/v1/shipment-bookings/stats")

    assert response.status_code == 500
    assert response.json()["detail"] == "Database connection error"


# ============================================================================
# 2. TEST: GET /api/v1/shipment-bookings (Paginated List)
# ============================================================================

@patch("app.routes.sales_agent.shipment_booking.supabase_secondary")
def test_get_shipment_bookings_success(mock_supabase):
    mock_data = [
        {"id": VALID_UUID, "booking_code": "BK-001", "booking_status": "quoted"}
    ]

    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.select.return_value = mock_query
    mock_query.ilike.return_value = mock_query
    mock_query.or_.return_value = mock_query
    mock_query.order.return_value = mock_query
    mock_query.range.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=mock_data, count=1)

    response = client.get(
        "/api/v1/shipment-bookings?status=quoted&search=BK-001&page=1&limit=5"
    )

    assert response.status_code == 200
    res = response.json()
    assert res["status"] == "success"
    assert len(res["data"]) == 1
    assert res["meta"] == {
        "total": 1,
        "page": 1,
        "limit": 5,
        "total_pages": 1,
    }


@patch("app.routes.sales_agent.shipment_booking.supabase_secondary")
def test_get_shipment_bookings_empty(mock_supabase):
    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.select.return_value = mock_query
    mock_query.order.return_value = mock_query
    mock_query.range.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=[], count=0)

    response = client.get("/api/v1/shipment-bookings")

    assert response.status_code == 200
    res = response.json()
    assert res["data"] == []
    assert res["meta"]["total"] == 0
    assert res["meta"]["total_pages"] == 1


# ============================================================================
# 3. TEST: GET /api/v1/shipment-bookings/{booking_id}
# ============================================================================

@patch("app.routes.sales_agent.shipment_booking.supabase_secondary")
def test_get_booking_by_id_success(mock_supabase):
    mock_item = {"id": VALID_UUID, "booking_code": "BK-100"}

    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.select.return_value = mock_query
    mock_query.eq.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=[mock_item])

    response = client.get(f"/api/v1/shipment-bookings/{VALID_UUID}")

    assert response.status_code == 200
    assert response.json()["id"] == VALID_UUID


@patch("app.routes.sales_agent.shipment_booking.supabase_secondary")
def test_get_booking_by_id_not_found(mock_supabase):
    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.select.return_value = mock_query
    mock_query.eq.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=[])

    response = client.get(f"/api/v1/shipment-bookings/{VALID_UUID}")

    assert response.status_code == 404
    assert response.json()["detail"] == "Shipment booking not found."


def test_get_booking_by_id_invalid_uuid():
    response = client.get("/api/v1/shipment-bookings/invalid-uuid-123")

    # FastApi/Pydantic validation raises 422 Unprocessable Entity
    assert response.status_code == 422


# ============================================================================
# 4. TEST: PATCH /api/v1/shipment-bookings/{booking_id}
# ============================================================================

@patch("app.routes.sales_agent.shipment_booking.supabase_secondary")
def test_update_shipment_booking_success(mock_supabase):
    updated_item = {"id": VALID_UUID, "booking_status": "confirmed"}

    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.update.return_value = mock_query
    mock_query.eq.return_value = mock_query
    
    mock_result = MagicMock(data=[updated_item])
    mock_query.execute.return_value = mock_result

    payload = {"booking_status": "confirmed"}
    response = client.patch(f"/api/v1/shipment-bookings/{VALID_UUID}", json=payload)

    assert response.status_code == 200
    res = response.json()
    assert res["status"] == "success"
    assert res["message"] == "Shipment booking updated successfully"


def test_update_shipment_booking_empty_payload():
    # Nagpasa ng walang valid fields
    response = client.patch(f"/api/v1/shipment-bookings/{VALID_UUID}", json={})

    assert response.status_code == 400
    assert response.json()["detail"] == "No fields provided for update."


@patch("app.routes.sales_agent.shipment_booking.supabase_secondary")
def test_update_shipment_booking_not_found(mock_supabase):
    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.update.return_value = mock_query
    mock_query.eq.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=[])

    payload = {"booking_status": "confirmed"}
    response = client.patch(f"/api/v1/shipment-bookings/{VALID_UUID}", json=payload)

    assert response.status_code == 404
    assert response.json()["detail"] == "Booking update failed or record not found."