import sys
from unittest.mock import MagicMock, patch

# 1. Mock WeasyPrint bago i-import ang anumang app routes (lalo na sa Windows environment)
sys.modules["weasyprint"] = MagicMock()

import uuid
from datetime import datetime, timezone
from fastapi import FastAPI
from fastapi.testclient import TestClient
from app.routes.sales_agent.send_docs import router

app = FastAPI()
app.include_router(router)

client = TestClient(app)

# Dummy test IDs and data
MOCK_INQUIRY_ID = str(uuid.uuid4())
MOCK_QUOTATION_ID = str(uuid.uuid4())
MOCK_USER_ID = str(uuid.uuid4())

MOCK_QUOTATION_DB = {
    "id": MOCK_QUOTATION_ID,
    "inquiry_id": MOCK_INQUIRY_ID,
    "quote_number": "QT-2026-ABCDE",
    "version": 1,
    "subtotal": 45000.0,
    "tax_amount": 0.0,
    "discount_amount": 0.0,
    "total_amount": 45000.0,
    "status": "sent",
    "pdf_url": "https://supabase.co/storage/v1/object/public/quotations/pdf/QT-2026-ABCDE.pdf",
    "valid_until": "2026-12-31T23:59:59Z",
    "created_by": MOCK_USER_ID,
    "created_at": "2026-08-15T10:00:00Z",
}

VALID_PAYLOAD = {
    "inquiry_id": MOCK_INQUIRY_ID,
    "customer_name": "Juan Dela Cruz",
    "customer_email": "juan@example.com",
    "company_name": "ABC Logistics Corp",
    "origin": "Manila Port",
    "destination": "Cebu Port",
    "service_type": "FCL Freight",
    "tax_amount": 0.0,
    "discount_amount": 0.0,
    "valid_until": "2026-12-31T23:59:59Z",
    "created_by": MOCK_USER_ID,
    "items": [
        {
            "description": "20ft FCL Sea Freight Charge",
            "quantity": 1.0,
            "unit_price": 45000.0,
        }
    ],
}


# ==========================================
# 1. TEST: POST /api/sales/quotations/send
# ==========================================


@patch("app.routes.sales_agent.send_docs.generate_pdf_bytes")
@patch("app.routes.sales_agent.send_docs.supabase_secondary")
def test_create_and_send_quotation_success(mock_supabase, mock_gen_pdf):
    # Mock PDF Byte Generation
    mock_gen_pdf.return_value = b"%PDF-1.4 Fake PDF Content"

    # Mock Storage methods
    mock_storage = MagicMock()
    mock_supabase.storage.from_.return_value = mock_storage
    mock_storage.upload.return_value = True
    mock_storage.get_public_url.return_value = MOCK_QUOTATION_DB["pdf_url"]

    # Mock Table Insertions
    mock_quote_insert = MagicMock()
    mock_quote_insert.data = [MOCK_QUOTATION_DB]

    mock_supabase.table.return_value.insert.return_value.execute.return_value = (
        mock_quote_insert
    )
    mock_supabase.table.return_value.update.return_value.eq.return_value.execute.return_value = (
        MagicMock(data=[{"status": "quote_sent"}])
    )

    response = client.post("/api/sales/quotations/send", json=VALID_PAYLOAD)

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["id"] == MOCK_QUOTATION_ID
    assert res_json["quote_number"] == "QT-2026-ABCDE"
    assert res_json["total_amount"] == 45000.0
    assert res_json["pdf_url"] == MOCK_QUOTATION_DB["pdf_url"]


@patch("app.routes.sales_agent.send_docs.generate_pdf_bytes")
@patch("app.routes.sales_agent.send_docs.supabase_secondary")
def test_create_and_send_quotation_db_insert_error(mock_supabase, mock_gen_pdf):
    mock_gen_pdf.return_value = b"%PDF-1.4 Fake PDF Content"

    mock_storage = MagicMock()
    mock_supabase.storage.from_.return_value = mock_storage
    mock_storage.upload.return_value = True
    mock_storage.get_public_url.return_value = "https://fakeurl.com/pdf.pdf"

    # Mock insert failure (returns empty data list)
    mock_quote_insert = MagicMock()
    mock_quote_insert.data = []

    mock_supabase.table.return_value.insert.return_value.execute.return_value = (
        mock_quote_insert
    )

    response = client.post("/api/sales/quotations/send", json=VALID_PAYLOAD)

    assert response.status_code == 500
    assert "Failed to insert quotation record." in response.json()["detail"]


def test_create_and_send_quotation_validation_error():
    # Incomplete payload (Missing required origin and destination)
    invalid_payload = {
        "inquiry_id": MOCK_INQUIRY_ID,
        "customer_name": "Juan Dela Cruz",
        "customer_email": "juan@example.com",
    }

    response = client.post("/api/sales/quotations/send", json=invalid_payload)

    assert response.status_code == 422  # Unprocessable Entity


