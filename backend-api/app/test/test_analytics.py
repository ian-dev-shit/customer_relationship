from unittest.mock import MagicMock, patch
import pytest
from fastapi import FastAPI
from fastapi.testclient import TestClient
from app.routes.analytics import router
app = FastAPI()
app.include_router(router)

client = TestClient(app)


# ============================================================================
# 1. TEST: GET /api/v1/analytics/dashboard
# ============================================================================
@patch("app.routes.analytics.supabase_secondary")
@patch("app.routes.analytics.process_sales_analytics")
def test_get_sales_dashboard_analytics_success(
    mock_process, mock_supabase
):
    # Mocking Supabase Table Responses
    mock_query = MagicMock()
    mock_query.select.return_value.execute.return_value = MagicMock(
        data=[{"id": 1}]
    )
    mock_supabase.table.return_value = mock_query

    # Mocking Service Function Response
    mock_process.return_value = {"total_sales": 1000}

    response = client.get("/api/v1/analytics/dashboard")

    assert response.status_code == 200
    assert response.json() == {
        "status": "success",
        "data": {"total_sales": 1000},
    }


@patch("app.routes.analytics.supabase_secondary")
def test_get_sales_dashboard_analytics_exception(mock_supabase):
    # Simulate DB Exception
    mock_supabase.table.side_effect = Exception("Database connection failed")

    response = client.get("/api/v1/analytics/dashboard")

    # The endpoint catches all exceptions and returns a 200 JSON with error status
    assert response.status_code == 200
    assert response.json() == {
        "status": "error",
        "message": "Database connection failed",
    }


# ============================================================================
# 2. TEST: GET /api/v1/analytics/sales-dashboard
# ============================================================================
@patch("app.routes.analytics.get_pipeline_dashboard")
def test_get_pipeline_sales_dashboard_success(mock_get_pipeline):
    mock_get_pipeline.return_value = {
        "status": "success",
        "pipeline": [],
    }

    response = client.get("/api/v1/analytics/sales-dashboard")

    assert response.status_code == 200
    assert response.json() == {"status": "success", "pipeline": []}


@patch("app.routes.analytics.get_pipeline_dashboard")
def test_get_pipeline_sales_dashboard_invalid_structure(mock_get_pipeline):
    mock_get_pipeline.return_value = ["invalid_data"]

    response = client.get("/api/v1/analytics/sales-dashboard")

    assert response.status_code == 500
    assert response.json()["detail"]["status"] == "error"
    assert "Invalid response structure" in response.json()["detail"]["message"]


@patch("app.routes.analytics.get_pipeline_dashboard")
def test_get_pipeline_sales_dashboard_exception(mock_get_pipeline):
    mock_get_pipeline.side_effect = Exception("Service error")

    response = client.get("/api/v1/analytics/sales-dashboard")

    assert response.status_code == 500
    assert response.json()["detail"]["status"] == "error"
    assert "Pipeline Analytics Error" in response.json()["detail"]["message"]


# ============================================================================
# 3. TEST: BIAnalyticsService Endpoints
# ============================================================================
@pytest.mark.parametrize(
    "endpoint,service_method,mock_return",
    [
        (
            "/api/v1/analytics/gross-revenue",
            "get_closed_won_revenue_analytics",
            {"gross_revenue": 50000},
        ),
        (
            "/api/v1/analytics/service-types",
            "get_service_types_analytics",
            {"services": ["air", "sea"]},
        ),
        (
            "/api/v1/analytics/top-routes",
            "get_top_routes_analytics",
            {"routes": ["MNL-CEB"]},
        ),
        (
            "/api/v1/analytics/shipments-closed",
            "get_shipments_closed_analytics",
            {"closed": 120},
        ),
        (
            "/api/v1/analytics/win-loss-service",
            "get_win_loss_by_service_analytics",
            {"win_rate": 0.75},
        ),
        (
            "/api/v1/analytics/service-won-distribution",
            "get_service_won_distribution_analytics",
            {"distribution": {}},
        ),
        (
            "/api/v1/analytics/weight-class-win-loss",
            "get_weight_class_win_loss_analytics",
            {"heavy": 10},
        ),
    ],
)
@patch("app.routes.analytics.BIAnalyticsService")
def test_bi_analytics_endpoints_success(
    mock_bi_service, endpoint, service_method, mock_return
):
    getattr(mock_bi_service, service_method).return_value = mock_return

    response = client.get(endpoint)

    assert response.status_code == 200
    assert response.json() == mock_return


@pytest.mark.parametrize(
    "endpoint,service_method",
    [
        ("/api/v1/analytics/gross-revenue", "get_closed_won_revenue_analytics"),
        ("/api/v1/analytics/service-types", "get_service_types_analytics"),
        ("/api/v1/analytics/top-routes", "get_top_routes_analytics"),
        ("/api/v1/analytics/shipments-closed", "get_shipments_closed_analytics"),
        ("/api/v1/analytics/win-loss-service", "get_win_loss_by_service_analytics"),
        ("/api/v1/analytics/service-won-distribution", "get_service_won_distribution_analytics"),
        ("/api/v1/analytics/weight-class-win-loss", "get_weight_class_win_loss_analytics"),
    ],
)
@patch("app.routes.analytics.BIAnalyticsService")
def test_bi_analytics_endpoints_exception(
    mock_bi_service, endpoint, service_method
):
    # Simulate internal service error
    getattr(mock_bi_service, service_method).side_effect = Exception("BI Service Failed")

    response = client.get(endpoint)

    assert response.status_code == 500
    assert response.json() == {"detail": "BI Service Failed"}