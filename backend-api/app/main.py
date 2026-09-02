from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.routes.auth.auth import auth_router
from app.routes.portal import router as portal_router
from app.routes.sales_agent.leads import router as leads_router
from app.routes.sales_agent.sales_agents import router as sales_agent
from app.routes.sales_agent.shipment_booking import router as shipment_booking_router
from app.routes.sales_agent.campaign_router import router as campaign_router
from app.routes.admin.admin import router as admin_router
from app.routes.customers.customers import router as customer_router
from app.routes.chat.chat import router as chat_router
from app.routes.analytics import router as analytic_router
from app.service.inactivity_checker import check_5hr_agent_inactivity
from apscheduler.schedulers.asyncio import AsyncIOScheduler

# 1. I-initialize ang FastAPI app
app = FastAPI(
    title="CRM & Business Control API",
    description="Backend API for Customer Relationship and Business Control System",
    version="1.0.0"
)

# Initialize Scheduler
scheduler = AsyncIOScheduler()

@app.on_event("startup")
def start_scheduler():
    # Tumatakbo bawat 15 minuto para check kung may 5 hours nang walang sagot ang Sales
    scheduler.add_job(check_5hr_agent_inactivity, "interval", minutes=15)
    scheduler.start()

# 2. CORS Middleware 
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Sa production, palitan  ito ng mismong URL ng PHP frontend 
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Dito natin lalagay mga routes
app.include_router(auth_router)
app.include_router(portal_router)
app.include_router(leads_router)
app.include_router(admin_router)
app.include_router(customer_router)
app.include_router(chat_router)
app.include_router(analytic_router)
app.include_router(sales_agent)
app.include_router(shipment_booking_router)
app.include_router(campaign_router)


# 3. Simple Root Route 
@app.get("/")
def read_root():
    return {
        "status": "online",
        "message": "Welcome to CRM & Business Control API!",
        "version": "1.0.0"
    }