# ==========================================
# 2. TEST: GET /api/sales/quotations/
# ==========================================


@patch("app.routes.sales_agent.send_docs.supabase_secondary")
def test_list_sent_quotations_success(mock_supabase):
    mock_list_data = [
        {
            **MOCK_QUOTATION_DB,
            "inquiries": {
                "contact_person": "Juan Dela Cruz",
                "company_name": "ABC Logistics Corp",
                "email": "juan@example.com",
                "service_type": "FCL Freight",
                "origin": "Manila Port",
                "destination": "Cebu Port",
            },
        }
    ]

    mock_execute = MagicMock()
    mock_execute.data = mock_list_data
    mock_execute.count = 1

    (
        mock_supabase.table.return_value.select.return_value.order.return_value.range.return_value.execute.return_value
    ) = mock_execute

    response = client.get("/api/sales/quotations/")

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["success"] is True
    assert len(res_json["data"]) == 1
    assert res_json["data"][0]["contact_person"] == "Juan Dela Cruz"
    assert res_json["data"][0]["company_name"] == "ABC Logistics Corp"
    assert res_json["pagination"]["total"] == 1


@patch("app.routes.sales_agent.send_docs.supabase_secondary")
def test_list_sent_quotations_with_search(mock_supabase):
    # Mock finding inquiry ID first
    mock_inq_execute = MagicMock()
    mock_inq_execute.data = [{"id": MOCK_INQUIRY_ID}]

    # Mock quotations response
    mock_quote_execute = MagicMock()
    mock_quote_execute.data = [
        {
            **MOCK_QUOTATION_DB,
            "inquiries": {
                "contact_person": "Juan Dela Cruz",
                "company_name": "ABC Logistics Corp",
                "email": "juan@example.com",
                "service_type": "FCL Freight",
                "origin": "Manila Port",
                "destination": "Cebu Port",
            },
        }
    ]
    mock_quote_execute.count = 1

    # Return inquiry ID for inquiries search, then quote results for quotation search
    mock_supabase.table.return_value.select.return_value.or_.return_value.execute.return_value = (
        mock_inq_execute
    )
    (
        mock_supabase.table.return_value.select.return_value.or_.return_value.order.return_value.range.return_value.execute.return_value
    ) = mock_quote_execute

    response = client.get("/api/sales/quotations/?search=ABC&page=1&limit=10")

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["success"] is True
    assert res_json["pagination"]["total"] == 1


@patch("app.routes.sales_agent.send_docs.supabase_secondary")
def test_list_sent_quotations_server_error(mock_supabase):
    mock_supabase.table.side_effect = Exception("Database fetch error")

    response = client.get("/api/sales/quotations/")

    assert response.status_code == 500
    assert "Failed to fetch document list" in response.json()["detail"]


# ==========================================
# 3. TEST: GET /api/sales/quotations/{quotation_id}
# ==========================================


@patch("app.routes.sales_agent.send_docs.supabase_secondary")
def test_get_quotation_details_success(mock_supabase):
    mock_quote_execute = MagicMock()
    mock_quote_execute.data = {
        **MOCK_QUOTATION_DB,
        "inquiries": {
            "contact_person": "Juan Dela Cruz",
            "company_name": "ABC Logistics Corp",
        },
    }

    mock_items_execute = MagicMock()
    mock_items_execute.data = [
        {
            "id": "item-1",
            "quotation_id": MOCK_QUOTATION_ID,
            "description": "20ft FCL Sea Freight Charge",
            "quantity": 1,
            "unit_price": 45000.0,
            "total_price": 45000.0,
        }
    ]

    (
        mock_supabase.table.return_value.select.return_value.eq.return_value.single.return_value.execute.return_value
    ) = mock_quote_execute

    (
        mock_supabase.table.return_value.select.return_value.eq.return_value.execute.return_value
    ) = mock_items_execute

    response = client.get(f"/api/sales/quotations/{MOCK_QUOTATION_ID}")

    assert response.status_code == 200
    res_json = response.json()
    assert res_json["success"] is True
    assert res_json["data"]["id"] == MOCK_QUOTATION_ID
    assert len(res_json["data"]["items"]) == 1
    assert res_json["data"]["items"][0]["description"] == "20ft FCL Sea Freight Charge"


@patch("app.routes.sales_agent.send_docs.supabase_secondary")
def test_get_quotation_details_not_found(mock_supabase):
    # I-mock ang pag-raise ng Exception sa .single().execute()
    mock_supabase.table.return_value.select.return_value.eq.return_value.single.return_value.execute.side_effect = Exception("PGRST116: JSON object requested, multiple (or no) rows returned")

    response = client.get("/api/sales/quotations/non-existing-id")

    # Dahil nasasalo ito ng catch-all Exception handler, nagbabalik ito ng 500
    assert response.status_code == 500