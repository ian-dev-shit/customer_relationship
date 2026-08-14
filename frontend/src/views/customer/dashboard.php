<?php
$page_title = "Customer Dashboard · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Fetch Dynamic Profile Data mula sa FastAPI
$profile_res = make_api_request('/api/v1/portal/profile', 'GET');
$raw_profile = $profile_res['data'] ?? [];
$profile     = $raw_profile['data'] ?? $raw_profile;

// 2. Fetch Dynamic Shipments Data
$shipments_res = make_api_request('/api/v1/portal/shipments', 'GET');
$shipments     = $shipments_res['data'] ?? [];

// 3. Extract Profile Fields & Metrics gamit ang Fallbacks
$customer_id     = $profile['customer_id'] ?? '8B41';
$company_name    = $profile['company_name'] ?? 'Charlie Hub Inc.';
$contract_status = $profile['status'] ?? 'Newly Onboarded';
$metrics         = $profile['metrics'] ?? [
    'active_shipments' => 0, 
    'in_transit'       => 0, 
    'delayed'          => 0, 
    'delivered_30d'    => 0
];
$documents       = $profile['initial_documents'] ?? $profile['documents'] ?? [];

// Helper function para sa dynamic status pill badging
function getStatusBadgeClass($status) {
    switch (strtolower(trim($status))) {
        case 'in transit':
        case 'in_transit':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'customs':
        case 'customs clearance':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'delivered':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'delayed':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
}
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
      <button onclick="toggleSidebar()" class="sm:hidden text-slate-600 hover:text-slate-900 p-1">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
      </button>
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight italic">Dashboard</h1>
        <p class="text-xs text-slate-500 font-medium"><?= htmlspecialchars($company_name) ?> (ID: <?= htmlspecialchars($customer_id) ?>)</p>
      </div>
    </div>

    <!-- Global Search Bar -->
    <div class="flex-1 max-w-md mx-4 hidden md:block">
      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
        </span>
        <input type="text" placeholder="Track a waybill, invoice, or document..." 
               class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
      </div>
    </div>

    <!-- Header Actions -->
    <div class="flex items-center gap-3">
      <button type="button" class="p-2 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-slate-800 shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
      </button>

      <a href="/helpdesk" class="px-3 py-2 text-xs font-semibold rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center gap-1.5 transition">
        <span>Help Desk</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/></svg>
      </a>

      <a href="/shipments/book" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-sm flex items-center gap-1.5 transition">
        <span class="text-base font-bold leading-none">+</span> Book Shipment
      </a>
    </div>
  </div>

  <!-- ROW 1: TOP KPI METRICS (DYNAMIC) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Active Shipments -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex justify-between items-start">
        <span class="text-xs font-medium text-slate-500">Active Shipments</span>
        <div class="p-1.5 rounded-lg bg-blue-50 text-blue-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
        </div>
      </div>
      <div class="mt-3">
        <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)($metrics['active_shipments'] ?? 0)) ?></p>
        <p class="text-xs text-emerald-600 font-semibold mt-2 flex items-center gap-1">
          ▲ <span>Active in pipeline</span>
        </p>
      </div>
    </div>

    <!-- In Transit -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex justify-between items-start">
        <span class="text-xs font-medium text-slate-500">In Transit</span>
        <div class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v11.135m12 0V11.25"/></svg>
        </div>
      </div>
      <div class="mt-3">
        <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)($metrics['in_transit'] ?? 0)) ?></p>
        <p class="text-xs text-slate-500 font-medium mt-2">Currently moving</p>
      </div>
    </div>

    <!-- Delayed -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex justify-between items-start">
        <span class="text-xs font-medium text-slate-500">Delayed</span>
        <div class="p-1.5 rounded-lg bg-rose-50 text-rose-500">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
      <div class="mt-3">
        <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)($metrics['delayed'] ?? 0)) ?></p>
        <p class="text-xs text-rose-600 font-semibold mt-2 flex items-center gap-1">
          ⚠️ <span>Requires attention</span>
        </p>
      </div>
    </div>

    <!-- Delivered (30d) -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex justify-between items-start">
        <span class="text-xs font-medium text-slate-500">Delivered (30d)</span>
        <div class="p-1.5 rounded-lg bg-amber-50 text-amber-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
      <div class="mt-3">
        <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)($metrics['delivered_30d'] ?? 0)) ?></p>
        <p class="text-xs text-emerald-600 font-semibold mt-2 flex items-center gap-1">
          ✓ <span>Completed past month</span>
        </p>
      </div>
    </div>
  </div>

  <!-- ROW 2: MAIN DASHBOARD GRID -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- SHIPMENT MANIFEST TABLE (DYNAMIC) -->
    <div class="lg:col-span-8 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-base font-bold text-slate-900">Shipment Manifest</h2>
          <p class="text-xs text-slate-400 mt-0.5">Live status across all active waybills</p>
        </div>
        <a href="/shipments" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View all →</a>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 pb-3">
              <th class="pb-3">WAYBILL</th>
              <th class="pb-3">DETAILS</th>
              <th class="pb-3">STATUS</th>
              <th class="pb-3">ETA</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs font-medium">
            <?php if (!empty($shipments)): ?>
              <?php foreach ($shipments as $item): ?>
                <?php 
                  $waybill    = $item['waybill_number'] ?? $item['id'] ?? 'N/A';
                  $type       = $item['type'] ?? 'Standard Cargo';
                  $status     = $item['status'] ?? 'Pending';
                  $eta        = $item['eta'] ?? 'TBD';
                  $origin     = $item['origin'] ?? 'Origin';
                  $dest       = $item['destination'] ?? 'Destination';
                  $badgeClass = getStatusBadgeClass($status);
                ?>
                <tr class="hover:bg-slate-50 transition">
                  <td class="py-4">
                    <p class="font-bold text-slate-800"><?= htmlspecialchars($waybill) ?></p>
                    <p class="text-[11px] text-slate-400 font-normal"><?= htmlspecialchars($type) ?></p>
                  </td>
                  <td class="py-4">
                    <p class="text-slate-700 font-medium"><?= htmlspecialchars($origin) ?> → <?= htmlspecialchars($dest) ?></p>
                  </td>
                  <td class="py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border <?= $badgeClass ?>">
                      • <?= htmlspecialchars($status) ?>
                    </span>
                  </td>
                  <td class="py-4 font-mono text-slate-600">
                    <?= htmlspecialchars($eta) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                  No active shipments found.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- RIGHT COLUMN WIDGETS -->
    <div class="lg:col-span-4 space-y-6">

      <!-- SLA HEALTH WIDGET -->
      <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h2 class="text-base font-bold text-slate-900">SLA Health</h2>
        <p class="text-xs text-slate-400 mb-6">By service commitment</p>

        <div class="space-y-4">
          <div>
            <div class="flex justify-between text-xs font-semibold mb-1.5">
              <span class="text-slate-700">On-time Pickup</span>
              <span class="text-emerald-600">97%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
              <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 97%;"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between text-xs font-semibold mb-1.5">
              <span class="text-slate-700">Transit Time</span>
              <span class="text-emerald-600">92%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
              <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 92%;"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between text-xs font-semibold mb-1.5">
              <span class="text-slate-700">Customs Clearance</span>
              <span class="text-amber-600">78%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
              <div class="bg-amber-500 h-1.5 rounded-full" style="width: 78%;"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between text-xs font-semibold mb-1.5">
              <span class="text-slate-700">Damage-free Delivery</span>
              <span class="text-emerald-600">99%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
              <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 99%;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- RECENT DOCUMENTS WIDGET (DYNAMIC) -->
      <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-base font-bold text-slate-900">Recent Documents</h2>
            <p class="text-xs text-slate-400">Contracts, SLA & waybills</p>
          </div>
          <a href="/documents" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Open →</a>
        </div>

        <div class="space-y-3">
          <?php if (!empty($documents)): ?>
            <?php foreach ($documents as $doc): ?>
              <?php 
                $doc_title = $doc['title'] ?? $doc['name'] ?? 'Document.pdf';
                $doc_type  = $doc['doc_type'] ?? 'PDF';
                $uploaded  = $doc['uploaded_by'] ?? 'Admin';
              ?>
              <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50/50 hover:bg-blue-50 transition border border-blue-100/50">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="p-2 rounded bg-blue-100 text-blue-600 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3h7.5M6 20.25h12A2.25 2.25 0 0020.25 18V8.25A2.25 2.25 0 0018 6H6A2.25 2.25 0 003.75 8.25v9.75A2.25 2.25 0 006 20.25z"/></svg>
                  </div>
                  <div class="truncate">
                    <p class="text-xs font-bold text-slate-800 truncate"><?= htmlspecialchars($doc_title) ?></p>
                    <p class="text-[10px] text-slate-400"><?= htmlspecialchars($doc_type) ?> • By <?= htmlspecialchars($uploaded) ?></p>
                  </div>
                </div>
                <button class="text-slate-400 hover:text-slate-700 shrink-0 ml-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                </button>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-xs text-slate-400 italic py-2 text-center">No documents uploaded yet.</p>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </div>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>