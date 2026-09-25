import sys
from unittest.mock import MagicMock

sys.modules["weasyprint"] = MagicMock()

import pytest
import json
from datetime import datetime, timezone, timedelta
from fastapi.testclient import TestClient
from unittest.mock import patch
from app.main import app  # Siguraduhing nasa ilalim ito ng sys.modules mock

client = TestClient(app)

# ==========================================
# MOCK CLASSES & HELPERS
# ==========================================

class FakeUser:
    id = "mocked-uuid-1234"
    email = "christian@gmail.com"

class FakeSession:
    access_token = "mock-access-token"
    refresh_token = "mock-refresh-token"

class FakeAuthResponse:
    user = FakeUser()
    session = FakeSession()

class DynamicQueryChain:
    """Flexible mock query chain para sa iba't ibang Supabase table calls."""
    def __init__(self, data=None):
        self._data = data if data is not None else []

    def select(self, *args, **kwargs):
        return self

    def eq(self, *args, **kwargs):
        return self

    def maybe_single(self, *args, **kwargs):
        return self

    def update(self, *args, **kwargs):
        return self

    def insert(self, *args, **kwargs):
        return self

    def execute(self, *args, **kwargs):
        mock_res = MagicMock()
        mock_res.data = self._data
        return mock_res


# ==========================================
# 1. TEST: VALIDATION
# ==========================================

def test_login_validation_failed():
    payload = {
        "email": "invalid-email-format",
        "password": ""
    }
    response = client.post("/api/auth/login", json=payload)
    assert response.status_code == 422


# ==========================================
# 2. TEST: LOGIN (PRIMARY & SECONDARY AUTH)
# ==========================================

@patch("app.routes.auth.auth.log_audit")
@patch("app.routes.auth.auth.send_otp_email")
@patch("app.routes.auth.auth.redis_client")
@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.supabase")
def test_login_primary_success(
    mock_supabase_primary, mock_supabase_secondary, mock_redis, mock_send_email, mock_audit
):
    # Setup mocks
    mock_supabase_secondary.table.return_value = DynamicQueryChain(data=None) # Walang restriction
    mock_supabase_primary.auth.sign_in_with_password.return_value = FakeAuthResponse()
    mock_supabase_primary.table.return_value = DynamicQueryChain(data=[{"role": "sales"}])

    payload = {
        "email": "christian@gmail.com",
        "password": "securepassword123",
    }

    response = client.post("/api/auth/login", json=payload)

    assert response.status_code == 200
    assert response.json()["status"] == "otp_sent"
    assert response.json()["email"] == "christian@gmail.com"
    assert mock_redis.setex.called
    assert mock_send_email.called


@patch("app.routes.auth.auth.log_audit")
@patch("app.routes.auth.auth.send_otp_email")
@patch("app.routes.auth.auth.redis_client")
@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.supabase")
def test_login_secondary_customer_success(
    mock_supabase_primary, mock_supabase_secondary, mock_redis, mock_send_email, mock_audit
):
    # Primary Auth fails, Fallback to Secondary Auth (Customer)
    mock_supabase_primary.auth.sign_in_with_password.side_effect = Exception("Primary failed")
    
    mock_supabase_secondary.auth.sign_in_with_password.return_value = FakeAuthResponse()
    mock_supabase_secondary.table.return_value = DynamicQueryChain(data=[{"role": "customer"}])

    payload = {
        "email": "customer@gmail.com",
        "password": "customerpassword123",
    }

    response = client.post("/api/auth/login", json=payload)

    assert response.status_code == 200
    assert response.json()["status"] == "otp_sent"
    assert mock_redis.setex.called
    assert mock_send_email.called


# ==========================================
# 3. TEST: FAILED LOGIN & RESTRICTION LOGIC
# ==========================================

