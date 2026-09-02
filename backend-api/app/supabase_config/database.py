from motor.motor_asyncio import AsyncIOMotorClient
from app.config.config import settings


# Single Client Instance
mongo_client = AsyncIOMotorClient(settings.MONGODB_URL)

db = mongo_client.logistics_portal
chat_collection = db.chat_histories