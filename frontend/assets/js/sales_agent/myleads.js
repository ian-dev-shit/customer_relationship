const FASTAPI_BASE_URL = "http://127.0.0.1:8000";

document.addEventListener('DOMContentLoaded', () => {
  const statusSelect = document.getElementById('modalStatusSelect');
  if (statusSelect) {
    statusSelect.addEventListener('change', function () {
      togglePickupFields(this.value);
      syncStepper();
    });
  }

  // Live update sa Value Chip 
  const priceInput = document.getElementById('modalPriceInput');
  if (priceInput) {
    priceInput.addEventListener('input', function () {
      const val = parseFloat(this.value) || 0;
      updateValueChip(val);
    });
  }
});

const STAGE_ORDER = ['new_inquiry', 'qualifying', 'quote_sent', 'negotiation', 'closed_won', 'closed_lost'];

function normalizeStatus(st) {
  if (!st) return 'new_inquiry';
  let clean = String(st).toLowerCase().trim().replace(/[\s-]+/g, '_');
  if (clean === 'quote') clean = 'quote_sent';
  if (clean === 'qualify') clean = 'qualifying';
  if (clean === 'negotiate') clean = 'negotiation';
  if (clean === 'won') clean = 'closed_won';
  if (clean === 'lost') clean = 'closed_lost';
  return clean;
}

function stageOrder(stage) {
  const clean = normalizeStatus(stage);
  const idx = STAGE_ORDER.indexOf(clean);
  return idx !== -1 ? idx : 0;
}

