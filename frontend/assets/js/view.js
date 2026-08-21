function viewCustomerDetails(user) {
  document.getElementById('modal_first_name').textContent = user.first_name || 'N/A';
  document.getElementById('modal_last_name').textContent = user.last_name || 'N/A';
  document.getElementById('modal_email').querySelector('span').textContent = user.email || 'N/A';
  document.getElementById('modal_company').querySelector('span').textContent = user.company_name || 'Individual Client';
  document.getElementById('modal_phone').querySelector('span').textContent = user.phone_number || 'N/A';
  document.getElementById('modal_created_at').querySelector('span').textContent = user.created_at || 'N/A';

  document.getElementById('viewDetailsModal').classList.remove('hidden');
}

function closeDetailsModal() {
  document.getElementById('viewDetailsModal').classList.add('hidden');
}