<?php
$page_title = "BI Analytics · Rising Red Dragon";

include_once '../../includes/header.php';
?>

<div class="app-container">

  <!-- SIDEBAR INCLUDE -->
  <?php include_once '../../includes/sidebar.php'; ?>

  <!-- MAIN CONTENT – BI ANALYTICS -->
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
          <h1 class="text-2xl font-bold text-white tracking-tight">BI Analytics</h1>
          <p class="text-sm text-slate-400 mt-0.5">BI Analytics - Performance Monitoring</p>
        </div>
        <!-- Session time -->
        <div class="text-right">
          <p class="text-xs text-slate-500">Session Active</p>
          <p class="text-sm font-mono text-sky-400 font-semibold" id="sessionTime">5:38:45 PM</p>
        </div>
      </div>

      <!-- CHARTS IN TWO-COLUMN LAYOUT -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- CHART 1: Monthly Shipment Volume -->
        <div class="glass-card rounded-2xl p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Monthly Shipment Volume</h2>
            <div class="flex items-center gap-2 text-xs">
              <span class="flex items-center gap-1 text-emerald-400"><span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span> Trend</span>
            </div>
          </div>
          <div class="chart-wrapper">
            <canvas id="shipmentVolumeChart"></canvas>
          </div>
          <!-- Legend / data labels -->
          <div class="mt-4 flex flex-wrap items-center justify-center gap-3 text-xs text-slate-400">
            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded" style="background:#3b82f6;"></span> Volume</span>
            <span class="text-slate-600">|</span>
            <span>Feb 38</span>
            <span class="text-slate-600">|</span>
            <span>Mar 44</span>
            <span class="text-slate-600">|</span>
            <span>Apr 41</span>
            <span class="text-slate-600">|</span>
            <span>May 52</span>
            <span class="text-slate-600">|</span>
            <span>Jun 48</span>
            <span class="text-slate-600">|</span>
            <span>Jul 58</span>
          </div>
        </div>

        <!-- CHART 2: On-Time Delivery Rate -->
        <div class="glass-card rounded-2xl p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">On-Time Delivery Rate</h2>
            <div class="flex items-center gap-2 text-xs">
              <span class="flex items-center gap-1 text-emerald-400"><span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span> Trend</span>
            </div>
          </div>
          <div class="chart-wrapper">
            <canvas id="deliveryRateChart"></canvas>
          </div>
          <!-- Legend / data labels -->
          <div class="mt-4 flex flex-wrap items-center justify-center gap-3 text-xs text-slate-400">
            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded" style="background:#f97316;"></span> Rate %</span>
            <span class="text-slate-600">|</span>
            <span>Feb 92%</span>
            <span class="text-slate-600">|</span>
            <span>Mar 93%</span>
            <span class="text-slate-600">|</span>
            <span>Apr 91%</span>
            <span class="text-slate-600">|</span>
            <span>May 94%</span>
            <span class="text-slate-600">|</span>
            <span>Jun 95%</span>
            <span class="text-slate-600">|</span>
            <span>Jul 96%</span>
          </div>
        </div>

      </div>

      <!-- TREND SUMMARY CARDS -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Volume trend -->
        <div class="glass-card rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-400">Shipment Volume Trend</p>
            <p class="text-sm text-white font-medium">+20 from Feb to Jul</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-emerald-400 text-2xl">↑</span>
            <span class="text-emerald-400 font-bold">+52.6%</span>
          </div>
        </div>
        <!-- Delivery rate trend -->
        <div class="glass-card rounded-2xl p-4 flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-400">Delivery Rate Trend</p>
            <p class="text-sm text-white font-medium">+4% from Feb to Jul</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-emerald-400 text-2xl">↑</span>
            <span class="text-emerald-400 font-bold">+4.3%</span>
          </div>
        </div>
      </div>

      <!-- FOOTER COPYRIGHT -->
      <p class="text-center text-[10px] text-slate-500 mt-8 pt-4 border-t border-white/5">© 2026 CargoNet Systems. Global Logistics Solutions.</p>

    </div>
  </main>

</div>

<!-- CHART.JS LOGIC FOR ANALYTICS -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const months = ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
    const volumeData = [38, 44, 41, 52, 48, 58];
    const rateData = [92, 93, 91, 94, 95, 96];

    // Chart 1: Shipment Volume
    const ctx1 = document.getElementById('shipmentVolumeChart').getContext('2d');
    new Chart(ctx1, {
      type: 'line',
      data: {
        labels: months,
        datasets: [{
          label: 'Shipment Volume',
          data: volumeData,
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59, 130, 246, 0.15)',
          borderWidth: 3,
          fill: true,
          tension: 0.3,
          pointBackgroundColor: '#3b82f6',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: {
            beginAtZero: true,
            max: 70,
            grid: { color: 'rgba(255,255,255,0.05)' },
            ticks: { color: '#94a3b8', stepSize: 10 }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#94a3b8', font: { weight: '500' } }
          }
        },
        interaction: { intersect: false, mode: 'index' }
      }
    });

    // Chart 2: Delivery Rate
    const ctx2 = document.getElementById('deliveryRateChart').getContext('2d');
    new Chart(ctx2, {
      type: 'line',
      data: {
        labels: months,
        datasets: [{
          label: 'Delivery Rate (%)',
          data: rateData,
          borderColor: '#f97316',
          backgroundColor: 'rgba(249, 115, 22, 0.15)',
          borderWidth: 3,
          fill: true,
          tension: 0.3,
          pointBackgroundColor: '#f97316',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: {
            beginAtZero: false,
            min: 85,
            max: 100,
            grid: { color: 'rgba(255,255,255,0.05)' },
            ticks: { color: '#94a3b8', stepSize: 2 }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#94a3b8', font: { weight: '500' } }
          }
        },
        interaction: { intersect: false, mode: 'index' }
      }
    });
  });
</script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>