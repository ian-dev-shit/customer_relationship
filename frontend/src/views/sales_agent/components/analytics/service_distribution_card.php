<!-- SHARE OF CLOSED WON DEALS DONUT CARD -->
<div class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-between w-full h-auto">
  <div>
    <!-- HEADER -->
    <div class="flex items-center justify-between mb-2">
      <div>
        <h3 class="text-sm font-bold text-slate-800 tracking-tight">Closed Won Share by Service</h3>
        <p class="text-[11px] text-slate-400">Distribution of successful deal closures</p>
      </div>

      <!-- GEMINI AI BADGE -->
      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
        <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Gemini AI Powered
      </span>
    </div>

    <!-- DONUT CHART CONTAINER -->
    <div class="w-full min-w-0 flex justify-center py-2">
      <div id="serviceWonDonutChart" class="w-full min-h-[200px] flex justify-center items-center"></div>
    </div>
  </div>

  <!-- AI INSIGHT BOX -->
  <div class="mt-2 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-start gap-3">
    <div class="p-1.5 bg-indigo-600 text-white rounded-xl shrink-0 mt-0.5 shadow-sm">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
      </svg>
    </div>
    <div>
      <h4 class="text-[11px] font-bold text-slate-800 uppercase tracking-wider">AI Market Insights</h4>
      <p id="ai-service-donut-suggestion" class="text-xs text-slate-600 mt-0.5 leading-relaxed">
        Calculating service share metrics...
      </p>
    </div>
  </div>
</div>