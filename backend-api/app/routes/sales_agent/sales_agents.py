from fastapi import APIRouter, HTTPException
from datetime import datetime, timezone
import pandas as pd
from app.supabase_config.supabase import supabase_secondary

# Router definition for FastAPI
router = APIRouter(
    prefix="/api/sales",
    tags=["Sales Agent"]
)

@router.get("/priority-followups")
async def get_priority_followups():
    try:
        # Kunin ang active inquiries 
        res = supabase_secondary.table('inquiries').select('*').neq('status', 'closed_won').neq('status', 'closed_lost').execute()
        inquiries = res.data or []

        now = datetime.now(timezone.utc)
        followup_list = []

        for inq in inquiries:
            created_at_str = inq.get('created_at')
            if not created_at_str:
                continue

            created_at = pd.to_datetime(created_at_str, utc=True)
            days_idle = (now - created_at).days
            
            status = str(inq.get('status', '')).lower().strip()
            amount = float(inq.get('estimated_amount') or inq.get('agreed_amount') or 0.0)

            # ML / Rule-based Weighting System
            status_weight = 2.5 if status == 'new_inquiry' else (2.0 if status == 'quote_sent' else 1.0)
            score = (days_idle * 1.8) + (status_weight * 2.0) + (amount / 1000.0)

            if days_idle >= 2 or score > 4.0:
                followup_list.append({
                    "id": inq.get('id'),
                    "inquiry_code": inq.get('inquiry_code') or 'INQ-XXXX',
                    "client_name": inq.get('company_name') or inq.get('contact_person') or 'Unknown Client',
                    "service_type": inq.get('service_type', 'General Inquiry'),
                    "status": status.replace('_', ' ').title(),
                    "days_idle": max(1, days_idle),
                    "score": round(score, 1),
                    "priority_level": "CRITICAL" if days_idle >= 3 else "HIGH"
                })

        # Sort based on highest ML score
        followup_list.sort(key=lambda x: x['score'], reverse=True)

        return {"status": "success", "data": followup_list[:5]}

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Service Error: {str(e)}")


@router.get("/leads-and-routes")
async def get_leads_and_routes():
    try:
        # Fetch inquiries data
        res = supabase_secondary.table('inquiries').select('status, service_type, origin, destination').execute()
        inquiries = res.data or []

        if not inquiries:
            return {
                "status": "success", 
                "lead_statuses": [], 
                "top_routes": []
            }

        df = pd.DataFrame(inquiries)
        total_inquiries = len(df)

        
        # 1. LEADS MANAGEMENT 
        df['status_clean'] = df['status'].astype(str).str.lower().str.strip()
        
        # Pagbubukod-bukod batay sa eksaktong 6 na statuses
        status_definitions = [
            ("New Inquiry", ['new_inquiry', 'new', 'inquiry']),
            ("Qualifying", ['qualifying', 'qualification', 'qualify']),
            ("Quote Sent", ['quote_sent', 'quoted', 'quote']),
            ("Negotiation", ['negotiation', 'negotiating', 'discussion']),
            ("Closed Won", ['closed_won', 'won', 'booked', 'approved']),
            ("Closed Lost", ['closed_lost', 'lost', 'rejected', 'cancelled'])
        ]

        lead_statuses = []
        for label, keywords in status_definitions:
            pattern = '|'.join(keywords)
            count = len(df[df['status_clean'].str.contains(pattern, na=False)])
            pct = int(round((count / total_inquiries) * 100)) if total_inquiries > 0 else 0
            
            lead_statuses.append({
                "label": label,
                "count": count,
                "percentage": pct
            })

        
        # 2. TOP FREIGHT ROUTES
        def parse_route(row):
            origin = row.get('origin')
            dest = row.get('destination')
            if origin and dest:
                return f"{origin} → {dest}"
            return str(row.get('service_type', 'General Route'))

        df['route_name'] = df.apply(parse_route, axis=1)
        route_counts = df['route_name'].value_counts().reset_index()
        route_counts.columns = ['route', 'count']

        top_routes = []
        for rank, row in route_counts.head(5).iterrows():
            pct = int(round((row['count'] / total_inquiries) * 100)) if total_inquiries > 0 else 0
            top_routes.append({
                "rank": rank + 1,
                "route": row['route'],
                "percentage": f"{pct}%"
            })

        return {
            "status": "success",
            "lead_statuses": lead_statuses,
            "top_routes": top_routes
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))



@router.get("/top-customers")
async def get_top_customers():
    try:
        # Kunin ang customers galing sa database
        res = supabase_secondary.table('customers').select('company_name, contact_person, total_bookings, tier').order('total_bookings', desc=True).limit(5).execute()
        customers = res.data or []

        formatted_customers = []
        for cust in customers:
            contact = (cust.get('contact_person') or '').strip()
            
            initials = ''
            if contact:
                parts = contact.split()
                if len(parts) >= 2:
                    initials = f"{parts[0][0]}{parts[1][0]}".upper()
                else:
                    initials = contact[:2].upper()
            else:
                initials = (cust.get('company_name') or 'CU')[:2].upper()

            formatted_customers.append({
                "company_name": cust.get('company_name') or 'N/A',
                "contact_person": contact or 'N/A',
                "initials": initials,
                "total_bookings": cust.get('total_bookings') or 0,
                "tier": (cust.get('tier') or 'BRONZE').upper()
            })

        return {"status": "success", "customers": formatted_customers}

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Top Customers Error: {str(e)}")