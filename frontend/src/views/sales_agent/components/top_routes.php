<!-- 3-Column Grid Layout (Leads | Top Routes | Top Customers) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

  <!-- 1. LEADS MANAGEMENT CARD (Walang Pagination) -->
  <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
    <div>
      <div class="flex items-center justify-between mb-1">
        <h4 class="text-base font-bold text-gray-900">Leads Management</h4>
        <button class="text-gray-400 hover:text-gray-600">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
        </button>
      </div>
      <p class="text-xs text-gray-400 mb-5">Pipeline status distribution</p>

      <div id="leads-status-container" class="space-y-3.5">
        <div class="text-xs text-gray-400 text-center py-6">Loading status breakdown...</div>
      </div>
    </div>
  </div>

  <!-- 2. TOP ROUTES CARD (May Pagination) -->
  <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
    <div>
      <div class="flex items-center justify-between mb-1">
        <h4 class="text-base font-bold text-gray-900">Top Routes</h4>
        <button class="text-gray-400 hover:text-gray-600">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
        </button>
      </div>
      <p class="text-xs text-gray-400 mb-3">Most requested destinations</p>

      <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">
        <div class="relative z-0 isolate w-full h-36 rounded-xl overflow-hidden border border-gray-100 bg-indigo-50/30">
          <div id="routes-map" class="w-full h-full"></div>
        </div>
        <div id="top-routes-container" class="space-y-2 flex flex-col justify-center min-h-[144px]">
          <div class="text-xs text-gray-400 text-center py-4">Loading routes...</div>
        </div>
      </div>
    </div>

    <!-- Pagination Footer para sa Top Routes -->
    <div id="routes-pagination" class="hidden flex items-center justify-between pt-4 mt-4 border-t border-gray-100">
      <span id="routesPageIndicator" class="text-xs font-semibold text-gray-500">Page 1 of 1</span>
      <div class="flex items-center gap-1.5">
        <button id="prevRoutesBtn" onclick="changeRoutesPage(-1)" class="px-3 py-1 text-xs font-semibold rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition">‹ Prev</button>
        <button id="nextRoutesBtn" onclick="changeRoutesPage(1)" class="px-3 py-1 text-xs font-semibold rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition">Next ›</button>
      </div>
    </div>
  </div>

  <!-- 3. TOP CUSTOMERS CARD (May Pagination) -->
  <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
    <div>
      <div class="flex items-center justify-between mb-1">
        <h4 class="text-base font-bold text-gray-900">Top Customers</h4>
        <a href="customer.php" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">View All &rarr;</a>
      </div>
      <p class="text-xs text-gray-400 mb-5">Highest total bookings</p>

      <div id="top-customers-list" class="space-y-3 min-h-[200px]">
        <div class="p-4 rounded-xl bg-gray-50 text-center text-xs text-gray-400 animate-pulse">
          Loading top customers...
        </div>
      </div>
    </div>

    <!-- Pagination Footer para sa Top Customers -->
    <div id="customers-pagination" class="hidden flex items-center justify-between pt-4 mt-4 border-t border-gray-100">
      <span id="customersPageIndicator" class="text-xs font-semibold text-gray-500">Page 1 of 1</span>
      <div class="flex items-center gap-1.5">
        <button id="prevCustomersBtn" onclick="changeCustomersPage(-1)" class="px-3 py-1 text-xs font-semibold rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition">‹ Prev</button>
        <button id="nextCustomersBtn" onclick="changeCustomersPage(1)" class="px-3 py-1 text-xs font-semibold rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition">Next ›</button>
      </div>
    </div>
  </div>

</div>