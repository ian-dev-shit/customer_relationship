document.addEventListener('DOMContentLoaded', () => {
  const logoutBtn = document.getElementById('logoutBtn');
  const logoutModal = document.getElementById('logoutModal');
  const logoutModalContainer = document.getElementById('logoutModalContainer');
  const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');
  const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
  const confirmState = document.getElementById('logoutConfirmState');
  const loadingState = document.getElementById('logoutLoadingState');
  const statusText = document.getElementById('logoutStatusText');

  // Open Logout Modal
  if (logoutBtn) {
    logoutBtn.addEventListener('click', (e) => {
      e.preventDefault();
      logoutModal?.classList.remove('opacity-0', 'pointer-events-none');
      logoutModalContainer?.classList.remove('scale-95');
      logoutModalContainer?.classList.add('scale-100');
    });
  }

  // Close Modal Function
  function closeModal() {
    logoutModalContainer?.classList.remove('scale-100');
    logoutModalContainer?.classList.add('scale-95');
    logoutModal?.classList.add('opacity-0', 'pointer-events-none');
  }

  if (cancelLogoutBtn) {
    cancelLogoutBtn.addEventListener('click', closeModal);
  }

  // Click outside modal background to close
  logoutModal?.addEventListener('click', (e) => {
    if (e.target === logoutModal) closeModal();
  });

  // Confirm Logout Action & Redirect Animation
  if (confirmLogoutBtn) {
    confirmLogoutBtn.addEventListener('click', () => {
      confirmState?.classList.add('hidden');
      loadingState?.classList.remove('hidden');

      setTimeout(() => {
        if (statusText) statusText.innerText = "Securing session cleanup...";
      }, 500);

      setTimeout(() => {
        if (statusText) statusText.innerText = "Redirecting to login...";
      }, 1000);

      setTimeout(() => {
        window.location.href = '/logout.php'; 
      }, 1400);
    });
  }
});