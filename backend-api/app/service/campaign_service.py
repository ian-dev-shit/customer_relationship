import uuid
from typing import List, Dict, Any, Optional
from datetime import datetime, timezone
from fastapi import UploadFile
from app.supabase_config.supabase import supabase_secondary

class CampaignService:

    @staticmethod
    async def create_campaign(
        title: str,
        description: Optional[str],
        is_permanent: bool,
        start_date: Optional[str],
        end_date: Optional[str],
        image_file: UploadFile,
        agent_id: Optional[str] = None
    ) -> Dict[str, Any]:
        try:
            # 1. Upload Poster Image sa Supabase Storage
            file_ext = image_file.filename.split(".")[-1]
            file_name = f"{uuid.uuid4()}.{file_ext}"
            file_content = await image_file.read()

            upload_response = supabase_secondary.storage.from_("campaign-posters").upload(
                file_name,
                file_content,
                file_options={"content-type": image_file.content_type}
            )

            # Kunin ang Public Image URL
            image_url = supabase_secondary.storage.from_("campaign-posters").get_public_url(file_name)

            # 2. Prepare Database Record
            payload = {
                "title": title,
                "description": description,
                "image_url": image_url,
                "is_permanent": is_permanent,
                "created_by": agent_id,
                "is_active": True
            }

            if not is_permanent:
                payload["start_date"] = start_date if start_date else datetime.now(timezone.utc).isoformat()
                payload["end_date"] = end_date
            else:
                payload["start_date"] = datetime.now(timezone.utc).isoformat()
                payload["end_date"] = None

            # 3. Save Record to Database
            db_response = supabase_secondary.table("campaign_posts").insert(payload).execute()

            return {"status": "success", "data": db_response.data}

        except Exception as e:
            raise Exception(f"Failed to create campaign post: {str(e)}")

    @staticmethod
    def get_active_campaigns_for_customer() -> List[Dict[str, Any]]:
        try:
            now_iso = datetime.now(timezone.utc).isoformat()

            # Query: Kumuha ng Active Records na alinman sa:
            # 1. Permanent (is_permanent = True)
            # 2. Limited-time (end_date > kasalukuyang oras)
            response = (
                supabase_secondary.table("campaign_posts")
                .select("*")
                .eq("is_active", True)
                .order("created_at", desc=True)
                .execute()
            )

            all_campaigns = response.data or []
            active_campaigns = []

            for item in all_campaigns:
                if item.get("is_permanent"):
                    active_campaigns.append(item)
                else:
                    end_date_str = item.get("end_date")
                    if end_date_str:
                        # Auto-filter out ng mga lumagpas na sa expiration time
                        end_dt = datetime.fromisoformat(end_date_str.replace('Z', '+00:00'))
                        if end_dt > datetime.now(timezone.utc):
                            active_campaigns.append(item)

            return active_campaigns

        except Exception as e:
            raise Exception(f"Failed to fetch customer campaigns: {str(e)}")