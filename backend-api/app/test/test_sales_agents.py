from unittest.mock import MagicMock, patch
from datetime import datetime, timezone, timedelta
import pytest
from fastapi import FastAPI
from fastapi.testclient import TestClient
from app.routes.sales_agent.sales_agents import router

app = FastAPI()
app.include_router(router)

client = TestClient(app)


# ============================================================================
# 1. TEST: GET /api/sales/priority-followups
# ============================================================================

@patch("app.routes.sales_agent.sales_agents.supabase_secondary")
def test_get_priority_followups_success(mock_supabase):
    # Mock date para sa past inquiries (halimbawa: 4 na araw na ang nakalipas)
    past_date = (datetime.now(timezone.utc) - timedelta(days=4)).isoformat()

    mock_data = [
        {
            "id": "inq-1",
            "inquiry_code": "INQ-001",
            "company_name": "Acme Corp",
            "contact_person": "John Doe",
            "service_type": "Sea Freight",
            "status": "new_inquiry",
            "estimated_amount": 5000,
            "created_at": past_date,
        }
    ]

    # Mock Supabase chain
    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.select.return_value = mock_query
    mock_query.neq.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=mock_data)

    response = client.get("/api/sales/priority-followups")

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["status"] == "success"
    assert len(res_json["data"]) == 1
    
    first_item = res_json["data"][0]
    assert first_item["id"] == "inq-1"
    assert first_item["priority_level"] == "CRITICAL"  
    assert first_item["client_name"] == "Acme Corp"


@patch("app.routes.sales_agent.sales_agents.supabase_secondary")
def test_get_priority_followups_empty_data(mock_supabase):
    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.select.return_value = mock_query
    mock_query.neq.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=[])

    response = client.get("/api/sales/priority-followups")

    assert response.status_code == 200
    assert response.json() == {"status": "success", "data": []}


@patch("app.routes.sales_agent.sales_agents.supabase_secondary")
def test_get_priority_followups_exception(mock_supabase):
    # I-simulate ang DB failure
    mock_supabase.table.side_effect = Exception("DB Connection Error")

    response = client.get("/api/sales/priority-followups")

    assert response.status_code == 500
    assert "Service Error: DB Connection Error" in response.json()["detail"]


# ============================================================================
# 2. TEST: GET /api/sales/leads-and-routes
# ============================================================================

@patch("app.routes.sales_agent.sales_agents.supabase_secondary")
def test_get_leads_and_routes_success(mock_supabase):
    mock_data = [
        {
            "status": "new_inquiry",
            "service_type": "Air Freight",
            "origin": "Manila",
            "destination": "Cebu",
        },
        {
            "status": "quote_sent",
            "service_type": "Land Transport",
            "origin": None,
            "destination": None,
        },
    ]

    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.select.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=mock_data)

    response = client.get("/api/sales/leads-and-routes")

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["status"] == "success"
    assert len(res_json["lead_statuses"]) == 6  
    assert len(res_json["top_routes"]) == 2


@patch("app.routes.sales_agent.sales_agents.supabase_secondary")
def test_get_leads_and_routes_empty(mock_supabase):
    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.select.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=[])

    response = client.get("/api/sales/leads-and-routes")

    assert response.status_code == 200
    assert response.json() == {
        "status": "success",
        "lead_statuses": [],
        "top_routes": [],
    }


@patch("app.routes.sales_agent.sales_agents.supabase_secondary")
def test_get_leads_and_routes_exception(mock_supabase):
    mock_supabase.table.side_effect = Exception("Pandas Processing Error")

    response = client.get("/api/sales/leads-and-routes")

    assert response.status_code == 500
    assert response.json()["detail"] == "Pandas Processing Error"


# ============================================================================
# 3. TEST: GET /api/sales/top-customers
# ============================================================================

@patch("app.routes.sales_agent.sales_agents.supabase_secondary")
def test_get_top_customers_success(mock_supabase):
    mock_data = [
        {
            "company_name": "Logistics Pro",
            "contact_person": "Juan Cruz",
            "total_bookings": 15,
            "tier": "gold",
        },
        {
            "company_name": "Single Name Co",
            "contact_person": "Cher",
            "total_bookings": 5,
            "tier": None,
        },
        {
            "company_name": "No Contact Inc",
            "contact_person": None,
            "total_bookings": 2,
            "tier": "silver",
        },
    ]

    mock_query = MagicMock()
    mock_supabase.table.return_value = mock_query
    mock_query.select.return_value = mock_query
    mock_query.order.return_value = mock_query
    mock_query.limit.return_value = mock_query
    mock_query.execute.return_value = MagicMock(data=mock_data)

    response = client.get("/api/sales/top-customers")

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["status"] == "success"
    
    customers = res_json["customers"]
    assert len(customers) == 3
    
    # Check Initials generation logic
    assert customers[0]["initials"] == "JC"      
    assert customers[1]["initials"] == "CH"      
    assert customers[2]["initials"] == "NO"      
    
    # Check default tier fallback
    assert customers[1]["tier"] == "BRONZE"


@patch("app.routes.sales_agent.sales_agents.supabase_secondary")
def test_get_top_customers_exception(mock_supabase):
    mock_supabase.table.side_effect = Exception("Database Timeout")

    response = client.get("/api/sales/top-customers")

    assert response.status_code == 500
    assert "Top Customers Error: Database Timeout" in response.json()["detail"]