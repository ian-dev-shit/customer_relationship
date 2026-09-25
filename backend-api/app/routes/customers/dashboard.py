from fastapi import APIRouter, HTTPException, status
from app.supabase_config.supabase import supabase_secondary
from datetime import datetime, timezone

router = APIRouter(prefix="/api/v1/customer/dashboard", tags=["Customer Dashboard"])

@router.get("/campaign-posts")
async def get_active_campaign_posts():
    """
    Kukunin ang lahat ng active campaign posts na ginawa ng Sales Agent.
    Dapat ay:
    1. is_active == True
    2. is_permanent == True O kaya kasalukuyang nasa pagitan ng start_data at end_date.
    """
    try:
        # 1. Kunin ANG LAHAT ng posts na active (parehong permanent at scheduled promos)
        response = supabase_secondary.table("campaign_posts") \
            .select("*") \
            .eq("is_active", True) \
            .order("created_at", desc=True) \
            .execute()

        posts = response.data or []
        now = datetime.now(timezone.utc)
        filtered_posts = []

        for post in posts:
            # Kung permanent post, isama agad sa listahan
            if post.get("is_permanent"):
                filtered_posts.append(post)
                continue

            # Kunin ang date fields (gamit ang 'start_data' base sa Supabase column name)
            start_date_str = post.get("start_data") or post.get("start_date")
            end_date_str = post.get("end_date")

            if start_date_str and end_date_str:
                try:
                    start_date = datetime.fromisoformat(start_date_str.replace("Z", "+00:00"))
                    end_date = datetime.fromisoformat(end_date_str.replace("Z", "+00:00"))

                    # I-verify kung nasa loob ng kasalukuyang petsa
                    if start_date <= now <= end_date:
                        filtered_posts.append(post)
                except ValueError:
                    # Kung may parsing issue sa format ng date, isama parin bilang fallback
                    filtered_posts.append(post)
            else:
                # Kung walang nakalagay na date range, isama bilang fallback
                filtered_posts.append(post)

        return filtered_posts

    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to load campaign posts: {str(e)}"
        )