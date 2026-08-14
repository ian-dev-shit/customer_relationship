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

@patch("app.routes.auth.supabase")
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

@patch("app.routes.auth.supabase")
@patch("app.routes.auth.redis_client")
@patch("app.routes.auth.send_otp_email")
def test_login_request_success(mock_send_email, mock_redis, mock_supabase):
    # 1. Purong Python Class para sa Auth Response
    class FakeUser:
        id = "mocked-uuid-1234"
        email = "christian@gmail.com"

    class FakeSession:
        access_token = "mock-access-token"
        refresh_token = "mock-refresh-token"

    class FakeAuthResponse:
        user = FakeUser()
        session = FakeSession()

    mock_supabase.auth.sign_in_with_password.return_value = FakeAuthResponse()

    # 2. Fake Execute Response (Naka-List structure para ligtas sa data[0] access)
    class FakeExecuteResponse:
        data = [{"role": "staff_sla"}]

    class FakeQueryChain:
        def select(self, *args, **kwargs): return self
        def eq(self, *args, **kwargs): return self
        def single(self, *args, **kwargs): return self
        def maybe_single(self, *args, **kwargs): return self
        def execute(self, *args, **kwargs): return FakeExecuteResponse()

    # I-bind ang query chain sa mock table
    mock_supabase.table.return_value = FakeQueryChain()

    # 3. Request Payload
    payload = {
        "email": "christian@gmail.com",
        "password": "securepassword123"
    }
    
    response = client.post("/api/auth/login", json=payload)

    # 4. Assertions
    assert response.status_code == 200
    assert response.json()["status"] == "otp_sent"
    assert mock_redis.setex.called
    assert mock_send_email.called
# ==========================================
# 4. TEST: VERIFY OTP (WITH MOCKING)
# ==========================================

@patch("app.routes.auth.redis_client")
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


@patch("app.routes.auth.redis_client")
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