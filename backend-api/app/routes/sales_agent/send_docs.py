# app/routes/sales_agent/quotation.py

import uuid
from datetime import datetime, timezone
from typing import List, Optional
from io import BytesIO
from fastapi import APIRouter, HTTPException, BackgroundTasks, Query
from jinja2 import Environment, FileSystemLoader
from weasyprint import HTML
from app.supabase_config.supabase import supabase_secondary
from app.middleware.quotation import send_quotation_email
from app.schemas.quotation import QuotationCreate, QuotationResponse

router = APIRouter(
    prefix="/api/sales/quotations",
    tags=["Sales Agent - Quotations"]
)


jinja_env = Environment(loader=FileSystemLoader("app/templates"))


def generate_pdf_bytes(quotation_data: dict) -> bytes:
    """I-re-render ang HTML template papuntang PDF bytes gamit ang WeasyPrint."""
    template = jinja_env.get_template("quotation_pdf.html")
    rendered_html = template.render(**quotation_data)
    return HTML(string=rendered_html).write_pdf()


@router.post("/send", response_model=QuotationResponse)
async def create_and_send_quotation(
    payload: QuotationCreate,
    background_tasks: BackgroundTasks
):
    try:
        # 1. Compute totals
        subtotal = sum(item.quantity * item.unit_price for item in payload.items)
        total_amount = subtotal + payload.tax_amount - payload.discount_amount
        
        # 2. Generate Unique Quote Code/Number
        quote_number = f"QT-{datetime.now().strftime('%Y')}-{uuid.uuid4().hex[:5].upper()}"

        # 3. Prepare PDF Template Data
        items_with_total = [
            {
                "description": item.description,
                "quantity": item.quantity,
                "unit_price": item.unit_price,
                "total_price": item.quantity * item.unit_price
            }
            for item in payload.items
        ]

        pdf_context = {
            "quote_number": quote_number,
            "customer_name": payload.customer_name,
            "company_name": payload.company_name or "N/A",
            "customer_email": payload.customer_email,
            "origin": payload.origin,
            "destination": payload.destination,
            "service_type": payload.service_type or "Freight",
            "date_issued": datetime.now().strftime("%B %d, %Y"),
            "valid_until": payload.valid_until.strftime("%B %d, %Y") if payload.valid_until else "N/A",
            "items": items_with_total,
            "subtotal": subtotal,
            "tax_amount": payload.tax_amount,
            "discount_amount": payload.discount_amount,
            "total_amount": total_amount,
        }

        # 4. Generate PDF bytes via WeasyPrint
        pdf_bytes = generate_pdf_bytes(pdf_context)

        # 5. Upload PDF sa Supabase Storage ('quotations' bucket)
        pdf_filename = f"{quote_number}.pdf"
        storage_path = f"pdf/{pdf_filename}"
        
        supabase_secondary.storage.from_("quotations").upload(
            file=pdf_bytes,
            path=storage_path,
            file_options={"content-type": "application/pdf"}
        )
        
        # Kunin ang public URL ng PDF sa Supabase
        public_pdf_url = supabase_secondary.storage.from_("quotations").get_public_url(storage_path)

        # 6. Save Quotation Header sa 'quotations' table
        quotation_data = {
            "inquiry_id": str(payload.inquiry_id),
            "quote_number": quote_number,
            "version": 1,
            "subtotal": subtotal,
            "tax_amount": payload.tax_amount,
            "discount_amount": payload.discount_amount,
            "total_amount": total_amount,
            "status": "sent",
            "pdf_url": public_pdf_url,
            "valid_until": payload.valid_until.isoformat() if payload.valid_until else None,
            "created_by": str(payload.created_by) if payload.created_by else None
        }

        quote_res = supabase_secondary.table("quotations").insert(quotation_data).execute()
        
        if not quote_res.data:
            raise HTTPException(status_code=500, detail="Failed to insert quotation record.")

        created_quote = quote_res.data[0]
        quotation_id = created_quote["id"]

        # 7. Save Line Items sa 'quotation_items' table
        items_to_insert = [
            {
                "quotation_id": quotation_id,
                "description": item["description"],
                "quantity": item["quantity"],
                "unit_price": item["unit_price"],
                "total_price": item["total_price"]
            }
            for item in items_with_total
        ]
        
        supabase_secondary.table("quotation_items").insert(items_to_insert).execute()

        # 8. Update status ng inquiry papuntang 'quote_sent'
        supabase_secondary.table("inquiries").update({"status": "quote_sent"}).eq("id", str(payload.inquiry_id)).execute()

        # 9. Isalang sa Background Task ang email sending gamit ang middleware function mo
        background_tasks.add_task(
            send_quotation_email,
            to_email=payload.customer_email,
            company_name=payload.company_name or payload.customer_name,
            pdf_bytes=pdf_bytes,
            filename=pdf_filename,
            origin=payload.origin,
            destination=payload.destination,
            service_type=payload.service_type,
            estimated_amount=total_amount,
            inquiry_code=quote_number
        )

        return created_quote

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@router.get("", response_model=None)
@router.get("/")
async def list_sent_quotations(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100),
    search: Optional[str] = Query(None)
):
    try:
        offset = (page - 1) * limit

        # 1. Kunin muna ang quotations data kasama ang foreign key relation sa inquiries
        query = supabase_secondary.table("quotations").select(
            "*, inquiries!inner(contact_person, company_name, email, service_type, origin, destination)",
            count="exact"
        )

        # 2. Kung may search term, i-filter muna ang quote_number sa DB o kaya sa matching inquiries
        search_term = search.strip() if search else ""

        if search_term:
            # Hanapin muna ang matching inquiry IDs kung nag-search ng company name o contact person
            inq_match = supabase_secondary.table("inquiries").select("id").or_(
                f"company_name.ilike.%{search_term}%,contact_person.ilike.%{search_term}%"
            ).execute()
            
            matching_inquiry_ids = [item["id"] for item in (inq_match.data or [])]

            if matching_inquiry_ids:
                # Search sa quote_number OR sa matching inquiry_ids
                inq_ids_str = ",".join(matching_inquiry_ids)
                query = query.or_(f"quote_number.ilike.%{search_term}%,inquiry_id.in.({inq_ids_str})")
            else:
                # Search lang sa quote_number
                query = query.ilike("quote_number", f"%{search_term}%")

        # 3. Paginate at Execute
        res = query.order("created_at", desc=True).range(offset, offset + limit - 1).execute()

        quotations = res.data or []
        total_count = res.count or 0

        # 4. Format the response para sa frontend JS
        now_utc = datetime.now(timezone.utc)
        formatted_list = []

        for q in quotations:
            inquiry_data = q.get("inquiries") or {}

            valid_until_raw = q.get("valid_until")
            is_valid = False

            if valid_until_raw:
                try:
                    valid_until_dt = datetime.fromisoformat(valid_until_raw.replace("Z", "+00:00"))
                    is_valid = valid_until_dt > now_utc
                except ValueError:
                    is_valid = False

            db_status = q.get("status", "sent").lower()
            computed_status = db_status
            if db_status == "sent" and not is_valid:
                computed_status = "expired"

            formatted_list.append({
                "id": q.get("id"),
                "quote_number": q.get("quote_number"),
                "inquiry_id": q.get("inquiry_id"),
                "contact_person": inquiry_data.get("contact_person") or "N/A",
                "company_name": inquiry_data.get("company_name") or inquiry_data.get("contact_person") or "N/A",
                "customer_email": inquiry_data.get("email") or "N/A",
                "service_type": inquiry_data.get("service_type") or "Freight",
                "origin": inquiry_data.get("origin") or "N/A",
                "destination": inquiry_data.get("destination") or "N/A",
                "total_amount": float(q.get("total_amount") or 0.0),
                "subtotal": float(q.get("subtotal") or 0.0),
                "pdf_url": q.get("pdf_url"),
                "created_at": q.get("created_at"),
                "valid_until": valid_until_raw,
                "is_valid": is_valid,
                "status": computed_status
            })

        total_pages = (total_count + limit - 1) // limit if total_count > 0 else 1

        return {
            "success": True,
            "data": formatted_list,
            "pagination": {
                "total": total_count,
                "page": page,
                "limit": limit,
                "total_pages": total_pages
            }
        }

    except Exception as e:
        print("QUOTATION FETCH ERROR:", str(e))
        raise HTTPException(status_code=500, detail=f"Failed to fetch document list: {str(e)}")


@router.get("/{quotation_id}")
async def get_quotation_details(quotation_id: str):
    try:
        quote_res = supabase_secondary.table("quotations") \
            .select("*, inquiries(*)") \
            .eq("id", quotation_id) \
            .single() \
            .execute()

        if not quote_res.data:
            raise HTTPException(status_code=404, detail="Quotation not found.")

        quote_data = quote_res.data

        items_res = supabase_secondary.table("quotation_items") \
            .select("*") \
            .eq("quotation_id", quotation_id) \
            .execute()

        quote_data["items"] = items_res.data or []

        return {
            "success": True,
            "data": quote_data
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))