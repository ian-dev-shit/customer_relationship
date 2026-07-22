import os

def pytest_configure(config):
    """Piliting i-load ang dummy env variables BAGO pa i-collect ng pytest ang mga modules."""
    os.environ.setdefault("SUPABASE_URL", "https://mock.supabase.co")
    os.environ.setdefault("SUPABASE_KEY", "mock-supabase-key")
    os.environ.setdefault("SUPABASE_JWT_SECRET", "mock-jwt-secret")
    os.environ.setdefault("UPSTASH_REDIS_REST_URL", "https://mock-redis.upstash.io")
    os.environ.setdefault("UPSTASH_REDIS_REST_TOKEN", "mock-redis-token")
    os.environ.setdefault("SMTP_SENDER", "test@example.com")
    os.environ.setdefault("SMTP_PASSWORD", "mock-smtp-password")