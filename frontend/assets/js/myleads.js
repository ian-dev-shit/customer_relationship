const FASTAPI_BASE_URL = "http://127.0.0.1:8000"; 

// 1. Dynamic Toggle para sa Pickup Section kapag nagbago ang dropdown
document.addEventListener('DOMContentLoaded', () => {
  const statusSelect = document.getElementById('modalStatusSelect');
  if (statusSelect) {
    statusSelect.addEventListener('change', function() {
      togglePickupFields(this.value);
    });
  }
});

function togglePickupFields(status) {
  const pickupSection = document.getElementById('pickupFieldsSection');
  if (!pickupSection) return;

  if (status === 'closed_won') {
    pickupSection.classList.remove('hidden');
  } else {
    pickupSection.classList.add('hidden');
  }
}

function openViewModal(lead) {
  document.getElementById('modalLeadId').value = lead.id;
  document.getElementById('modalCompany').innerText = lead.company_name || 'N/A';
  document.getElementById('modalCode').innerText = lead.inquiry_code || ('INQ-' + lead.id.substring(0, 8));
  document.getElementById('modalContact').innerText = lead.contact_person || 'N/A';
  document.getElementById('modalEmail').innerText = lead.email || 'N/A';
  document.getElementById('modalPhone').innerText = lead.phone_number || 'N/A';
  document.getElementById('modalPlatform').innerText = 
    (lead.platform_used === 'Google Forms') ? 'Gmail' : (lead.platform_used || 'N/A');
  document.getElementById('modalService').innerText = lead.service_type || 'N/A';
  document.getElementById('modalRoute').innerText = (lead.origin || 'N/A') + ' ➔ ' + (lead.destination || 'N/A');
  document.getElementById('modalCargo').innerText = lead.cargo_details || lead.initial_inquiry_text || 'No cargo specifications provided.';
  
  const status = lead.status || 'new_inquiry';
  document.getElementById('modalStatusSelect').value = status;

  // Set Current Price
  document.getElementById('modalPriceInput').value = lead.estimated_amount || '';

  // Set Current Pickup Data (kung mayroon)
  if (document.getElementById('modalPickupAddress')) {
    document.getElementById('modalPickupAddress').value = lead.pickup_address || '';
  }
  if (document.getElementById('modalPickupDateTime')) {
    document.getElementById('modalPickupDateTime').value = lead.pickup_datetime ? lead.pickup_datetime.slice(0, 16) : '';
  }

  // Tiyakin kung dapat bang nakatago o nakalitaw ang Pickup fields
  togglePickupFields(status);

  document.getElementById('viewModal').classList.remove('hidden');
  document.getElementById('viewModal').classList.add('flex');
}

function closeViewModal() {
  document.getElementById('viewModal').classList.add('hidden');
  document.getElementById('viewModal').classList.remove('flex');

  // Clear inputs sa modal
  if (document.getElementById('modalPickupAddress')) document.getElementById('modalPickupAddress').value = '';
  if (document.getElementById('modalPickupDateTime')) document.getElementById('modalPickupDateTime').value = '';
}

async function handleStatusUpdate(e) {
  e.preventDefault();
  const leadId = document.getElementById('modalLeadId').value;
  const newStatus = document.getElementById('modalStatusSelect').value;
  const priceVal = document.getElementById('modalPriceInput').value;

  const pickupAddress = document.getElementById('modalPickupAddress')?.value.trim();
  const pickupDateTime = document.getElementById('modalPickupDateTime')?.value;

  // Client-side Validation kapag CLOSED_WON
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

  // Kunin ang Session Agent Info 
  const agentId = document.body.dataset.agentId || null;
  const agentName = document.body.dataset.agentName || null;
  const agentEmail = document.body.dataset.agentEmail || null;

  const payload = {
    status: newStatus,
    estimated_amount: priceVal ? parseFloat(priceVal) : 0,
    pickup_address: pickupAddress || null,
    pickup_datetime: pickupDateTime ? new Date(pickupDateTime).toISOString() : null,
    agent_id: agentId,
    agent_name: agentName,
    agent_email: agentEmail
  };

  try {
    const response = await fetch(`${FASTAPI_BASE_URL}/api/v1/leads/${leadId}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    if (response.ok) {
      closeViewModal();
      
      // Modern SweetAlert Notification
      if (typeof SwiftAlert !== 'undefined') {
        await SwiftAlert.fire({
          icon: 'success',
          title: '<span class="text-slate-800 text-xl font-bold">Updated Successfully!</span>',
          html: `<p class="text-slate-600 text-sm mt-1">Status set to <b class="text-purple-600 uppercase">${newStatus.replace('_', ' ')}</b><br>Agreed Price: <b class="text-emerald-600">₱${parseFloat(priceVal || 0).toLocaleString(undefined, {minimumFractionDigits: 2})}</b></p>`,
          confirmButtonText: 'Continue',
          timer: 2000,
          timerProgressBar: true
        });
      }

      location.reload();
    } else {
      const errData = await response.json();
      const msg = errData.detail || 'Could not update lead record.';
      if (typeof showAlert === 'function') {
        showAlert('Update Failed', msg, 'error');
      } else {
        alert(`Update Failed: ${msg}`);
      }
    }
  } catch (err) {
    console.error(err);
    if (typeof showAlert === 'function') {
      showAlert('Server Error', 'Cannot connect to FastAPI server. Make sure Uvicorn is running!', 'error');
    } else {
      alert('Cannot connect to FastAPI server. Make sure Uvicorn is running!');
    }
  }
}