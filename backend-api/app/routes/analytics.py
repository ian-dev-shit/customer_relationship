from typing import Dict, Any
from fastapi import APIRouter, HTTPException
from app.supabase_config.supabase import supabase_secondary
from app.service.analytics_service import process_sales_analytics, get_pipeline_dashboard, get_growth_percentage
from app.service.bi_analytics import BIAnalyticsService

router = APIRouter(
    prefix="/api/v1/analytics",
    tags=["Analytics"]
)

@router.get("/dashboard")
async def get_sales_dashboard_analytics():
    try:
        # Fetch data mula sa 3 tables
        inquiries_res = supabase_secondary.table("inquiries").select("*").execute()
        tickets_res = supabase_secondary.table("tickets").select("*").execute()
        bookings_res = supabase_secondary.table("bookings").select("*").execute()

        raw_inquiries = inquiries_res.data if inquiries_res.data else []
        raw_tickets = tickets_res.data if tickets_res.data else []
        raw_bookings = bookings_res.data if bookings_res.data else []

        result = process_sales_analytics(raw_inquiries, raw_tickets, raw_bookings)

        return {
            "status": "success",
            "data": result
        }

    except Exception as e:
        return {
            "status": "error",
            "message": str(e)
        }

@router.get("/sales-dashboard")
async def get_sales_dashboard_analytics():
    """
    Endpoint para sa Pipeline Area Chart, Stage Counts, at Top Customers.
    """
    try:
        result = get_pipeline_dashboard(supabase_secondary)

        if not isinstance(result, dict):
            raise ValueError("Invalid response structure from service.")

        return result

    except Exception as e:
        # Magbalik ng structured error JSON 
        raise HTTPException(
            status_code=500, 
            detail={
                "status": "error",
                "message": f"Pipeline Analytics Error: {str(e)}"
            }
        )

# Card: 1
@router.get("/gross-revenue")
async def get_gross_revenue():
    """
    Kinukuha ang Gross Revenue Analytics base sa 'closed_won' inquiries.
    Nag-o-output ng total revenue, formatted currency, at Matplotlib spline chart (Base64).
    """
    try:
        data = BIAnalyticsService.get_closed_won_revenue_analytics()
        return data
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# Card 2: Service Types Breakdown
@router.get("/service-types")
async def get_service_types():
    try:
        return BIAnalyticsService.get_service_types_analytics()
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# Card 3: Top Routes Breakdown
@router.get("/top-routes")
async def get_top_routes():
    try:
        return BIAnalyticsService.get_top_routes_analytics()
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@router.get("/shipments-closed")
async def get_shipments_closed():
    try:
        return BIAnalyticsService.get_shipments_closed_analytics()
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@router.get("/win-loss-service")
async def get_win_loss_service_analytics() -> Dict[str, Any]:
    try:
        result = BIAnalyticsService.get_win_loss_by_service_analytics()
        return result
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@router.get("/service-won-distribution")
async def get_service_won_distribution() -> Dict[str, Any]:
    try:
        return BIAnalyticsService.get_service_won_distribution_analytics()
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@router.get("/weight-class-win-loss")
async def get_weight_class_win_loss() -> Dict[str, Any]:
    try:
        return BIAnalyticsService.get_weight_class_win_loss_analytics()
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))