// Global Sidebar Toggle Function
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (sidebar) {
    sidebar.classList.toggle('collapsed');
  }

  if (window.innerWidth <= 768 && overlay) {
    overlay.classList.toggle('active');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('sidebarOverlay');

  // Mobile Overlay Close
  overlay?.addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) sidebar.classList.add('collapsed');
    this.classList.remove('active');
  });

  // Responsive Layout Reset on Screen Resize
  window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (window.innerWidth > 768) {
      overlay?.classList.remove('active');
      sidebar?.classList.remove('collapsed');
    }
  });
});