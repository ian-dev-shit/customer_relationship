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
  document.getElementById('modalStatusSelect').value = lead.status || 'new_inquiry';

  document.getElementById('viewModal').classList.remove('hidden');
  document.getElementById('viewModal').classList.add('flex');
}

function closeViewModal() {
  document.getElementById('viewModal').classList.add('hidden');
  document.getElementById('viewModal').classList.remove('flex');
}

async function handleStatusUpdate(e) {
  e.preventDefault();
  const leadId = document.getElementById('modalLeadId').value;
  const newStatus = document.getElementById('modalStatusSelect').value;

  try {
    const response = await fetch(`/api/v1/leads/${leadId}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ status: newStatus })
    });

    if (response.ok) {
      alert('Lead status updated successfully!');
      location.reload();
    } else {
      alert('Failed to update status.');
    }
  } catch (err) {
    console.error(err);
    alert('An error occurred while updating status.');
  }
}