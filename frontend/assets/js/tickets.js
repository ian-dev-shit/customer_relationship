function openCreateModal(data) {
  document.getElementById('modal_ticket_id').value = data.ticket_id || '';
  document.getElementById('modal_email').value = data.email || '';
  document.getElementById('modal_first_name').value = data.first_name || '';
  document.getElementById('modal_last_name').value = data.last_name || '';
  document.getElementById('modal_company_name').value = data.company_name || '';
  document.getElementById('modal_phone_number').value = data.phone_number || '';
  
  generatePassword(); // Auto fill initial password
  
  document.getElementById('accountModal').classList.remove('hidden');
}

function closeCreateModal() {
  document.getElementById('accountModal').classList.add('hidden');
}

function generatePassword() {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
  let password = '';
  for (let i = 0; i < 10; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  document.getElementById('modal_password').value = password;
}

async function submitCreateAccount(e) {
  e.preventDefault();
  
  const submitBtn = document.getElementById('submitBtn');
  submitBtn.disabled = true;
  submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Creating...`;

  const payload = {
    ticket_id: document.getElementById('modal_ticket_id').value,
    email: document.getElementById('modal_email').value,
    first_name: document.getElementById('modal_first_name').value,
    last_name: document.getElementById('modal_last_name').value,
    company_name: document.getElementById('modal_company_name').value,
    phone_number: document.getElementById('modal_phone_number').value,
    password: document.getElementById('modal_password').value
  };

  try {
    const response = await fetch('http://127.0.0.1:8000/api/v1/admin/create-customer-from-ticket', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const result = await response.json();

    if (response.ok) {
      alert('Account successfully created! Notification email queued.');
      window.location.reload(); // Refresh table & sidebar count
    } else {
      alert('Error: ' + (result.detail || 'Failed to create account'));
    }
  } catch (err) {
    alert('Server connection failed: ' + err.message);
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = `<span>Provision Account</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>`;
  }
}