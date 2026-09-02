import json
import os
from google import genai
from google.genai import types
from app.supabase_config.supabase import gemini_client
from app.schemas.customer import ExtractedDetails, AIAnalysisResult
from typing import Optional, Dict, Any

async def analyze_message_and_extracted_details(user_message: str, current_details: Dict[str, Any]) -> dict:

    prompt = f"""
    You are a helpful logistics customer service assistant.
    Current extracted details: {current_details}
    Customer message: "{user_message}"

    1. Reply politely to the customer in Taglish/English.
    2. Analyze the sentiment ("positive", "neutral", "negative").
    3. Update the extracted details (origin, destination, service_type, cargo_details).
    4. Set "is_complete" to true ONLY IF origin, destination, and cargo_details are all provided.
    5. Set "force_handoff" to true if the customer is frustrated/angry (negative sentiment) OR explicitly asking for a human agent.
    """

    try:
        response = gemini_client.models.generate_content(
            model='gemini-3.6-flash',
            contents=prompt,
            config=types.GenerateContentConfig(
                response_mime_type="application/json",
                response_schema=AIAnalysisResult,
                temperature=0.2, # Para di maubos tubig
            ),
        )

        # Success na na parse ang gemini bilang json matching sa schema
        return json.loads(response.text)

    except Exception as e:
        print(f"Gemini error: {e}")
        # Fall back error
        return {
            "reply": "Pasensya na po, maaari niyo bang ulitin ang inyong mensahe?",
            "sentiment": "neutral",
            "updated_details": current_details,
            "is_complete": False,
            "force_handoff": False
        } 