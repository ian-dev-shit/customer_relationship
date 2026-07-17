
# Initialize supabase client

from supabase import create_client, Client
from app.config.config import settings
import redis

# Gawa ng single connection instance kay supabase
supabase: Client = create_client(settings.SUPABASE_URL, settings.SUPABASE_KEY)

# Connection sa redis
redis_client = redis.Redis.from_url(settings.REDIS_URL, decode_responses=True)