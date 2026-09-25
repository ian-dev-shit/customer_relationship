import sys
from unittest.mock import MagicMock

# 1. PREVENT WEASYPRINT ERROR ON WINDOWS ENVIRONMENT
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from unittest.mock import patch
from app.main import app

client = TestClient(app)

# ==========================================
# MOCK QUERY CHAIN FOR SUPABASE METHOD CHAINING
# ==========================================

class SupabaseAuditQueryMock:
    """
    Mock query builder object para sa Supabase fluently chained calls:
    .table().select().eq().order().limit().execute()
    """
    def __init__(self, data=None):
        self.data = data if data is not None else []

    def select(self, *args, **kwargs):
        return self

    def eq(self, *args, **kwargs):
        return self

    def order(self, *args, **kwargs):
        return self

    def limit(self, *args, **kwargs):
        return self

    def execute(self, *args, **kwargs):
        mock_response = MagicMock()
        mock_response.data = self.data
        return mock_response


# ==========================================
# TEST CASES: GET AUDIT LOGS
# ==========================================

@patch("app.routes.super_admin.audit.supabase_secondary")
def test_get_audit_logs_no_filters_success(mock_supabase_secondary):
    mock_logs = [
        {
            "id": 1,
            "user_email": "admin@example.com",
            "user_role": "super_admin",
            "action": "LOGIN_SUCCESS",
            "module": "AUTH",
            "details": "User logged in",
            "created_at": "2026-03-30T10:00:00Z"
        },
        {
            "id": 2,
            "user_email": "customer@example.com",
            "user_role": "customer",
            "action": "CREATE_TICKET",
            "module": "SUPPORT",
            "details": "Created ticket #123",
            "created_at": "2026-03-30T09:00:00Z"
        }
    ]
    mock_supabase_secondary.table.return_value = SupabaseAuditQueryMock(data=mock_logs)

    response = client.get("/api/v1/super_admin/audit/logs")

    assert response.status_code == 200
    res_data = response.json()
    assert len(res_data) == 2
    assert res_data[0]["user_role"] == "super_admin"
    assert res_data[1]["module"] == "SUPPORT"


@patch("app.routes.super_admin.audit.supabase_secondary")
def test_get_audit_logs_with_role_and_module_filters(mock_supabase_secondary):
    mock_filtered_logs = [
        {
            "id": 1,
            "user_email": "sales@example.com",
            "user_role": "sales_agent",
            "action": "LOGIN_SUCCESS",
            "module": "AUTH",
            "details": "User logged in",
            "created_at": "2026-03-30T10:00:00Z"
        }
    ]
    mock_supabase_secondary.table.return_value = SupabaseAuditQueryMock(data=mock_filtered_logs)

    response = client.get(
        "/api/v1/super_admin/audit/logs",
        params={"role_filter": "sales_agent", "module_filter": "AUTH"}
    )

    assert response.status_code == 200
    res_data = response.json()
    assert len(res_data) == 1
    assert res_data[0]["user_role"] == "sales_agent"
    assert res_data[0]["module"] == "AUTH"


@patch("app.routes.super_admin.audit.supabase_secondary")
def test_get_audit_logs_empty_result(mock_supabase_secondary):
    # Simulate DB returning empty list or None
    mock_supabase_secondary.table.return_value = SupabaseAuditQueryMock(data=None)

    response = client.get("/api/v1/super_admin/audit/logs")

    assert response.status_code == 200
    assert response.json() == []


@patch("app.routes.super_admin.audit.supabase_secondary")
def test_get_audit_logs_500_server_error(mock_supabase_secondary):
    # Simulate database connection crash
    mock_supabase_secondary.table.side_effect = Exception("Database query failed")

    response = client.get("/api/v1/super_admin/audit/logs")

    assert response.status_code == 500
    assert "Database query failed" in response.json()["detail"]