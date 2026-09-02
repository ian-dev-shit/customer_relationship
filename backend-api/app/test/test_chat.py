import pytest
from unittest.mock import AsyncMock, patch, MagicMock
from fastapi import FastAPI
from fastapi.testclient import TestClient

from app.routes.chat.chat import router as customer_router

# Setup isolated test app instance
app_instance = FastAPI()
app_instance.include_router(customer_router)

client = TestClient(app_instance)

# 1. Test Sentiment Analysis Unit Function
@pytest.mark.asyncio
@patch("app.service.sintement.gemini_client")  # Eto ang tamang target na i-patch
async def test_sentiment_analysis_logic(mock_gemini_client):
    from app.service.sintement import analyze_message_and_extracted_details

    # Mock response object mula kay Gemini
    mock_response = MagicMock()
    mock_response.text = """{
        "reply": "Kailan po ang target date ng delivery?",
        "sentiment": "neutral",
        "updated_details": {"origin": "Manila", "destination": "Cebu"},
        "is_complete": false,
        "force_handoff": false
    }"""

    # Synchronous call ang generate_content kaya return_value lang gagamitin (walang AsyncMock)
    mock_gemini_client.models.generate_content.return_value = mock_response

    result = await analyze_message_and_extracted_details("Manila to Cebu", {})

    assert result["sentiment"] == "neutral"
    assert result["updated_details"]["origin"] == "Manila"
    assert result["is_complete"] is False

# 3. Test Get Chat Messages Endpoint
@patch("app.routes.chat.chat.supabase_secondary")
def test_get_chat_messages_endpoint(mock_supabase):
    conv_id = "test-conv-uuid"

    mock_supabase.table().select().eq().order().execute.return_value.data = [
        {
            "id": "msg-1",
            "conversation_id": conv_id,
            "sender_type": "customer",
            "sender_id": "cust-1",
            "message": "Hello",
            "created_at": "2026-08-27T17:31:00+00:00"
        }
    ]

    response = client.get(f"/agent/v1/chat/messages/{conv_id}")

    assert response.status_code == 200
    data = response.json()
    assert len(data) == 1
    assert data[0]["message"] == "Hello"
    assert data[0]["formatted_time"] == "17:31"