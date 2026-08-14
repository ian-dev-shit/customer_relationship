<?php
$page_title = "Shipment SLA · Rising Red Dragon";

include_once '../../includes/header.php';
?>

<div class="app-container">

  <!-- SIDEBAR INCLUDE -->
  <?php include_once '../../includes/sidebar.php'; ?>

  <!-- MAIN CONTENT – SLA DASHBOARD -->
  <main class="main-content mesh-bg relative overflow-y-auto">

    <!-- Mobile toggle button -->
    <button onclick="toggleSidebar()" class="mobile-toggle fixed top-4 left-4 z-30 p-2 rounded-lg bg-slate-800/80 backdrop-blur border border-slate-700 text-slate-300 hover:text-white transition" aria-label="Open sidebar">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
      </svg>
    </button>

    <div class="max-w-7xl mx-auto fade-in">

      <!-- PAGE HEADER -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-tight">Shipment SLA</h1>
          <p class="text-sm text-slate-400 mt-0.5">SLA - Scoped to CornHub Inc. shipments only</p>
        </div>
        <!-- Session time -->
        <div class="text-right flex items-center gap-3">
          <span class="text-slate-400 text-lg">🔔</span>
          <div>
            <p class="text-xs text-slate-500">Session Active</p>
            <p class="text-sm font-mono text-sky-400 font-semibold" id="sessionTime">5:38:45 PM</p>
          </div>
        </div>
      </div>

      <!-- TOP METRIC CARDS (3 COLUMNS) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <!-- CARD 1: SLA COMPLIANCE -->
        <div class="glass-card rounded-2xl p-5 border-l-4 border-l-emerald-500">
          <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">SLA COMPLIANCE (30D)</h2>
          <div class="py-2">
            <p class="text-4xl font-bold text-emerald-400">99.9%</p>
            <p class="text-xs text-emerald-300 mt-1 flex items-center gap-1">
              <span>▲</span> +1.1% vs last month
            </p>
          </div>
        </div>

        <!-- CARD 2: ACTIVE SHIPMENTS -->
        <div class="glass-card rounded-2xl p-5 border-l-4 border-l-sky-500">
          <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">ACTIVE SHIPMENTS</h2>
          <div class="py-2">
            <p class="text-4xl font-bold text-sky-400">10</p>
            <p class="text-xs text-slate-400 mt-1">2 in transit today</p>
          </div>
        </div>

        <!-- CARD 3: BREACHES -->
        <div class="glass-card rounded-2xl p-5 border-l-4 border-l-rose-500">
          <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">BREACHES (30D)</h2>
          <div class="py-2">
            <div class="flex items-baseline gap-3">
              <p class="text-4xl font-bold text-rose-400">1</p>
              <p class="text-xs text-slate-300">WB-12345 <span class="text-slate-500">- under review</span></p>
            </div>
          </div>
        </div>

      </div>

      <!-- BOTTOM SECTION: FULL WIDTH SHIPMENT SLA TABLE -->
      <div class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">SHIPMENT SLA STATUS</h2>
          <span class="text-xs text-slate-500 font-mono">LIVE</span>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-xs text-slate-500 border-b border-slate-700/50">
                <th class="pb-3 font-medium">WAYBILL</th>
                <th class="pb-3 font-medium">ROUTE</th>
                <th class="pb-3 font-medium">ETA</th>
                <th class="pb-3 font-medium text-right">SLA</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
              <tr class="hover:bg-white/5 transition">
                <td class="py-3.5 font-mono text-xs text-sky-400">WB-12367</td>
                <td class="py-3.5 text-slate-300">Cebu → Manila</td>
                <td class="py-3.5 text-slate-300">14:32</td>
                <td class="py-3.5 text-right">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">On Time</span>
                </td>
              </tr>
              <tr class="hover:bg-white/5 transition">
                <td class="py-3.5 font-mono text-xs text-sky-400">WB-22456</td>
                <td class="py-3.5 text-slate-300">Manila → Leyte</td>
                <td class="py-3.5 text-slate-300">+1h</td>
                <td class="py-3.5 text-right">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">AI Risk</span>
                </td>
              </tr>
              <tr class="hover:bg-white/5 transition">
                <td class="py-3.5 font-mono text-xs text-sky-400">WB-12345</td>
                <td class="py-3.5 text-slate-300">Cebu → Leyte</td>
                <td class="py-3.5 text-slate-300">Delivered late</td>
                <td class="py-3.5 text-right">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">Breached</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- FOOTER COPYRIGHT -->
      <p class="text-center text-[10px] text-slate-500 mt-8 pt-4 border-t border-white/5">© 2026 CargoNet Systems. Global Logistics Solutions.</p>

    </div>
  </main>

</div>

<!-- CLOCK SCRIPT -->
<script>
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