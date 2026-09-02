import pytest
from fastapi.testclient import TestClient
from unittest.mock import MagicMock, patch
import json
from app.main import app

client = TestClient(app)

# ==========================================
# 1. TEST: VALIDATION EMAIL AT PASSWORD
# ==========================================

def test_signup_validation_failed():
    payload = {
        "email": "invalid-email-format",
        "password": "123",
        "first_name": "Christian",
        "last_name": "Developer"
    }
    response = client.post("/api/auth/signup", json=payload)
    assert response.status_code == 422
    errors = response.json()["detail"]
    error_fields = [err["loc"][-1] for err in errors]
    assert "email" in error_fields
    assert "password" in error_fields


def test_login_validation_failed():
    payload = {
        "email": "christian_at_gmail.com",
        "password": "short"
    }
    
    response = client.post("/api/auth/login", json=payload)
    assert response.status_code == 422


# ==========================================
# 2. TEST: SUCCESSFUL SIGNUP (WITH MOCKING)
# ==========================================

@patch("app.routes.auth.auth.supabase")
def test_signup_success(mock_supabase):
    mock_user = MagicMock()
    mock_user.id = "mocked-uuid-1234"
    mock_response = MagicMock()
    mock_response.user = mock_user
    mock_supabase.auth.sign_up.return_value = mock_response

    payload = {
        "email": "christian@gmail.com",
        "password": "securepassword123",
        "first_name": "Christian",
        "last_name": "Developer"
    }
    response = client.post("/api/auth/signup", json=payload)
    assert response.status_code == 201
    assert response.json()["message"] == "Registration successful!"
    assert response.json()["user_id"] == "mocked-uuid-1234"

# ==========================================
# 3. TEST: REQUEST OTP / LOGIN (WITH MOCKING)
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


class FakeExecuteResponse:
    data = [{"role": "sales"}]


class FakeQueryChain:

    def select(self, *args, **kwargs):
        return self

    def eq(self, *args, **kwargs):
        return self

    def execute(self, *args, **kwargs):
        return FakeExecuteResponse()


# 3a. Primary Auth Login Success (Admin / Sales)
@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.supabase")
@patch("app.routes.auth.auth.redis_client")
@patch("app.routes.auth.auth.send_otp_email")
def test_login_primary_success(
    mock_send_email, mock_redis, mock_supabase_primary, mock_supabase_secondary
):
    # Primary Auth succeeds
    mock_supabase_primary.auth.sign_in_with_password.return_value = (
        FakeAuthResponse()
    )
    mock_supabase_primary.table.return_value = FakeQueryChain()

    payload = {
        "email": "christian@gmail.com",
        "password": "securepassword123",
    }

    response = client.post("/api/auth/login", json=payload)

    assert response.status_code == 200
    assert response.json()["status"] == "otp_sent"
    assert mock_redis.setex.called
    assert mock_send_email.called


# 3b. Secondary Auth Fallback Success (Customer Portal)
@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.supabase")
@patch("app.routes.auth.auth.redis_client")
@patch("app.routes.auth.auth.send_otp_email")
def test_login_secondary_fallback_success(
    mock_send_email, mock_redis, mock_supabase_primary, mock_supabase_secondary
):
    # Primary Auth fails, causing fallback to Secondary
    mock_supabase_primary.auth.sign_in_with_password.side_effect = Exception(
        "Invalid primary credentials"
    )

    class CustomerExecuteResponse:
        data = [{"role": "customer"}]

    class CustomerQueryChain:

        def select(self, *args, **kwargs):
            return self

        def eq(self, *args, **kwargs):
            return self

        def execute(self, *args, **kwargs):
            return CustomerExecuteResponse()

    mock_supabase_secondary.auth.sign_in_with_password.return_value = (
        FakeAuthResponse()
    )
    mock_supabase_secondary.table.return_value = CustomerQueryChain()

    payload = {
        "email": "customer@gmail.com",
        "password": "securepassword123",
    }

    response = client.post("/api/auth/login", json=payload)

    assert response.status_code == 200
    assert response.json()["status"] == "otp_sent"
    assert mock_redis.setex.called
    assert mock_send_email.called


# 3c. Both Auth Fail (Unauthorized 401)
@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.supabase")
def test_login_both_auth_failed(mock_supabase_primary, mock_supabase_secondary):
    mock_supabase_primary.auth.sign_in_with_password.side_effect = Exception(
        "Primary Auth failed"
    )
    mock_supabase_secondary.auth.sign_in_with_password.side_effect = Exception(
        "Secondary Auth failed"
    )

    payload = {
        "email": "wrong@gmail.com",
        "password": "wrongpassword",
    }

    response = client.post("/api/auth/login", json=payload)

    assert response.status_code == 401
    assert response.json()["detail"] == "Invalid email or password"
# ==========================================
# 4. TEST: VERIFY OTP (WITH MOCKING)
# ==========================================

@patch("app.routes.auth.auth.redis_client")
def test_login_verify_success(mock_redis):
    cached_session = {
        "otp": "123456",
        "access_token": "valid-jwt-token",
        "refresh_token": "valid-refresh-token",
        "user_id": "user-uuid",
        "email": "christian@gmail.com",
        "role": "customer"
    }
    
    mock_redis.get.return_value = json.dumps(cached_session)
    mock_redis.return_value = json.dumps(cached_session)

    response = client.post("/api/auth/verify-otp", params={"email": "christian@gmail.com", "otp_code": "123456"})
    assert response.status_code == 200
    data = response.json()
    assert data["access_token"] == "valid-jwt-token"
    assert data["role"] == "customer"
    mock_redis.delete.assert_called_once_with("pre_auth:christian@gmail.com")


@patch("app.routes.auth.auth.redis_client")
def test_login_verify_wrong_otp(mock_redis):
    cached_session = {
        "otp": "123456",
        "access_token": "token",
        "refresh_token": "token",
        "user_id": "uid",
        "email": "christian@gmail.com",
        "role": "customer"
    }
    mock_redis.get.return_value = json.dumps(cached_session)
    mock_redis.return_value = json.dumps(cached_session)

    response = client.post("/api/auth/verify-otp", params={"email": "christian@gmail.com", "otp_code": "999999"})
    assert response.status_code == 400
    assert response.json()["detail"] == "Wrong OTP code"