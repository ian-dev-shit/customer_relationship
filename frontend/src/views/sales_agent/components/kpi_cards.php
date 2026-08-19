<!-- ROW 1: TOP KPI METRICS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
  
  <!-- Active Leads -->
  <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
    <div class="flex justify-between items-start">
      <span class="text-xs font-bold text-slate-700">Active Leads</span>
      <div class="p-1.5 rounded-lg bg-purple-100 text-purple-600">
        <i class="fa-solid fa-user-group text-xs"></i>
      </div>
    </div>
    <div class="mt-3">
      <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)$total_leads) ?></p>
      <p class="text-[11px] text-emerald-600 font-semibold mt-2 flex items-center gap-1">
        ▲ <span><?= htmlspecialchars((string)$new_inquiry) ?> new inquiries</span>
      </p>
    </div>
  </div>

  <!-- AI Escalations Pending -->
  <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
    <div class="flex justify-between items-start">
      <span class="text-xs font-bold text-slate-700">AI Escalations Pending</span>
      <div class="p-1.5 rounded-lg bg-rose-100 text-rose-500">
        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
      </div>
    </div>
    <div class="mt-3">
      <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)$new_inquiry) ?></p>
      <p class="text-[11px] text-rose-600 font-semibold mt-2">
        <?= $new_inquiry > 0 ? $new_inquiry . ' inquiries need attention' : 'No pending actions' ?>
      </p>
    </div>
  </div>

  <!-- Total Revenue (MTD) -->
  <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
    <div class="flex justify-between items-start">
      <span class="text-xs font-bold text-slate-700">Total Revenue (MTD)</span>
      <div class="p-1.5 rounded-lg bg-purple-100 text-purple-600">
        <i class="fa-solid fa-coins text-xs"></i>
      </div>
    </div>
    <div class="mt-3">
      <p class="text-3xl font-extrabold text-slate-900">₱<?= number_format($revenue_current, 2) ?></p>
      <p class="text-[11px] font-semibold mt-2 flex items-center gap-1 <?= $revenue_diff >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
        <span><?= $revenue_diff >= 0 ? '▲' : '▼' ?></span>
        <span>₱<?= number_format(abs($revenue_diff), 2) ?> vs last month</span>
      </p>
    </div>
  </div>

  <!-- Customers Closed (MTD) -->
  <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
    <div class="flex justify-between items-start">
      <span class="text-xs font-bold text-slate-700">Customers Closed (MTD)</span>
      <div class="p-1.5 rounded-lg bg-emerald-100 text-emerald-600">
        <i class="fa-solid fa-user-check text-xs"></i>
      </div>
    </div>
    <div class="mt-3">
      <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)$customers_current) ?></p>
      <p class="text-[11px] font-semibold mt-2 flex items-center gap-1 <?= $customers_diff >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
        <span><?= $customers_diff >= 0 ? '▲' : '▼' ?></span>
        <span><?= abs($customers_diff) ?> vs last month</span>
      </p>
    </div>
  </div>

</div>