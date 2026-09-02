from datetime import datetime, timedelta, timezone
from app.supabase_config.supabase import supabase_secondary
from app.service.websocket_manager import manager

async def check_5hr_agent_inactivity():
    five_hours_ago = (datetime.now(timezone.utc) - timedelta(hours=5)).isoformat()

    # Hanapin ang conversations na 'pending_sales' o 'agent_handling' na hindi na-update sa nakalipas na 5 oras

    response = supabase_secondary.table("conversations")\
        .select("id")\
        .in_("status", ["pending_sales", "agent_handling"])\
        .lt("updated_at", five_hours_ago)\
        .execute()

    stale_conversations = response.data

    for conv in stale_conversations:
        conv_id = conv["id"]

        # 1. Ibalik ang status sa 'ai_handling'
        supabase_secondary.table("conversations")\
            .update({
                "status": "ai_handling",
                "updated_at": datetime.now(timezone.utc).isoformat()
            })\
            .eq("id", conv_id)\
            .execute()

        # 2. Mag-send ng message si AI sa Customer
        ai_fallback_msg = supabase_secondary.table("messages").insert({
            "conversation_id": conv_id,
            "sender_type": "ai",
            "message": "Pasensya na sa delay, mejo busy pa ang aming Sales Agent. Nais mo bang ako muna ang tumulong sa iba mo pang katanungan?"
        }).execute().data[0]

        # Broadcast sa WebSocket kung online pa ang customer
        await manager.broadcast(conv_id, ai_fallback_msg)