// Global Sidebar Toggle Function
// Mobile drawer state is driven by .is-open / .is-active, styled in
// includes/tailwind_css.php (section 16 - MOBILE RAIL).
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (sidebar) {
    sidebar.classList.toggle('is-open');
  }

  if (window.innerWidth <= 767 && overlay) {
    overlay.classList.toggle('is-active');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('sidebarOverlay');

  // Mobile Overlay Close
  overlay?.addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) sidebar.classList.remove('is-open');
    this.classList.remove('is-active');
  });

  // Responsive Layout Reset on Screen Resize
  window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (window.innerWidth > 767) {
      overlay?.classList.remove('is-active');
      sidebar?.classList.remove('is-open');
    }
  });
});