function updateValueChip(amount) {
  const chip = document.getElementById('modalValueChip');
  if (chip) {
    const val = parseFloat(amount) || 0;
    chip.innerText = '₱' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
}

function syncStepper() {
  const select = document.getElementById('modalStatusSelect');
  if (!select) return;

  const current = normalizeStatus(select.value);
  const activeIdx = stageOrder(current);
  const steps = document.querySelectorAll('#viewModal .lm-step');

  steps.forEach(el => {
    const stageAttr = normalizeStatus(el.getAttribute('data-stage'));
    const idx = stageOrder(stageAttr);

    el.classList.remove('is-active', 'is-done');
    if (idx === activeIdx) {
      el.classList.add('is-active');
    } else if (idx < activeIdx) {
      el.classList.add('is-done');
    }
  });
}

function togglePickupFields(status) {
  const pickupSection = document.getElementById('pickupFieldsSection');
  if (!pickupSection) return;

  if (normalizeStatus(status) === 'closed_won') {
    pickupSection.classList.remove('hidden');
  } else {
    pickupSection.classList.add('hidden');
  }
}

function generateInitials(name) {
  if (!name || name === 'N/A') return '--';
  const cleanName = name.trim();
  const words = cleanName.split(/\s+/);
  if (words.length >= 2) {
    return (words[0][0] + words[1][0]).toUpperCase();
  }
  return cleanName.substring(0, 2).toUpperCase();
}

function restrictStatusDropdown(currentStatus) {
  const select = document.getElementById('modalStatusSelect');
  if (!select) return;

  const currentIdx = stageOrder(currentStatus);

  Array.from(select.options).forEach(option => {
    const optionIdx = stageOrder(option.value);

    // Idi-disable ang mga nakalipas na status 
    if (optionIdx < currentIdx && normalizeStatus(option.value) !== 'closed_lost') {
      option.disabled = true;
      if (!option.innerText.includes('(Locked)')) {
        option.innerText = option.innerText + ' (Locked)';
      }
    } else {
      option.disabled = false;
      option.innerText = option.innerText.replace(' (Locked)', '');
    }
  });
}

function openViewModal(lead) {
  console.log("OPENING MODAL WITH DATA:", lead);

  // 1. Lead ID at Header Info
  document.getElementById('modalLeadId').value = lead.id || lead.lead_id || '';
  const company = lead.company_name || lead.company || 'N/A';
  document.getElementById('modalCompany').innerText = company;
  document.getElementById('modalCode').innerText = lead.inquiry_code || lead.code || ('INQ-' + String(lead.id || '').substring(0, 8));

  // 2. Compute Dynamic Avatar Initials 
  const avatarElem = document.getElementById('modalAvatar');
  if (avatarElem) {
    avatarElem.innerText = generateInitials(company);
  }

  // 3. Contact Info
  const contact = lead.contact_person || lead.contact || 'N/A';
  const email = lead.email || 'N/A';
  const phone = lead.phone_number || lead.phone || 'N/A';

  document.getElementById('modalContact').innerText = contact;
  document.getElementById('modalEmail').innerText = email;
  document.getElementById('modalPhone').innerText = phone;
  document.getElementById('modalPlatform').innerText = (lead.platform_used === 'Google Forms') ? 'Gmail' : (lead.platform_used || 'N/A');
  document.getElementById('modalService').innerText = lead.service_type || lead.service || 'N/A';
  document.getElementById('modalRoute').innerText = (lead.origin || 'N/A') + ' ➔ ' + (lead.destination || 'N/A');

  // 4. Action Buttons 
  const emailBtn = document.getElementById('contactModalEmailBtn');
  const emailText = document.getElementById('contactModalEmailText');
  if (emailBtn && emailText) {
    emailText.innerText = email;
    emailBtn.href = (email !== 'N/A') ? `mailto:${email}` : '#';
  }

  const phoneBtn = document.getElementById('contactModalPhoneBtn');
  const phoneText = document.getElementById('contactModalPhoneText');
  if (phoneBtn && phoneText) {
    phoneText.innerText = phone;
    phoneBtn.href = (phone !== 'N/A') ? `tel:${phone}` : '#';
  }

  // 5. Cargo Details
  const cargoElem = document.getElementById('modalCargo');
  if (cargoElem) {
    cargoElem.value = lead.cargo_details || lead.initial_inquiry_text || '';
  }

  // 6. Price Auto-Fill 
  const rawPrice = parseFloat(lead.estimated_amount ?? lead.estimated_price ?? lead.agreed_price ?? 0);
  const priceInput = document.getElementById('modalPriceInput');
  if (priceInput) {
    priceInput.value = rawPrice > 0 ? rawPrice : '';
  }
  updateValueChip(rawPrice);

  // 7. Status & Stepper Alignment
  const currentStatus = normalizeStatus(lead.status);
  const statusSelect = document.getElementById('modalStatusSelect');
  if (statusSelect) {
    statusSelect.value = currentStatus;
  }

  restrictStatusDropdown(currentStatus);
  togglePickupFields(currentStatus);
  syncStepper();

  // 8. Display Modal
  const modal = document.getElementById('viewModal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeViewModal() {
  const modal = document.getElementById('viewModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}

async function handleStatusUpdate(e) {
  e.preventDefault();
  const leadId = document.getElementById('modalLeadId').value;
  const newStatus = document.getElementById('modalStatusSelect').value;
  const priceVal = document.getElementById('modalPriceInput').value;

  const cargoDetails = document.getElementById('modalCargo')?.value.trim();
  const pickupAddress = document.getElementById('modalPickupAddress')?.value.trim();
  const pickupDateTime = document.getElementById('modalPickupDateTime')?.value;

  if (newStatus === 'closed_won') {
    if (!pickupAddress || !pickupDateTime) {
      if (typeof SwiftAlert !== 'undefined') {
        SwiftAlert.fire({
          icon: 'warning',
          title: 'Missing Pickup Details',
          text: 'Please provide both Pickup Address and Pickup Date & Time before closing as WON.'
        });
      } else {
        alert('Please provide both Pickup Address and Pickup Date & Time before closing as WON.');
      }
      return;
    }
  }

  const payload = {
    status: newStatus,
    estimated_amount: priceVal ? parseFloat(priceVal) : 0,
    estimated_price: priceVal ? parseFloat(priceVal) : 0,
    cargo_details: cargoDetails || null,
    pickup_address: pickupAddress || null,
    pickup_datetime: pickupDateTime ? new Date(pickupDateTime).toISOString() : null
  };

  try {
    const response = await fetch(`${FASTAPI_BASE_URL}/api/v1/leads/${leadId}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    if (response.ok) {
      closeViewModal();
      location.reload();
    } else {
      const errData = await response.json();
      alert(`Update Failed: ${errData.detail || 'Could not update lead record.'}`);
    }
  } catch (err) {
    console.error(err);
    alert('Cannot connect to FastAPI server. Make sure Uvicorn is running!');
  }
}