from fastapi import FastAPI
from fastapi.testclient import TestClient
from unittest.mock import patch, MagicMock
import pytest
import sys

sys.modules["weasyprint"] = MagicMock()

from app.routes.sales_agent.rates import router, load_rates_data

app = FastAPI()
app.include_router(router)

client = TestClient(app)

MOCK_RATES = [
    {
        "id": "RATE-001",
        "mode": "AIR",
        "type": "local",
        "delivery_option": "Door-to-Door",
        "transit_time": "1-2 Days",
        "carrier": "Air21",
        "origin": "Manila",
        "destination": "Cebu",
        "base_rate_per_kg": 50.0,
        "min_weight_kg": 10,
        "trucking_pickup_fee": 500.0,
        "trucking_delivery_fee": 500.0,
        "documentation_fee": 200.0,
        "handling_fee": 300.0,
        "currency": "PHP",
        "valid_until": "2026-12-31"
    },
    {
        "id": "RATE-002",
        "mode": "SEA",
        "type": "local",
        "delivery_option": "Port-to-Port",
        "transit_time": "3-5 Days",
        "carrier": "2GO",
        "origin": "Manila",
        "destination": "Davao",
        "base_rate_flat": 2500.0,
        "documentation_fee": 150.0,
        "handling_fee": 250.0,
        "currency": "PHP",
        "valid_until": "2026-12-31"
    }
]

@patch("app.routes.sales_agent.rates.load_rates_data", return_value=MOCK_RATES)
def test_search_rates_all(mock_load):
    response = client.get("/api/v1/sales-agent/rates")
    assert response.status_code == 200
    data = response.json()
    assert data["status"] == "success"
    assert data["count"] == 2

@patch("app.routes.sales_agent.rates.load_rates_data", return_value=MOCK_RATES)
def test_search_rates_filtered(mock_load):
    response = client.get("/api/v1/sales-agent/rates?mode=AIR&destination=Cebu")
    assert response.status_code == 200
    data = response.json()
    assert data["count"] == 1
    assert data["data"][0]["id"] == "RATE-001"

@patch("app.routes.sales_agent.rates.load_rates_data", return_value=MOCK_RATES)
def test_calculate_quotation_success(mock_load):
    payload = {
        "rate_id": "RATE-001",
        "weight_kg": 20.0,
        "cbm": 0.0
    }
    response = client.post("/api/v1/sales-agent/rates/calculate", json=payload)
    assert response.status_code == 200
    data = response.json()
    assert data["status"] == "success"
    assert data["rate_id"] == "RATE-001"
    assert data["breakdown"]["base_freight"] == 1000.0
    assert data["total_quotation"] == 2500.0

@patch("app.routes.sales_agent.rates.load_rates_data", return_value=MOCK_RATES)
def test_calculate_quotation_not_found(mock_load):
    payload = {
        "rate_id": "INVALID-ID",
        "weight_kg": 10.0
    }
    response = client.post("/api/v1/sales-agent/rates/calculate", json=payload)
    assert response.status_code == 404