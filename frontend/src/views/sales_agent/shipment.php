<?php
$page_title = "Shipment Tracking - Sales Agent Dashboard";
$activePage = 'shipment';

/* Static demo dataset (demo data only, no backend dependency). */
$shipments = [
    ['waybill' => 'PH-WB-208841', 'customer' => 'Cebu Retail Inc.', 'type' => '40ft container · Reefer', 'origin' => 'Manila', 'destination' => 'Cebu', 'carrier' => 'Trans-Pacific Lines', 'service' => 'Sea Freight', 'status' => 'in-transit', 'eta' => 'Jul 29, 14:00', 'progress' => 62],
    ['waybill' => 'PH-WB-208835', 'customer' => 'Acme Foods Inc.', 'type' => '20ft container · Dry van', 'origin' => 'Cebu', 'destination' => 'Manila', 'carrier' => '2GO Freight', 'service' => 'Sea Freight', 'status' => 'customs', 'eta' => 'Jul 30, 09:00', 'progress' => 45],
    ['waybill' => 'PH-WB-208812', 'customer' => 'Davao Fresh Mart', 'type' => 'LCL · Break-bulk', 'origin' => 'Manila', 'destination' => 'Davao', 'carrier' => 'Sulpicio Lines', 'service' => 'Sea Freight', 'status' => 'delivered', 'eta' => 'Jul 25, 11:20', 'progress' => 100],
    ['waybill' => 'PH-WB-208712', 'customer' => 'Iloilo Cold Storage', 'type' => '40ft container · Dry van', 'origin' => 'Manila', 'destination' => 'Iloilo', 'carrier' => 'Sulpicio Lines', 'service' => 'Sea Freight', 'status' => 'delayed', 'eta' => 'Jul 27, 18:00', 'progress' => 38],
    ['waybill' => 'PH-WB-208699', 'customer' => 'Cagayan Valley Foods', 'type' => '20ft container · Reefer', 'origin' => 'Manila', 'destination' => 'Cagayan de Oro', 'carrier' => '2GO Freight', 'service' => 'Sea Freight', 'status' => 'in-transit', 'eta' => 'Aug 02, 07:30', 'progress' => 55],
    ['waybill' => 'PH-WB-208650', 'customer' => 'Bacolod Grocery Corp', 'type' => 'FCL · Dry van', 'origin' => 'Manila', 'destination' => 'Bacolod', 'carrier' => 'Trans-Pacific Lines', 'service' => 'Sea Freight', 'status' => 'in-transit', 'eta' => 'Jul 31, 10:00', 'progress' => 71],
    ['waybill' => 'PH-WB-208611', 'customer' => 'Laguna Distributors', 'type' => 'LCL · Parcels', 'origin' => 'Pasig', 'destination' => 'Binan, Laguna', 'carrier' => '2GO Freight', 'service' => 'Land Freight', 'status' => 'customs', 'eta' => 'Jul 29, 16:45', 'progress' => 30],
    ['waybill' => 'PH-WB-208590', 'customer' => 'Pampanga Grocery Corp', 'type' => '20ft container · Dry van', 'origin' => 'Cavite', 'destination' => 'San Fernando', 'carrier' => 'Sulpicio Lines', 'service' => 'Land Freight', 'status' => 'delivered', 'eta' => 'Jul 22, 09:10', 'progress' => 100],
];
foreach ($shipments as &$s) { $s['route'] = $s['origin'] . ' → ' . $s['destination']; }
unset($s);
$counts = ['all' => count($shipments), 'in-transit' => 0, 'customs' => 0, 'delayed' => 0, 'delivered' => 0];
foreach ($shipments as $s) { if (isset($counts[$s['status']])) $counts[$s['status']]++; }
$upcoming = array_filter($shipments, fn($s) => $s['status'] !== 'delivered');
usort($upcoming, fn($a, $b) => strtotime($a['eta']) <=> strtotime($b['eta']));
$carriers = [];
foreach ($shipments as $s) { $carriers[$s['carrier']] = ($carriers[$s['carrier']] ?? 0) + 1; }
arsort($carriers);
$carrier_max = !empty($carriers) ? max($carriers) : 1;
function shipmentBadge($status) {
    switch ($status) {
        case 'in-transit': return ['label' => 'In Transit', 'class' => 'bg-blue-50 text-blue-700 border-blue-200', 'dot' => 'bg-blue-500'];
        case 'customs': return ['label' => 'Customs', 'class' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'];
        case 'delayed': return ['label' => 'Delayed', 'class' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500'];
        case 'delivered': return ['label' => 'Delivered', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'];
        default: return ['label' => ucfirst($status), 'class' => 'bg-slate-50 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'];
    }
}
function carrierInitials($name) {
    $p = preg_split('/\s+/', trim($name));
    return strtoupper(substr($p[0] ?? '', 0, 1) . (isset($p[1]) ? substr($p[1], 0, 1) : ''));
}
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>
<main data-brand="priority" class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">
<?php
$header_title = "Shipment Tracking";
$header_subtitle = "Monitor every waybill assigned to your accounts — live status, ETAs and exceptions.";
$header_actions = '<a href="book_shipment.php" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-2"><i class="fa-solid fa-plus text-[10px]"></i><span>Book Shipment</span></a>';
 include_once '../../components/top_header.php'; 
?>
<div class="p-6 lg:p-8 space-y-6">
<?php if ($counts['delayed'] > 0): ?>
<section class="flex items-center gap-4 bg-rose-50 border border-rose-200 rounded-2xl p-4 lg:px-6 shadow-sm">
<div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-triangle-exclamation text-base"></i></div>
<div class="min-w-0 flex-1">
<p class="text-sm font-bold text-rose-800"><?= $counts['delayed'] ?> shipment<?= $counts['delayed'] > 1 ? 's' : '' ?> need<?= $counts['delayed'] > 1 ? '' : 's' ?> your attention</p>
<p class="text-xs text-rose-600/80 mt-0.5">Delayed waybills may miss their delivery window. Update the customer before they chase you.</p>
</div>
<button onclick="filterShipments('delayed', document.querySelector('.filter-tab[data-status-key=\'delayed\']'))" class="text-xs font-semibold text-rose-700 bg-white border border-rose-200 px-3.5 py-2 rounded-xl shrink-0 hidden sm:block">View delayed &rarr;</button>
</section>
<?php endif; ?>
<section class="grid grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-5">
<?php
$kpis = [
['key'=>'all','label'=>'Total Shipments','icon'=>'fa-boxes-stacked','chip'=>'bg-blue-50 text-brand-blue','note'=>'All waybills','noteClass'=>'text-slate-500'],
['key'=>'in-transit','label'=>'In Transit','icon'=>'fa-truck-fast','chip'=>'bg-blue-50 text-blue-600','note'=>'On the move','noteClass'=>'text-blue-600 font-semibold'],
['key'=>'customs','label'=>'In Customs','icon'=>'fa-file-invoice','chip'=>'bg-amber-50 text-amber-600','note'=>'Clearance pending','noteClass'=>'text-amber-600 font-semibold'],
['key'=>'delayed','label'=>'Delayed','icon'=>'fa-triangle-exclamation','chip'=>'bg-rose-50 text-rose-500','note'=>'Needs attention','noteClass'=>'text-rose-600 font-semibold'],
['key'=>'delivered','label'=>'Delivered (30d)','icon'=>'fa-circle-check','chip'=>'bg-emerald-50 text-emerald-600','note'=>'Completed','noteClass'=>'text-emerald-600 font-semibold'],
];
foreach ($kpis as $k): ?>
<div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
<div class="flex justify-between items-start"><span class="text-xs font-medium text-slate-500"><?= $k['label'] ?></span><div class="p-2 rounded-xl <?= $k['chip'] ?>"><i class="fa-solid <?= $k['icon'] ?> text-sm"></i></div></div>
<div class="mt-4"><p class="text-3xl font-extrabold text-slate-900"><?= $counts[$k['key']] ?></p><p class="text-xs <?= $k['noteClass'] ?> mt-2"><?= $k['note'] ?></p></div>
</div>
<?php endforeach; ?>
</section>
<section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
<div class="lg:col-span-8 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
<div class="p-6 flex flex-wrap justify-between items-start gap-4">
<div><h2 class="text-base font-bold text-slate-900">Shipment Manifest</h2><p class="text-xs text-slate-400 mt-0.5">Full waybill history across your assigned accounts.</p></div>
<div class="flex items-center gap-2">
<div class="relative"><i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[11px]"></i>
<input id="shipmentSearchInput" onkeyup="searchShipmentsTable()" type="text" placeholder="Search waybill, customer, route..." class="bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-xs w-56 focus:outline-none focus:border-brand-blue"></div>
<button onclick="exportShipmentsCSV()" class="text-xs font-semibold text-brand-blue bg-blue-50 border border-blue-100 px-3 py-2 rounded-xl"><i class="fa-solid fa-file-export"></i> Export CSV</button>
</div></div>
<div class="px-6 pb-5"><div class="flex flex-wrap items-center gap-1.5">
<button data-status-key="all" onclick="filterShipments('all', this)" class="filter-tab crm-pill is-active">All (<?= $counts['all'] ?>)</button>
<button data-status-key="in-transit" onclick="filterShipments('in-transit', this)" class="filter-tab crm-pill">In Transit (<?= $counts['in-transit'] ?>)</button>
<button data-status-key="customs" onclick="filterShipments('customs', this)" class="filter-tab crm-pill">Customs (<?= $counts['customs'] ?>)</button>
<button data-status-key="delayed" onclick="filterShipments('delayed', this)" class="filter-tab crm-pill">Delayed (<?= $counts['delayed'] ?>)</button>
<button data-status-key="delivered" onclick="filterShipments('delivered', this)" class="filter-tab crm-pill">Delivered (<?= $counts['delivered'] ?>)</button>
</div></div>
<div class="overflow-x-auto"><table class="w-full text-left text-xs" id="shipmentsTable">
<thead><tr class="border-y border-slate-100 bg-slate-50/60 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
<th class="py-3 px-6">Waybill</th><th class="py-3 px-6">Customer</th><th class="py-3 px-6">Route</th><th class="py-3 px-6">Carrier</th><th class="py-3 px-6">Status</th><th class="py-3 px-6 text-right">ETA</th><th class="py-3 px-6 text-center">Action</th>
</tr></thead>
<tbody class="divide-y divide-slate-100" id="shipmentsTbody">
<?php foreach ($shipments as $s): $b = shipmentBadge($s['status']); $etaClass = $s['status']==='delayed' ? 'text-rose-500' : 'text-slate-700'; ?>
<tr class="shipment-row hover:bg-slate-50 transition-colors" id="row-<?= htmlspecialchars($s['waybill']) ?>" data-status="<?= $s['status'] ?>">
<td class="py-4 px-6"><strong class="font-mono text-slate-900 text-xs block"><?= htmlspecialchars($s['waybill']) ?></strong><span class="text-[10px] text-slate-400"><?= htmlspecialchars($s['type']) ?></span></td>
<td class="py-4 px-6 font-semibold text-slate-800 whitespace-nowrap"><?= htmlspecialchars($s['customer']) ?></td>
<td class="py-4 px-6"><span class="font-semibold text-slate-800 whitespace-nowrap"><?= htmlspecialchars($s['route']) ?></span><span class="block text-[10px] text-slate-400"><?= htmlspecialchars($s['service']) ?></span></td>
<td class="py-4 px-6"><span class="inline-flex items-center gap-2 text-slate-600 whitespace-nowrap"><span class="w-6 h-6 rounded-lg bg-navy-50 text-navy-700 text-[9px] font-extrabold flex items-center justify-center"><?= carrierInitials($s['carrier']) ?></span><?= htmlspecialchars($s['carrier']) ?></span></td>
<td class="py-4 px-6"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border whitespace-nowrap <?= $b['class'] ?>"><span class="w-1.5 h-1.5 rounded-full <?= $b['dot'] ?>"></span><?= $b['label'] ?></span></td>
<td class="py-4 px-6 text-right font-mono font-medium whitespace-nowrap <?= $etaClass ?>"><?= htmlspecialchars($s['eta']) ?></td>
<td class="py-4 px-6 text-center"><button type="button" onclick='openShipmentModal(<?= json_encode($s, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="px-3 py-1.5 bg-navy-700 hover:bg-navy-800 text-white font-bold rounded-lg text-[11px] transition-all active:scale-95 shadow-sm inline-flex items-center gap-1.5"><i class="fa-solid fa-radar text-[10px]"></i> Track</button></td>
</tr>
<?php endforeach; ?>
<tr id="shipmentEmptyState" class="hidden"><td colspan="7" class="py-10 px-6 text-center text-xs text-slate-400"><i class="fa-solid fa-box-open text-2xl text-slate-200 block mb-2"></i>No shipments match this filter.</td></tr>
</tbody></table></div>
</div>
<div class="lg:col-span-4 space-y-6">
<div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
<h2 class="text-base font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-location-crosshairs text-brand-blue"></i> Quick Track</h2>
<p class="text-xs text-slate-400 mt-0.5 mb-4">Type a waybill to jump to its timeline.</p>
<form onsubmit="quickTrack(event)" class="space-y-3">
<div class="relative"><i class="fa-solid fa-barcode absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
<input id="quickTrackInput" list="waybillList" type="text" placeholder="Enter waybill e.g. PH-WB-208841" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-xs focus:outline-none focus:border-brand-blue"></div>
<datalist id="waybillList"><?php foreach ($shipments as $s): ?><option value="<?= htmlspecialchars($s['waybill']) ?>"><?= htmlspecialchars($s['customer'] . ' · ' . $s['route']) ?></option><?php endforeach; ?></datalist>
<button type="submit" class="w-full bg-navy-700 hover:bg-navy-800 text-white font-semibold text-xs py-2.5 rounded-xl shadow-md flex items-center justify-center gap-2"><i class="fa-solid fa-radar"></i> Track Now</button>
</form></div>
<div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
<h2 class="text-base font-bold text-slate-900">Status Distribution</h2>
<p class="text-xs text-slate-400 mt-0.5 mb-4">Snapshot across your waybills</p>
<div class="relative h-56 flex items-center justify-center"><canvas id="shipmentStatusChart"></canvas></div>
</div>
<div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
<div class="flex items-center justify-between mb-4"><div><h2 class="text-base font-bold text-slate-900">Upcoming Deliveries</h2><p class="text-xs text-slate-400 mt-0.5">Next ETAs, soonest first</p></div><a href="book_shipment.php" class="text-xs font-semibold text-brand-blue">Book &rarr;</a></div>
<div class="relative pl-4 space-y-5 before:absolute before:inset-y-1 before:left-[5px] before:w-px before:bg-slate-200">
<?php foreach (array_slice($upcoming, 0, 5) as $u): $b = shipmentBadge($u['status']); ?>
<div class="relative"><span class="absolute -left-4 top-1.5 w-2.5 h-2.5 rounded-full <?= $b['dot'] ?> ring-4 ring-white"></span>
<div class="flex items-center justify-between gap-2"><p class="text-xs font-bold font-mono text-slate-800"><?= htmlspecialchars($u['waybill']) ?></p><span class="text-[10px] font-bold px-2 py-0.5 rounded-full border <?= $b['class'] ?>"><?= $b['label'] ?></span></div>
<p class="text-[11px] text-slate-500 mt-0.5"><?= htmlspecialchars($u['customer']) ?> · <?= htmlspecialchars($u['route']) ?></p>
<p class="text-[11px] font-semibold text-slate-700 mt-0.5"><i class="fa-regular fa-clock text-slate-300 mr-1"></i><?= htmlspecialchars($u['eta']) ?></p></div>
<?php endforeach; ?>
</div></div>
<div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
<h2 class="text-base font-bold text-slate-900">Carrier Mix</h2>
<p class="text-xs text-slate-400 mt-0.5 mb-4">Which carriers carry your volume</p>
<div class="space-y-3"><?php foreach ($carriers as $name => $n): ?>
<div><div class="flex items-center justify-between text-xs mb-1"><span class="flex items-center gap-2 font-semibold text-slate-700"><span class="w-6 h-6 rounded-lg bg-navy-50 text-navy-700 text-[9px] font-extrabold flex items-center justify-center"><?= carrierInitials($name) ?></span><?= htmlspecialchars($name) ?></span><span class="font-bold text-slate-800"><?= $n ?></span></div>
<div class="h-1.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full bg-navy-700" style="width: <?= round(($n / $carrier_max) * 100) ?>%"></div></div></div>
<?php endforeach; ?></div></div>
<div class="rounded-2xl border border-navy-800 shadow-sm overflow-hidden" style="background: linear-gradient(120deg, #1d2e6a 0%, #084163 55%, #003a5f 100%);">
<div class="p-6"><h2 class="text-base font-bold text-white">Move freight faster</h2>
<p class="text-xs text-white/70 mt-0.5 mb-4">Quote, book, or check a customer in one click.</p>
<div class="grid grid-cols-3 gap-2">
<a href="book_shipment.php" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-white"><i class="fa-solid fa-box-archive"></i><span class="text-[11px] font-semibold">Book</span></a>
<a href="rates.php" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-white"><i class="fa-solid fa-calculator"></i><span class="text-[11px] font-semibold">Rates</span></a>
<a href="customer.php" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-white"><i class="fa-solid fa-users"></i><span class="text-[11px] font-semibold">Clients</span></a>
</div></div></div>
</div>
</section>
</div>
</main>
<div id="shipmentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
<div class="absolute inset-0 bg-navy-950/60 backdrop-blur-sm" onclick="closeShipmentModal()"></div>
<div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
<div class="p-6 pb-4 flex items-start justify-between gap-4 border-b border-slate-100">
<div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Waybill</p>
<h3 id="mWaybill" class="text-lg font-extrabold font-mono text-slate-900">—</h3>
<p id="mCustomer" class="text-xs text-slate-500 mt-0.5">—</p></div>
<span id="mStatus" class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold border whitespace-nowrap">—</span>
<button onclick="closeShipmentModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none ml-2">&times;</button>
</div>
<div class="p-6 space-y-5">
<div class="flex items-center gap-3 text-sm"><span id="mOrigin" class="font-bold text-slate-800">—</span>
<span class="flex-1 h-px bg-slate-200 relative"><i class="fa-solid fa-truck-fast absolute -top-2 left-1/2 -translate-x-1/2 text-brand-blue bg-white px-1 text-xs"></i></span>
<span id="mDestination" class="font-bold text-slate-800">—</span></div>
<div class="grid grid-cols-2 gap-3 text-xs">
<div class="bg-slate-50 rounded-xl p-3 border border-slate-100"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Carrier</p><p id="mCarrier" class="font-bold text-slate-800">—</p></div>
<div class="bg-slate-50 rounded-xl p-3 border border-slate-100"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">ETA</p><p id="mEta" class="font-bold font-mono text-slate-800">—</p></div>
<div class="bg-slate-50 rounded-xl p-3 border border-slate-100"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Cargo</p><p id="mType" class="font-bold text-slate-800">—</p></div>
<div class="bg-slate-50 rounded-xl p-3 border border-slate-100"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Service</p><p id="mService" class="font-bold text-slate-800">—</p></div>
</div>
<div><div class="flex items-center justify-between text-xs mb-1.5"><span class="font-bold text-slate-700">Journey progress</span><span id="mProgressLabel" class="font-mono font-bold text-navy-700">0%</span></div>
<div class="h-2 rounded-full bg-slate-100 overflow-hidden"><div id="mProgress" class="h-full rounded-full bg-navy-700" style="width:0%"></div></div></div>
<ol id="mTimeline" class="relative pl-5 space-y-4 before:absolute before:inset-y-1 before:left-[5px] before:w-px before:bg-slate-200 text-xs"></ol>
<div class="flex items-center gap-2 pt-1">
<a href="customer.php" class="flex-1 text-center px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50"><i class="fa-solid fa-user mr-1.5"></i>Contact Customer</a>
<a href="book_shipment.php" class="flex-1 text-center px-4 py-2.5 rounded-xl bg-navy-700 hover:bg-navy-800 text-white text-xs font-bold shadow-md"><i class="fa-solid fa-rotate-right mr-1.5"></i>Book Again</a>
</div></div></div></div>
<script>
(function () {
var c = document.getElementById('shipmentStatusChart');
if (!c || typeof Chart === 'undefined') return;
var P = (window.crmPalette || function () { return window.CRM_COLORS || {}; })();
new Chart(c, { type: 'doughnut',
data: { labels: ['In Transit','Customs','Delayed','Delivered'],
datasets: [{ data: [<?= $counts['in-transit'] ?>, <?= $counts['customs'] ?>, <?= $counts['delayed'] ?>, <?= $counts['delivered'] ?>], backgroundColor: [(P.sky||'#4e83c5'),'#f59e0b','#f43f5e','#10b981'], borderColor: '#fff', borderWidth: 3, hoverOffset: 6 }] },
options: { cutout: '68%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, pointStyle: 'circle', font: { size: 11, family: 'Inter' }, color: '#64748b' } } }, responsive: true, maintainAspectRatio: false } });
})();
function applyManifestFilter() {
var a = document.querySelector('.filter-tab.is-active');
var st = a ? a.getAttribute('data-status-key') : 'all';
var q = (document.getElementById('shipmentSearchInput').value || '').toLowerCase().trim();
var vis = 0;
document.querySelectorAll('.shipment-row').forEach(function (r) {
var ok = (st === 'all' || r.getAttribute('data-status') === st) && (!q || r.innerText.toLowerCase().indexOf(q) !== -1);
r.classList.toggle('hidden', !ok); if (ok) vis++;
});
document.getElementById('shipmentEmptyState').classList.toggle('hidden', vis !== 0);
}
function filterShipments(s, b) { document.querySelectorAll('.filter-tab').forEach(function (t) { t.classList.remove('is-active'); }); if (b) b.classList.add('is-active'); applyManifestFilter(); }
function searchShipmentsTable() { applyManifestFilter(); }
function exportShipmentsCSV() {
var rows = Array.prototype.filter.call(document.querySelectorAll('.shipment-row'), function (r) { return !r.classList.contains('hidden'); });
var csv = ['Waybill,Customer,Type,Origin,Destination,Carrier,Service,Status,ETA'];
var Q = String.fromCharCode(34);
rows.forEach(function (row) {
var td = row.querySelectorAll('td');
var rt = td[2].querySelector('span').innerText.trim().split('→');
var line = [td[0].querySelector('strong').innerText.trim(), td[1].innerText.trim(), td[0].querySelector('span').innerText.trim(), (rt[0]||'').trim(), (rt[1]||'').trim(), td[3].innerText.trim(), td[2].querySelectorAll('span')[1].innerText.trim(), td[4].innerText.trim(), td[5].innerText.trim()];
csv.push(line.map(function (x) { return Q + String(x).replace(new RegExp(Q,'g'), Q+Q) + Q; }).join(','));
});
var blob = new Blob([csv.join(String.fromCharCode(10))], { type: 'text/csv;charset=utf-8;' });
var u = URL.createObjectURL(blob); var a = document.createElement('a');
a.href = u; a.download = 'agent_shipments.csv'; document.body.appendChild(a); a.click();
document.body.removeChild(a); URL.revokeObjectURL(u);
}
var SHIPMENT_INDEX = <?= json_encode(array_column($shipments, null, 'waybill'), JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
function quickTrack(e) {
e.preventDefault();
var v = (document.getElementById('quickTrackInput').value || '').trim().toUpperCase();
if (!v) return;
var hit = null;
Object.keys(SHIPMENT_INDEX).forEach(function (k) { if (k.toUpperCase() === v) hit = SHIPMENT_INDEX[k]; });
if (!hit) { alert('Waybill ' + v + ' was not found in your manifest.'); return; }
filterShipments('all', document.querySelector('.filter-tab[data-status-key="all"]'));
document.getElementById('shipmentSearchInput').value = ''; applyManifestFilter();
openShipmentModal(hit);
var row = document.getElementById('row-' + hit.waybill);
if (row) { row.scrollIntoView({ behavior: 'smooth', block: 'center' }); row.classList.add('bg-blue-50'); setTimeout(function(){ row.classList.remove('bg-blue-50'); }, 2200); }
}
var STATUS_META = { 'in-transit': { label: 'In Transit', cls: 'bg-blue-50 text-blue-700 border-blue-200' }, 'customs': { label: 'Customs', cls: 'bg-amber-50 text-amber-700 border-amber-200' }, 'delayed': { label: 'Delayed', cls: 'bg-rose-50 text-rose-700 border-rose-200' }, 'delivered': { label: 'Delivered', cls: 'bg-emerald-50 text-emerald-700 border-emerald-200' } };
function timelineFor(status) {
var steps = ['Booked','Picked up','In Transit','Out for Delivery','Delivered'];
var done = { 'customs': 2, 'in-transit': 3, 'delayed': 2, 'delivered': 5 }[status] || 1;
if (status === 'customs') steps[2] = 'Customs Clearance';
if (status === 'delayed') steps[2] = 'Delayed - exception raised';
return steps.map(function (lb, i) {
var d = i < done; var dot = d ? 'bg-emerald-500' : 'bg-slate-300';
if (status === 'delayed' && i === 2) dot = 'bg-rose-500';
return '<li class="relative"><span class="absolute -left-5 top-0.5 w-2.5 h-2.5 rounded-full ' + dot + ' ring-4 ring-white"></span><p class="font-bold ' + (d ? 'text-slate-800' : 'text-slate-400') + '">' + lb + '</p></li>';
}).join('');
}
function openShipmentModal(s) {
var m = STATUS_META[s.status] || { label: s.status, cls: 'bg-slate-50 text-slate-700 border-slate-200' };
document.getElementById('mWaybill').textContent = s.waybill || '-';
document.getElementById('mCustomer').textContent = (s.customer || '-') + ' / ' + (s.type || '');
var pill = document.getElementById('mStatus');
pill.className = 'inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold border whitespace-nowrap ' + m.cls;
pill.textContent = m.label;
document.getElementById('mOrigin').textContent = s.origin || '-';
document.getElementById('mDestination').textContent = s.destination || '-';
document.getElementById('mCarrier').textContent = s.carrier || '-';
document.getElementById('mEta').textContent = s.eta || '-';
document.getElementById('mType').textContent = s.type || '-';
document.getElementById('mService').textContent = s.service || '-';
document.getElementById('mProgress').style.width = (s.progress || 0) + '%';
document.getElementById('mProgressLabel').textContent = (s.progress || 0) + '%';
document.getElementById('mTimeline').innerHTML = timelineFor(s.status);
document.getElementById('shipmentModal').classList.remove('hidden');
document.body.classList.add('overflow-hidden');
}
function closeShipmentModal() { document.getElementById('shipmentModal').classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeShipmentModal(); });
</script>
<?php include_once '../../includes/footer.php'; ?>