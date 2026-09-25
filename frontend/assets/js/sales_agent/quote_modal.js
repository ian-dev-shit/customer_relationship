
let quoteItems = [];

function openQuotationModal(inquiryData) {
    document.getElementById('quote_inquiry_id').value = inquiryData.id;
    document.getElementById('quote_customer_name').value = inquiryData.contact_person || inquiryData.company_name;
    document.getElementById('quote_customer_email').value = inquiryData.email;
    document.getElementById('quote_company_name').value = inquiryData.company_name || '';
    document.getElementById('quote_origin').innerText = inquiryData.origin || 'N/A';
    document.getElementById('quote_destination').innerText = inquiryData.destination || 'N/A';
    document.getElementById('quote_service_type').innerText = inquiryData.service_type || inquiryData.service || 'Freight';

    // Set default valid date (7 days from today)
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    document.getElementById('quote_valid_until').value = nextWeek.toISOString().split('T')[0];

    // Clear items and add default initial item
    document.getElementById('quotationItemsContainer').innerHTML = '';
    addQuotationRow(inquiryData.service_type ? `${inquiryData.service_type} Charge` : 'Freight Charge', 1, inquiryData.estimated_amount || 0);

    document.getElementById('quotationModal').classList.remove('hidden');
}

function closeQuotationModal() {
    document.getElementById('quotationModal').classList.add('hidden');
}

function addQuotationRow(desc = '', qty = 1, price = 0) {
    const container = document.getElementById('quotationItemsContainer');
    const rowId = Date.now() + Math.random();

    const tr = document.createElement('tr');
    tr.id = `row-${rowId}`;
    tr.innerHTML = `
        <td class="p-2">
            <input type="text" value="${desc}" placeholder="Charge Description" required class="item-desc w-full px-2 py-1 border border-slate-300 rounded outline-none">
        </td>
        <td class="p-2">
            <input type="number" value="${qty}" min="0.1" step="any" oninput="calculateQuoteTotals()" required class="item-qty w-full text-center px-2 py-1 border border-slate-300 rounded outline-none">
        </td>
        <td class="p-2">
            <input type="number" value="${price}" min="0" step="any" oninput="calculateQuoteTotals()" required class="item-price w-full text-right px-2 py-1 border border-slate-300 rounded outline-none">
        </td>
        <td class="p-2 text-right font-semibold text-slate-700 item-total">
            ₱0.00
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="removeQuotationRow('row-${rowId}')" class="text-red-500 hover:text-red-700 font-bold">✕</button>
        </td>
    `;
    container.appendChild(tr);
    calculateQuoteTotals();
}

function removeQuotationRow(rowId) {
    const container = document.getElementById('quotationItemsContainer');
    if (container.children.length > 1) {
        document.getElementById(rowId).remove();
        calculateQuoteTotals();
    } else {
        alert("At least one charge item is required.");
    }
}

function calculateQuoteTotals() {
    let subtotal = 0;
    const rows = document.querySelectorAll('#quotationItemsContainer tr');

    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const total = qty * price;
        subtotal += total;
        row.querySelector('.item-total').innerText = `₱${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    });

    const tax = parseFloat(document.getElementById('quote_tax_amount').value) || 0;
    const discount = parseFloat(document.getElementById('quote_discount_amount').value) || 0;
    const grandTotal = subtotal + tax - discount;

    document.getElementById('quote_subtotal_text').innerText = `₱${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    document.getElementById('quote_grand_total_text').innerText = `₱${grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
}

async function handleSendQuotation(event) {
    event.preventDefault();
    const btn = document.getElementById('btnSubmitQuotation');
    btn.disabled = true;
    btn.innerText = "Sending PDF & Email...";

    const rows = document.querySelectorAll('#quotationItemsContainer tr');
    const items = [];

    rows.forEach(row => {
        items.push({
            description: row.querySelector('.item-desc').value,
            quantity: parseFloat(row.querySelector('.item-qty').value),
            unit_price: parseFloat(row.querySelector('.item-price').value)
        });
    });

    const payload = {
        inquiry_id: document.getElementById('quote_inquiry_id').value,
        customer_name: document.getElementById('quote_customer_name').value,
        customer_email: document.getElementById('quote_customer_email').value,
        company_name: document.getElementById('quote_company_name').value,
        origin: document.getElementById('quote_origin').innerText,
        destination: document.getElementById('quote_destination').innerText,
        service_type: document.getElementById('quote_service_type').innerText,
        valid_until: document.getElementById('quote_valid_until').value,
        tax_amount: parseFloat(document.getElementById('quote_tax_amount').value) || 0,
        discount_amount: parseFloat(document.getElementById('quote_discount_amount').value) || 0,
        items: items
    };

    try {
        const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/sales/quotations/send`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            alert('Quotation sent successfully!');
            closeQuotationModal();
            window.location.reload(); // Refresh table status
        } else {
            const err = await response.json();
            alert('Error: ' + (err.detail || 'Failed to send quotation.'));
        }
    } catch (e) {
        alert('Network Error: Check if FastAPI backend is running.');
    } finally {
        btn.disabled = false;
        btn.innerText = "Send Official Quotation";
    }
}