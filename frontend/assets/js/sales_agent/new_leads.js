function openLeadModal() {
  document.getElementById('newLeadModal').classList.remove('hidden');
}

function closeLeadModal() {
  document.getElementById('newLeadModal').classList.add('hidden');
  document.getElementById('createLeadForm').reset();
}

async function submitNewLead(event) {
  event.preventDefault();

  const form = event.target;
  const submitBtn = document.getElementById('submitLeadBtn');
  
  // Disable button while processing
  submitBtn.disabled = true;
  submitBtn.innerHTML = `<i class="fa-solid fa-spinner animate-spin"></i> Saving...`;

  const payload = {
    company_name: form.company_name.value.trim(),
    contact_person: form.contact_person.value.trim(),
    email: form.email.value.trim(),
    phone_number: form.phone_number.value.trim(),
    service_type: form.service_type.value,
    origin: form.origin.value.trim(),
    destination: form.destination.value.trim()
  };

  try {
    const response = await fetch('http://127.0.0.1:8000/api/v1/leads/leads', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    const result = await response.json();

    if (response.ok && (result.status === 'success' || response.status === 200 || response.status === 201)) {
      closeLeadModal();
      
      // 1. Success Toast Notification
      showToast('Lead successfully added!', 'success');

      // 2. Continuous flow delay para makita ang toast bago mag-reload
      setTimeout(() => {
        window.location.reload();
      }, 1200);

    } else {
      // 3. Error SweetAlert Modal
      showAlert('Failed to Add Lead', result.detail || 'Failed to create lead.', 'error');
    }
  } catch (err) {
    console.error('Fetch Error:', err);
    // 4. Connection Failure Alert
    showAlert('Connection Error', 'Failed to connect to backend server.', 'error');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = `<span>Save Lead</span>`;
  }
}