<!-- Top KPI Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
  
  <!-- Active Leads -->
  <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-2">
      <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Active Leads</span>
      <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
      </div>
    </div>
    <div class="flex flex-col items-start gap-1">
      <h3 id="kpi-active-leads" class="text-3xl font-black text-gray-900">--</h3>
      <span class="text-[11px] font-semibold text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">Pipeline Active</span>
    </div>
  </div>

  <!-- Conversion Rate -->
  <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-2">
      <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Conversion Rate</span>
      <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
      </div>
    </div>
    <!-- Nasa ILALIM ng numero ang % growth badge -->
    <div class="flex flex-col items-start gap-1">
      <h3 id="kpi-conversion-rate" class="text-3xl font-black text-indigo-600">--%</h3>
      <span id="growth-conversion" class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-400">
        --
      </span>
    </div>
  </div>

  <!-- Total Revenue MTD -->
  <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-2">
      <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Revenue (MTD)</span>
      <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
    </div>
    <!-- Nasa ILALIM ng numero ang % growth badge -->
    <div class="flex flex-col items-start gap-1">
      <h3 id="kpi-revenue" class="text-2xl font-black text-gray-900">₱0.00</h3>
      <span id="growth-revenue" class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-400">
        --
      </span>
    </div>
  </div>

  <!-- Customers Closed -->
  <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-2">
      <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Customers Closed</span>
      <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
    </div>
    <!-- Nasa ILALIM ng numero ang % growth badge -->
    <div class="flex flex-col items-start gap-1">
      <h3 id="kpi-closed" class="text-3xl font-black text-purple-600">--</h3>
      <span id="growth-closed" class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-400">
        --
      </span>
    </div>
  </div>

</div>

<!-- Lower Chart & Priority Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
  <!-- Revenue Performance & ML Forecast Chart -->
  <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
  <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
      <div class="flex items-center gap-2">
        <h4 class="text-base font-bold text-gray-900">Revenue Performance & ML Forecast</h4>
        <span class="text-[11px] bg-indigo-50 text-indigo-600 px-2.5 py-0.5 rounded-full font-semibold">Scikit-Learn ML</span>
      </div>
      <p class="text-xs text-gray-400 mt-0.5">Actual sales vs Scikit-Learn predicted trend</p>
    </div>

    <!-- Timeframe Filter Pills  -->
    <div class="flex items-center bg-gray-50/80 p-1 rounded-xl border border-gray-100 text-xs font-semibold text-gray-500 gap-1">
      <button class="px-3 py-1 rounded-lg hover:text-gray-900 transition-all">1D</button>
      <button class="px-3 py-1 rounded-lg hover:text-gray-900 transition-all">1W</button>
      <button class="px-3 py-1 rounded-lg hover:text-gray-900 transition-all">1M</button>
      <button class="px-3 py-1 rounded-lg hover:text-gray-900 transition-all">6M</button>
      <button class="px-3 py-1 bg-white text-gray-900 rounded-lg shadow-sm transition-all">1Y</button>
      <button class="px-3 py-1 rounded-lg hover:text-gray-900 transition-all">ALL</button>
    </div>
  </div>

  <!-- Chart Canvas Container -->
  <div id="revenueForecastChart" class="w-full min-h-[300px]"></div>
</div>

  <!-- Priority Action Needed Container -->
  <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
  <div>
    <!-- Header Area (Malinis na ulit) -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <h4 class="text-base font-bold text-gray-900">Priority Follow-Ups</h4>
        <p class="text-xs text-gray-500">ML-predicted urgent inquiries requiring action</p>
      </div>
      <span class="px-2.5 py-1 text-[10px] font-semibold bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100">
        Smart Alert
      </span>
    </div>

    <!-- Weekly Calendar Strip -->
    <div class="mb-5 pb-4 border-b border-gray-100">
      <div class="flex items-center justify-between text-xs font-semibold text-gray-700 mb-3" id="calendar-month-year">
        <!-- Dynamic Month Year -->
      </div>
      <div class="grid grid-cols-7 gap-1 text-center" id="calendar-days-strip">
        <!-- Dynamic Days Strip -->
      </div>
    </div>

    <!-- Priority Follow-Up Cards List -->
    <div id="priority-list" class="space-y-3">
      <div class="p-4 bg-slate-50 rounded-xl text-xs text-gray-400 text-center py-6">
        Analyzing inquiry activities...
      </div>
    </div>
  </div>

  <!-- PAGINATION CONTROL SA ILALIM -->
  <div id="followup-pagination" class="hidden flex items-center justify-between pt-4 mt-4 border-t border-gray-100">
    <span id="followupPageIndicator" class="text-xs font-semibold text-gray-500">Page 1 of 1</span>
    
    <div class="flex items-center gap-1.5">
      <button id="prevFollowupBtn" onclick="changeFollowupPage(-1)" class="px-3 py-1 text-xs font-semibold rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition">
        ‹ Prev
      </button>
      <button id="nextFollowupBtn" onclick="changeFollowupPage(1)" class="px-3 py-1 text-xs font-semibold rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition">
        Next ›
      </button>
    </div>
  </div>
</div>
</div>

