<!-- Pipeline Analytics Section -->
<div class="bg-white p-6 rounded-[2.2rem] shadow-sm border border-gray-100 flex flex-col justify-between h-full w-full min-h-[440px]">
  <div>
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-8">
        <div>
          <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Leads</span>
          <h3 id="chart-total-leads" class="text-2xl font-black text-gray-900 mt-0.5">0</h3>
        </div>
        <div>
          <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Won (MTD)</span>
          <h3 id="chart-won-mtd" class="text-2xl font-black text-indigo-600 mt-0.5">0</h3>
        </div>
      </div>

      <div>
        <select id="timeframe-select" class="text-xs bg-gray-50 border border-gray-200 text-gray-700 rounded-xl px-3 py-2 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
          <option value="30">Last 30 days</option>
          <option value="7">Last 7 days</option>
          <option value="90">Last 90 days</option>
        </select>
      </div>
    </div>

    <!-- Area Chart Container -->
    <div id="pipelineActivityChart" class="w-full min-h-[260px]"></div>
  </div>

  <!-- Stage Legend & Full Report Link -->
  <div class="pt-4 mt-2 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-gray-600" id="stage-legend">
      <span class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span> 
        New Inquiry <b id="cnt-new" class="text-gray-900 font-bold ml-0.5">0</b>
      </span>
      <span class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> 
        Qualifying <b id="cnt-qualifying" class="text-gray-900 font-bold ml-0.5">0</b>
      </span>
      <span class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span> 
        Quote Sent <b id="cnt-quote" class="text-gray-900 font-bold ml-0.5">0</b>
      </span>
      <span class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span> 
        Negotiation <b id="cnt-negotiation" class="text-gray-900 font-bold ml-0.5">0</b>
      </span>
      <span class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> 
        Won (MTD) <b id="cnt-won" class="text-gray-900 font-bold ml-0.5">0</b>
      </span>
    </div>

    <a href="reports.php" class="inline-flex items-center gap-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 rounded-xl transition-all shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      View full report
    </a>
  </div>
</div>