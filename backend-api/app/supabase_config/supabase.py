
# Initialize supabase client

from supabase import create_client, Client
from app.config.config import settings
from upstash_redis import Redis
from google import genai

# Gawa ng single connection instance kay supabase
supabase: Client = create_client(settings.SUPABASE_URL, settings.SUPABASE_KEY)
supabase_secondary: Client = create_client(settings.SUPABASE_URL_2, settings.SUPABASE_SERVICE_KEY_2)


# Connection sa redis
redis_client = Redis(
    url=settings.UPSTASH_REDIS_REST_URL, 
    token=settings.UPSTASH_REDIS_REST_TOKEN
)

gemini_client = genai.Client(api_key=settings.GEMINI_API_KEY)