function openContactModal(company, person, email, phone) {
  document.getElementById('contactModalCompany').innerText = company || 'N/A';
  document.getElementById('contactModalPerson').innerText = person ? '👤 ' + person : 'No contact person';
  
  // Gmail / Email Link setup
  const emailBtn = document.getElementById('contactModalEmailBtn');
  const emailText = document.getElementById('contactModalEmailText');
  if (email) {
    emailBtn.href = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(email)}`;
    emailText.innerText = email;
    emailBtn.classList.remove('opacity-50', 'pointer-events-none');
  } else {
    emailBtn.href = '#';
    emailText.innerText = 'No email provided';
    emailBtn.classList.add('opacity-50', 'pointer-events-none');
  }

  // Phone Link setup
  const phoneBtn = document.getElementById('contactModalPhoneBtn');
  const phoneText = document.getElementById('contactModalPhoneText');
  if (phone) {
    phoneBtn.href = `tel:${phone}`;
    phoneText.innerText = phone;
    phoneBtn.classList.remove('opacity-50', 'pointer-events-none');
  } else {
    phoneBtn.href = '#';
    phoneText.innerText = 'No phone provided';
    phoneBtn.classList.add('opacity-50', 'pointer-events-none');
  }

  document.getElementById('contactOptionsModal').classList.remove('hidden');
  document.getElementById('contactOptionsModal').classList.add('flex');
}

function closeContactModal() {
  document.getElementById('contactOptionsModal').classList.add('hidden');
  document.getElementById('contactOptionsModal').classList.remove('flex');
}