from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.routes.auth import auth_router
from app.routes.portal import router as portal_router
from app.routes.leads import router as leads_router

# 1. I-initialize ang FastAPI app
app = FastAPI(
    title="CRM & Business Control API",
    description="Backend API for Customer Relationship and Business Control System",
    version="1.0.0"
)

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

# 3. Simple Root Route (Ito ang babasahin ng test file natin para pumasa ang CI)
@app.get("/")
def read_root():
    return {
        "status": "online",
        "message": "Welcome to CRM & Business Control API!",
        "version": "1.0.0"
    }

