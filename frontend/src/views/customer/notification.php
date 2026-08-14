<?php
$page_title = "Notification · Rising Red Dragon";

include_once '../../includes/header.php';
?>

<div class="app-container">

  <!-- SIDEBAR INCLUDE -->
  <?php include_once '../../includes/sidebar.php'; ?>

  <!-- MAIN CONTENT – NOTIFICATION DASHBOARD -->
  <main class="main-content mesh-bg relative overflow-y-auto">

    <!-- Mobile toggle button -->
    <button onclick="toggleSidebar()" class="mobile-toggle fixed top-4 left-4 z-30 p-2 rounded-lg bg-slate-800/80 backdrop-blur border border-slate-700 text-slate-300 hover:text-white transition" aria-label="Open sidebar">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
      </svg>
    </button>

    <div class="max-w-5xl mx-auto fade-in">

      <!-- PAGE HEADER -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-tight">Notification</h1>
          <p class="text-sm text-slate-400 mt-0.5">Notification - Alerts for your accounts</p>
        </div>
        <!-- Session time + notification count -->
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2 text-sm">
            <span class="text-slate-400">🔔</span>
            <span class="text-slate-300">5 alerts</span>
          </div>
          <div class="text-right">
            <p class="text-xs text-slate-500">Session Active</p>
            <p class="text-sm font-mono text-sky-400 font-semibold" id="sessionTime">5:38:45 PM</p>
          </div>
        </div>
      </div>

      <!-- NOTIFICATION LIST -->
      <div class="glass-card rounded-2xl p-5">

        <!-- Header with actions -->
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-700/50">
          <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Recent Alerts</h2>
          <button onclick="markAllRead()" class="text-xs text-sky-400 hover:text-sky-300 transition font-medium">
            Mark all as read
          </button>
        </div>

        <!-- Notification items -->
        <div class="space-y-4">

          <!-- Alert 1: SLA Breach (Urgent) -->
          <div class="notif-urgent glass-panel rounded-xl p-4 hover:bg-white/5 transition cursor-pointer" onclick="viewNotification('SLA Breach - WB12345')">
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 rounded-full bg-rose-500 mt-2 flex-shrink-0"></div>
              <div class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <h3 class="text-sm font-semibold text-white">SLA Breaches - WB12345</h3>
                  <span class="text-xs text-rose-400 font-medium">Urgent</span>
                </div>
                <p class="text-sm text-slate-300 mt-0.5">
                  Delivery Exceeded SLA window by 3h 40m. Escalated to Ops.
                </p>
                <div class="flex items-center gap-4 mt-2">
                  <span class="text-xs text-slate-500">⏱️ 2h ago</span>
                  <a href="#" class="text-xs text-sky-400 hover:text-sky-300 transition font-medium">View Details →</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Alert 2: Document Pending (Warning) -->
          <div class="notif-warning glass-panel rounded-xl p-4 hover:bg-white/5 transition cursor-pointer" onclick="viewNotification('Document Pending - Inv2026.pdf')">
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 rounded-full bg-amber-500 mt-2 flex-shrink-0"></div>
              <div class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <h3 class="text-sm font-semibold text-white">Document Pending - Inv2026.pdf</h3>
                  <span class="text-xs text-amber-400 font-medium">Pending</span>
                </div>
                <p class="text-sm text-slate-300 mt-0.5">
                  Awaiting verification uploaded and confirmed.
                </p>
                <div class="flex items-center gap-4 mt-2">
                  <span class="text-xs text-slate-500">⏱️ 5h ago</span>
                  <a href="#" class="text-xs text-sky-400 hover:text-sky-300 transition font-medium">Review →</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Alert 3: Delivered (Success) -->
          <div class="notif-success glass-panel rounded-xl p-4 hover:bg-white/5 transition cursor-pointer" onclick="viewNotification('Delivered - WB-12367')">
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 rounded-full bg-emerald-500 mt-2 flex-shrink-0"></div>
              <div class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <h3 class="text-sm font-semibold text-white">Delivered - WB-12367</h3>
                  <span class="text-xs text-emerald-400 font-medium">Completed</span>
                </div>
                <p class="text-sm text-slate-300 mt-0.5">
                  Proof of delivery uploaded and confirmed.
                </p>
                <div class="flex items-center gap-4 mt-2">
                  <span class="text-xs text-slate-500">⏱️ 1d ago</span>
                  <a href="#" class="text-xs text-sky-400 hover:text-sky-300 transition font-medium">View POD →</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Alert 4: Inquiry Resolved (Info) -->
          <div class="notif-info glass-panel rounded-xl p-4 hover:bg-white/5 transition cursor-pointer" onclick="viewNotification('Inquiry Resolved - INQ-1245')">
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 rounded-full bg-sky-500 mt-2 flex-shrink-0"></div>
              <div class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <h3 class="text-sm font-semibold text-white">Inquiry Resolved - INQ-1245</h3>
                  <span class="text-xs text-sky-400 font-medium">Resolved</span>
                </div>
                <p class="text-sm text-slate-300 mt-0.5">
                  Billing clarification closed by your account manager.
                </p>
                <div class="flex items-center gap-4 mt-2">
                  <span class="text-xs text-slate-500">⏱️ 3d ago</span>
                  <a href="#" class="text-xs text-sky-400 hover:text-sky-300 transition font-medium">View →</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Alert 5: Shipment Delay (Warning) -->
          <div class="notif-warning glass-panel rounded-xl p-4 hover:bg-white/5 transition cursor-pointer" onclick="viewNotification('Shipment Delay - WB-1245')">
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 rounded-full bg-amber-500 mt-2 flex-shrink-0"></div>
              <div class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <h3 class="text-sm font-semibold text-white">Shipment Delay - WB-1245</h3>
                  <span class="text-xs text-amber-400 font-medium">Updated</span>
                </div>
                <p class="text-sm text-slate-300 mt-0.5">
                  New ETA +2h due to traffic advisory on route.
                </p>
                <div class="flex items-center gap-4 mt-2">
                  <span class="text-xs text-slate-500">⏱️ 3d ago</span>
                  <a href="#" class="text-xs text-sky-400 hover:text-sky-300 transition font-medium">Track →</a>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- FOOTER COPYRIGHT -->
      <p class="text-center text-[10px] text-slate-500 mt-8 pt-4 border-t border-white/5">© 2026 CargoNet Systems. Global Logistics Solutions.</p>

    </div>
  </main>

</div>

<!-- PAGE SPECIFIC SCRIPT -->
<script>
  function viewNotification(title) {
    alert(`📬 Notification: ${title}\n\nIn production, this would open the full notification details.`);
  }

  function markAllRead() {
    if (confirm('Mark all notifications as read?')) {
      alert('✅ All notifications marked as read.');
    }
  }

  function updateSessionTime() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const el = document.getElementById('sessionTime');
    if (el) el.textContent = timeStr;
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    updateSessionTime();
    setInterval(updateSessionTime, 1000);
  });
</script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>