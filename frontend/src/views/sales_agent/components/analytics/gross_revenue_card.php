<?php
$revenue_res  = make_api_request('/api/v1/analytics/gross-revenue', 'GET');
$revenue_data = $revenue_res['data'] ?? [];

$summary           = $revenue_data['summary'] ?? [];
$formatted_revenue = $summary['formatted_revenue'] ?? '₱0.00';
$chart_image       = $revenue_data['chart_image'] ?? '';
?>

<!-- CARD 1: TOTAL FREIGHT REVENUE -->
<div class="bg-[#ebf3fa] p-5 rounded-[2.2rem] shadow-sm w-[320px] h-[210px] flex flex-col justify-between">
  <div>
    <div class="flex items-center justify-between mb-1">
      <span class="text-slate-600 font-semibold text-xs tracking-wide">Total Freight Revenue</span>
      <button type="button" class="text-slate-400 hover:text-slate-600 transition" title="Closed-won deals revenue">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </button>
    </div>

    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight mb-2">
      <?= htmlspecialchars($formatted_revenue) ?>
    </h2>
  </div>

  <div class="w-full flex justify-center items-center">
    <?php if (!empty($chart_image)): ?>
      <img src="<?= $chart_image ?>" alt="Revenue Trend" class="w-full h-auto object-contain rounded-lg select-none" />
    <?php else: ?>
      <div class="py-4 text-center text-[10px] text-slate-400 font-medium">No chart data</div>
    <?php endif; ?>
  </div>
</div>