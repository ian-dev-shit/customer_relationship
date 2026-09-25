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

class SupabaseDashboardQueryMock:
    """
    Mock query builder object para sa Supabase fluently chained calls:
    .table().select().eq().order().limit().gte().execute()
    """
    def __init__(self, data=None, count=None):
        self.data = data if data is not None else []
        self.count = count

    def select(self, *args, **kwargs):
        return self

    def eq(self, *args, **kwargs):
        return self

    def order(self, *args, **kwargs):
        return self

    def limit(self, *args, **kwargs):
        return self

    def gte(self, *args, **kwargs):
        return self

    def execute(self, *args, **kwargs):
        mock_response = MagicMock()
        mock_response.data = self.data
        mock_response.count = self.count
        return mock_response


# ==========================================
# TEST CASES
# ==========================================

@patch("app.routes.super_admin.dashboard.supabase_secondary")
def test_get_dashboard_stats_success(mock_supabase_secondary):
    # Mock data sets
    mock_restricted_list = [
        {"id": 1, "email": "user1@example.com", "is_restricted": True, "failed_attempts": 5},
        {"id": 2, "email": "user2@example.com", "is_restricted": True, "failed_attempts": 6}
    ]
    
    mock_audit_list = [
        {"id": 101, "user_email": "user1@example.com", "action": "LOGIN_SUCCESS", "module": "AUTH"},
        {"id": 102, "user_email": "user2@example.com", "action": "LOGIN_FAILED", "module": "AUTH"}
    ]

    # Dynamically return different mock responses depending on table name queried
    def side_effect_table(table_name):
        if table_name == "restrict_attempt":
            # Gagamitin pareho sa count at sa top 5 list query
            return SupabaseDashboardQueryMock(data=mock_restricted_list, count=len(mock_restricted_list))
        elif table_name == "audit_logs":
            # Gagamitin sa total audit count, recent audits list, at today's logins count
            return SupabaseDashboardQueryMock(data=mock_audit_list, count=10)
        return SupabaseDashboardQueryMock()

    mock_supabase_secondary.table.side_effect = side_effect_table

    # Call endpoint
    response = client.get("/api/v1/super_admin/dashboard/stats")

    # Assertions
    assert response.status_code == 200
    res_data = response.json()

    assert res_data["restricted_count"] == 2
    assert res_data["today_logins"] == 10
    assert res_data["audit_count"] == 10
    assert len(res_data["recent_restricted"]) == 2
    assert len(res_data["recent_audits"]) == 2
    assert res_data["recent_restricted"][0]["email"] == "user1@example.com"


@patch("app.routes.super_admin.dashboard.supabase_secondary")
def test_get_dashboard_stats_empty_data(mock_supabase_secondary):
    # Simulate DB returning empty lists and None for counts
    mock_supabase_secondary.table.return_value = SupabaseDashboardQueryMock(data=None, count=None)

    response = client.get("/api/v1/super_admin/dashboard/stats")

    assert response.status_code == 200
    res_data = response.json()

    assert res_data["restricted_count"] == 0
    assert res_data["today_logins"] == 0
    assert res_data["audit_count"] == 0
    assert res_data["recent_restricted"] == []
    assert res_data["recent_audits"] == []


@patch("app.routes.super_admin.dashboard.supabase_secondary")
def test_get_dashboard_stats_internal_server_error(mock_supabase_secondary):
    # Simulate database connection crash / query error
    mock_supabase_secondary.table.side_effect = Exception("Database connection error")

    response = client.get("/api/v1/super_admin/dashboard/stats")

    assert response.status_code == 500
    assert "Failed to fetch dashboard stats: Database connection error" in response.json()["detail"]