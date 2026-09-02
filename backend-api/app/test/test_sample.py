from fastapi.testclient import TestClient
from app.main import app
import pytest

client = TestClient(app)

def test_read_root():
    response = client.get("/")
    assert response.status_code == 200
    assert response.json() == {
        "status": "online",
        "message": "Welcome to CRM & Business Control API!",
        "version": "1.0.0"
    }

def test_full_app_websocket_route_exists():
    conv_id = "non-existent-uuid"
    # Sinusubukan kumonekta sa WebSocket endpoint
    with client.websocket_connect(f"/customer/v1/chat/ws/chat/{conv_id}") as websocket:
        # Kapag nakapasok sa context manager nang walang error, ibig sabihin registered ang route!
        assert websocket is not None