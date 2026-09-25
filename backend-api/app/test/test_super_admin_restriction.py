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

class SupabaseRestrictionQueryMock:
    """
    Mock query builder object para sa Supabase fluently chained calls:
    .table().select().eq().order().maybe_single().update().execute()
    """
    def __init__(self, data=None):
        self.data = data

    def select(self, *args, **kwargs):
        return self

    def eq(self, *args, **kwargs):
        return self

    def order(self, *args, **kwargs):
        return self

    def maybe_single(self, *args, **kwargs):
        return self

    def update(self, *args, **kwargs):
        return self

    def execute(self, *args, **kwargs):
        mock_response = MagicMock()
        mock_response.data = self.data
        return mock_response


# ==========================================
# 1. TEST CASES: GET RESTRICTED USERS
# ==========================================

@patch("app.routes.super_admin.restriction.supabase_secondary")
def test_get_restricted_users_only_restricted_true(mock_supabase_secondary):
    mock_data = [
        {
            "id": 1,
            "email": "restricted1@example.com",
            "failed_attempts": 5,
            "is_restricted": True,
            "restricted_until": "2026-03-30T10:00:00Z",
            "last_attempt_at": "2026-03-30T09:30:00Z"
        }
    ]
    mock_supabase_secondary.table.return_value = SupabaseRestrictionQueryMock(data=mock_data)

    response = client.get("/api/v1/super_admin/restrictions/restricted-users?only_restricted=true")

    assert response.status_code == 200
    res_data = response.json()
    assert len(res_data) == 1
    assert res_data[0]["email"] == "restricted1@example.com"
    assert res_data[0]["is_restricted"] is True


@patch("app.routes.super_admin.restriction.supabase_secondary")
def test_get_restricted_users_all_users_flag_false(mock_supabase_secondary):
    mock_data = [
        {"id": 1, "email": "user1@example.com", "failed_attempts": 5, "is_restricted": True},
        {"id": 2, "email": "user2@example.com", "failed_attempts": 2, "is_restricted": False}
    ]
    mock_supabase_secondary.table.return_value = SupabaseRestrictionQueryMock(data=mock_data)

    response = client.get("/api/v1/super_admin/restrictions/restricted-users?only_restricted=false")

    assert response.status_code == 200
    res_data = response.json()
    assert len(res_data) == 2


@patch("app.routes.super_admin.restriction.supabase_secondary")
def test_get_restricted_users_500_server_error(mock_supabase_secondary):
    mock_supabase_secondary.table.side_effect = Exception("Database connection timeout")

    response = client.get("/api/v1/super_admin/restrictions/restricted-users")

    assert response.status_code == 500
    assert "Failed to fetch restricted users: Database connection timeout" in response.json()["detail"]


# ==========================================
# 2. TEST CASES: UNRESTRICT USER
# ==========================================

@patch("app.routes.super_admin.restriction.supabase_secondary")
def test_unrestrict_user_success(mock_supabase_secondary):
    existing_record = {
        "id": 1,
        "email": "user@example.com",
        "failed_attempts": 5,
        "is_restricted": True
    }
    
    mock_supabase_secondary.table.return_value = SupabaseRestrictionQueryMock(data=existing_record)

    response = client.post(
        "/api/v1/super_admin/restrictions/unrestrict-user",
        params={"email": " USER@EXAMPLE.COM "}  # Binawasan dapat ng whitespace at ginawang lowercase
    )

    assert response.status_code == 200
    res_data = response.json()
    assert res_data["status"] == "success"
    assert res_data["email"] == "user@example.com"
    assert "Successfully unrestricted account" in res_data["message"]


@patch("app.routes.super_admin.restriction.supabase_secondary")
def test_unrestrict_user_not_found_404(mock_supabase_secondary):
    # Walang nahanap na record sa DB
    mock_supabase_secondary.table.return_value = SupabaseRestrictionQueryMock(data=None)

    response = client.post(
        "/api/v1/super_admin/restrictions/unrestrict-user",
        params={"email": "notfound@example.com"}
    )

    assert response.status_code == 404
    assert "No restriction record found for email: notfound@example.com" in response.json()["detail"]


@patch("app.routes.super_admin.restriction.supabase_secondary")
def test_unrestrict_user_500_server_error(mock_supabase_secondary):
    mock_supabase_secondary.table.side_effect = Exception("Database query failed")

    response = client.post(
        "/api/v1/super_admin/restrictions/unrestrict-user",
        params={"email": "user@example.com"}
    )

    assert response.status_code == 500
    assert "Failed to unrestrict user: Database query failed" in response.json()["detail"]