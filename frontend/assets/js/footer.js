function openPrivacyModal() {
    const modal = document.getElementById('privacyModal');
    if (modal) modal.classList.remove('opacity-0', 'pointer-events-none');
}

function closePrivacyModal() {
    const modal = document.getElementById('privacyModal');
    if (modal) modal.classList.add('opacity-0', 'pointer-events-none');
}

function openTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) modal.classList.remove('opacity-0', 'pointer-events-none');
}

function closeTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) modal.classList.add('opacity-0', 'pointer-events-none');
}