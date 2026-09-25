import pytest
from fastapi.testclient import TestClient
from unittest.mock import MagicMock, patch
from app.main import app
import sys

sys.modules["weasyprint"] = MagicMock()

client = TestClient(app)
BASE_URL = "/api/v1/admin/analytics/bi"

# ==========================================
# TEST: GET /api/v1/admin/analytics/bi (Descriptive)
# ==========================================

@patch("app.routes.admin.descriptive.get_descriptive_analytics")
def test_fetch_descriptive_analytics_success(mock_get_descriptive):
    mock_get_descriptive.return_value = {"kpi": {"total_inquiries": 100}}

    response = client.get(BASE_URL)

    assert response.status_code == 200
    assert response.json() == {
        "status": "success",
        "message": "Descriptive analytics fetched successfully",
        "data": {"kpi": {"total_inquiries": 100}}
    }
    mock_get_descriptive.assert_called_once()

@patch("app.routes.admin.descriptive.get_descriptive_analytics")
def test_fetch_descriptive_analytics_error(mock_get_descriptive):
    mock_get_descriptive.side_effect = Exception("DB Connection Error")

    response = client.get(BASE_URL)

    assert response.status_code == 500
    assert "Failed to fetch descriptive analytics: DB Connection Error" in response.json()["detail"]


# ==========================================
# TEST: GET /api/v1/admin/analytics/bi/diagnostic
# ==========================================

@patch("app.routes.admin.descriptive.get_diagnostic_analytics")
def test_fetch_diagnostic_analytics_success(mock_get_diagnostic):
    mock_get_diagnostic.return_value = {"lead_scores": [0.85, 0.92]}

    response = client.get(f"{BASE_URL}/diagnostic")

    assert response.status_code == 200
    assert response.json() == {
        "status": "success",
        "message": "Diagnostic analytics fetched successfully",
        "data": {"lead_scores": [0.85, 0.92]}
    }
    mock_get_diagnostic.assert_called_once()

@patch("app.routes.admin.descriptive.get_diagnostic_analytics")
def test_fetch_diagnostic_analytics_error(mock_get_diagnostic):
    mock_get_diagnostic.side_effect = Exception("ML Model Load Failed")

    response = client.get(f"{BASE_URL}/diagnostic")

    assert response.status_code == 500
    assert "Failed to fetch diagnostic analytics: ML Model Load Failed" in response.json()["detail"]


# ==========================================
# TEST: GET /api/v1/admin/analytics/bi/predictive
# ==========================================

@patch("app.routes.admin.descriptive.get_predictive_analytics")
def test_fetch_predictive_analytics_success(mock_get_predictive):
    mock_get_predictive.return_value = {"forecast": {"next_month": 500000}}

    response = client.get(f"{BASE_URL}/predictive")

    assert response.status_code == 200
    assert response.json() == {
        "status": "success",
        "message": "Predictive analytics fetched successfully",
        "data": {"forecast": {"next_month": 500000}}
    }
    mock_get_predictive.assert_called_once()

@patch("app.routes.admin.descriptive.get_predictive_analytics")
def test_fetch_predictive_analytics_error(mock_get_predictive):
    mock_get_predictive.side_effect = Exception("Prediction Timeout")

    response = client.get(f"{BASE_URL}/predictive")

    assert response.status_code == 500
    assert "Failed to fetch predictive analytics: Prediction Timeout" in response.json()["detail"]


# ==========================================
# TEST: GET /api/v1/admin/analytics/bi/prescriptive
# ==========================================

@patch("app.routes.admin.descriptive.get_prescriptive_analytics")
def test_fetch_prescriptive_analytics_success(mock_get_prescriptive):
    mock_get_prescriptive.return_value = {"recommendations": ["Increase air freight capacity"]}

    response = client.get(f"{BASE_URL}/prescriptive")

    assert response.status_code == 200
    assert response.json() == {
        "status": "success",
        "message": "Prescriptive analytics generated successfully",
        "data": {"recommendations": ["Increase air freight capacity"]}
    }
    mock_get_prescriptive.assert_called_once()

@patch("app.routes.admin.descriptive.get_prescriptive_analytics")
def test_fetch_prescriptive_analytics_error(mock_get_prescriptive):
    mock_get_prescriptive.side_effect = Exception("Prescriptive Engine Failed")

    response = client.get(f"{BASE_URL}/prescriptive")

    assert response.status_code == 500
    assert "Failed to fetch prescriptive analytics: Prescriptive Engine Failed" in response.json()["detail"]