@patch("app.routes.auth.auth.is_customer_account", return_value=False)
@patch("app.routes.auth.auth.record_failed_attempt", return_value=(1, False))
@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.supabase")
def test_login_staff_failed_attempt_counter(
    mock_supabase_primary, mock_supabase_secondary, mock_record_failed, mock_is_customer
):
    # Fail both primary and secondary
    mock_supabase_primary.auth.sign_in_with_password.side_effect = Exception("Failed")
    mock_supabase_secondary.auth.sign_in_with_password.side_effect = Exception("Failed")
    mock_supabase_secondary.table.return_value = DynamicQueryChain(data=None)

    payload = {
        "email": "staff@gmail.com",
        "password": "wrongpassword",
    }

    response = client.post("/api/auth/login", json=payload)

    assert response.status_code == 401
    assert "4 attempt(s) left" in response.json()["detail"]
    assert mock_record_failed.called


@patch("app.routes.auth.auth.is_customer_account", return_value=False)
@patch("app.routes.auth.auth.record_failed_attempt", return_value=(5, True))
@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.supabase")
def test_login_staff_fifth_failed_attempt_triggers_restriction(
    mock_supabase_primary, mock_supabase_secondary, mock_record_failed, mock_is_customer
):
    mock_supabase_primary.auth.sign_in_with_password.side_effect = Exception("Failed")
    mock_supabase_secondary.auth.sign_in_with_password.side_effect = Exception("Failed")
    mock_supabase_secondary.table.return_value = DynamicQueryChain(data=None)

    payload = {
        "email": "staff@gmail.com",
        "password": "wrongpassword",
    }

    response = client.post("/api/auth/login", json=payload)

    assert response.status_code == 403
    assert "Maximum failed login attempts (5) reached" in response.json()["detail"]


@patch("app.routes.auth.auth.supabase_secondary")
def test_login_precheck_blocked_if_restricted(mock_supabase_secondary):
    # Simulate an account currently restricted in the DB
    future_time = (datetime.now(timezone.utc) + timedelta(minutes=20)).isoformat()
    restricted_data = {
        "is_restricted": True,
        "restricted_until": future_time
    }
    
    mock_supabase_secondary.table.return_value = DynamicQueryChain(data=restricted_data)

    payload = {
        "email": "locked@gmail.com",
        "password": "anypassword",
    }

    response = client.post("/api/auth/login", json=payload)

    assert response.status_code == 403
    assert "Account temporarily restricted" in response.json()["detail"]


# ==========================================
# 4. TEST: VERIFY OTP
# ==========================================

@patch("app.routes.auth.auth.log_audit")
@patch("app.routes.auth.auth.redis_client")
def test_verify_otp_success(mock_redis, mock_audit):
    cached_session = {
        "otp": "123456",
        "access_token": "valid-jwt-token",
        "refresh_token": "valid-refresh-token",
        "user_id": "user-uuid-123",
        "email": "christian@gmail.com",
        "role": "sales"
    }
    
    mock_redis.get.return_value = json.dumps(cached_session)

    response = client.post("/api/auth/verify-otp", params={"email": "christian@gmail.com", "otp_code": "123456"})
    
    assert response.status_code == 200
    data = response.json()
    assert data["access_token"] == "valid-jwt-token"
    assert data["role"] == "sales"
    assert data["token_type"] == "bearer"
    mock_redis.delete.assert_called_once_with("pre_auth:christian@gmail.com")
    assert mock_audit.called


@patch("app.routes.auth.auth.log_audit")
@patch("app.routes.auth.auth.redis_client")
def test_verify_otp_invalid_code(mock_redis, mock_audit):
    cached_session = {
        "otp": "123456",
        "access_token": "token",
        "refresh_token": "token",
        "user_id": "user-uuid-123",
        "email": "christian@gmail.com",
        "role": "sales"
    }
    mock_redis.get.return_value = json.dumps(cached_session)

    response = client.post("/api/auth/verify-otp", params={"email": "christian@gmail.com", "otp_code": "999999"})
    
    assert response.status_code == 400
    assert response.json()["detail"] == "Wrong OTP code"
    assert mock_audit.called


@patch("app.routes.auth.auth.log_audit")
@patch("app.routes.auth.auth.redis_client")
def test_verify_otp_expired_or_missing_session(mock_redis, mock_audit):
    mock_redis.get.return_value = None

    response = client.post("/api/auth/verify-otp", params={"email": "christian@gmail.com", "otp_code": "123456"})
    
    assert response.status_code == 400
    assert "Expired na o walang nahanap" in response.json()["detail"]
    assert mock_audit.called