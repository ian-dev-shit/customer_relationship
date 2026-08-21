
# Configuraton

import os
from pydantic_settings import BaseSettings, SettingsConfigDict

class Settings(BaseSettings):
    SUPABASE_URL: str
    SUPABASE_KEY: str
    SUPABASE_JWT_SECRET: str
    UPSTASH_REDIS_REST_URL: str
    UPSTASH_REDIS_REST_TOKEN: str
    SMTP_SENDER: str
    SMTP_PASSWORD: str

    # SECOND ACC
    SUPABASE_URL_2: str
    SUPABASE_KEY_2: str
    SUPABASE_SERVICE_KEY_2: str
    

    #Automatic na babasahin ang .env file sa root folder
    model_config = SettingsConfigDict(
        env_file=".env", 
        env_file_encoding="utf-8",
        extra="ignore" # I-ignore ang ibang variables na wala sa class na ito
    )
settings = Settings()
