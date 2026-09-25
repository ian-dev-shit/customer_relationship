<?php
$pageTitle = "Customer Details";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Kuhain ang Customer ID mula sa URL parameter
$customer_id = $_GET['id'] ?? null;

// 2. Fetch Single Customer Details mula sa API
$customer_data = [];
if ($customer_id) {
    $res = make_api_request("/api/v1/customers/" . urlencode($customer_id), 'GET');
    $customer_data = $res['data']['data'] ?? $res['data'] ?? [];
}

// Data Mapping (live fields mula sa API)
$contact_person = $customer_data['contact_person'] ?? $customer_data['name'] ?? 'N/A';
$company_name   = $customer_data['company_name'] ?? $customer_data['company'] ?? 'N/A';
$email          = $customer_data['email'] ?? 'N/A';
$phone_number   = $customer_data['phone_number'] ?? $customer_data['phone'] ?? 'N/A';
// Address fields (used to pre-fill the booking shipment modal consignor block)
$customer_address = $customer_data['address'] ?? $customer_data['billing_address'] ?? $customer_data['street_address'] ?? '';
$customer_city    = $customer_data['city'] ?? $customer_data['town'] ?? '';
$customer_country = $customer_data['country'] ?? 'Philippines';
$customer_zip     = $customer_data['zip_code'] ?? $customer_data['zipcode'] ?? $customer_data['zip'] ?? $customer_data['postal_code'] ?? '';
$total_bookings = (int)($customer_data['total_bookings'] ?? $customer_data['bookings'] ?? 0);
$tier           = strtoupper($customer_data['tier'] ?? 'BRONZE');
$avatar_url     = $customer_data['avatar_url'] ?? $customer_data['profile_picture'] ?? null;
$created_at     = $customer_data['created_at'] ?? null;

// Helper para sa Initials kapag walang Profile Picture
function getInitials($name) {
    $words = explode(" ", trim($name));
    $initials = '';
    foreach ($words as $w) {
        if (!empty($w)) {
            $initials .= strtoupper($w[0]);
        }
    }
    return substr($initials, 0, 2) ?: 'CU';
}

// Helper function para sa Tier Status Badge
function getCustomerTierBadge($tier) {
    switch (strtoupper(trim($tier ?? 'BRONZE'))) {
        case 'PLATINUM':
            return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'GOLD':
            return 'bg-yellow-100 text-yellow-800 border-yellow-300';
        case 'SILVER':
            return 'bg-slate-100 text-slate-700 border-slate-200';
        case 'BRONZE':
        default:
            return 'bg-amber-100 text-amber-800 border-amber-200';
    }
}

// ----------------------------------------------------------------------
// BOOKING DATASET (JSON mock — swap for live fetch when endpoint exists)
// TODO: Palitan ito ng live fetch kapag may endpoint na:
//   $res = make_api_request("/api/v1/customers/".urlencode($customer_id)."/bookings", 'GET');
//   $bookings = $res['data']['data'] ?? $res['data'] ?? [];
// ----------------------------------------------------------------------
$bookings_fallback = [
    ['ref' => 'BK-94820', 'service' => 'Freight Transport', 'date' => 'Aug 20, 2026', 'amount' => 15400.00, 'status' => 'Completed', 'origin' => 'Manila', 'destination' => 'Cebu'],
    ['ref' => 'BK-93102', 'service' => 'Cargo Logistics',   'date' => 'Jul 14, 2026', 'amount' => 8200.00,  'status' => 'Completed', 'origin' => 'Pasig', 'destination' => 'Davao'],
    ['ref' => 'BK-92511', 'service' => 'Cold Chain',         'date' => 'Jun 30, 2026', 'amount' => 11250.00, 'status' => 'Completed', 'origin' => 'Quezon', 'destination' => 'Iloilo'],
    ['ref' => 'BK-91904', 'service' => 'Freight Transport', 'date' => 'Jun 02, 2026', 'amount' => 9800.00,  'status' => 'In Transit','origin' => 'Manila', 'destination' => 'Batangas'],
    ['ref' => 'BK-90788', 'service' => 'Warehousing',        'date' => 'May 18, 2026', 'amount' => 6400.00,  'status' => 'Completed', 'origin' => 'Makati', 'destination' => 'Laguna'],
    ['ref' => 'BK-89910', 'service' => 'Cargo Logistics',   'date' => 'Apr 27, 2026', 'amount' => 7300.00,  'status' => 'Cancelled', 'origin' => 'Cavite','destination' => 'Pampanga'],
    ['ref' => 'BK-88431', 'service' => 'Freight Transport', 'date' => 'Mar 15, 2026', 'amount' => 12500.00, 'status' => 'Completed', 'origin' => 'Manila', 'destination' => 'Cagayan'],
];
$bookings = $bookings_fallback;
$bookings_json_path = __DIR__ . '/../../data/sales_agent/view_customer_bookings.json';
if (is_readable($bookings_json_path)) {
    $decoded = json_decode(@file_get_contents($bookings_json_path), true);
    if (is_array($decoded) && !empty($decoded)) {
        $bookings = $decoded;
    }
}

// ---- Derive KPIs from the booking dataset (keeps numbers consistent) ----
$status_counts = ['Completed' => 0, 'In Transit' => 0, 'Pending' => 0, 'Cancelled' => 0];
$total_spent   = 0;
$last_booking  = null;
foreach ($bookings as $b) {
    $s = ucfirst(strtolower($b['status']));
    if (!isset($status_counts[$s])) $status_counts[$s] = 0;
    $status_counts[$s]++;
    if ($s === 'Completed' || $s === 'In Transit') {
        $total_spent += (float)$b['amount'];
    }
    if ($last_booking === null || strtotime($b['date']) > strtotime($last_booking['date'])) {
        $last_booking = $b;
    }
}
$completed   = $status_counts['Completed'];
$in_transit  = $status_counts['In Transit'];
$pending     = $status_counts['Pending'];
$cancelled   = $status_counts['Cancelled'];
$booking_count  = count($bookings);
$completed_rate = $booking_count > 0 ? round(($completed / $booking_count) * 100) : 0;
$avg_value   = $booking_count > 0 ? $total_spent / $booking_count : 0;

// Trend: second half vs first half of active bookings (cancelled excluded)
$active = array_filter($bookings, function ($b) { $s = ucfirst(strtolower($b['status'])); return $s !== 'Cancelled'; });
$active_cnt = count($active);
$half = (int) ceil($active_cnt / 2);
$spend_first  = $active_cnt > 0 ? array_sum(array_column(array_slice($active, 0, $half), 'amount')) : 0;
$spend_second = $active_cnt > 0 ? array_sum(array_column(array_slice($active, $half), 'amount')) : 0;
$spend_trend  = ($spend_first > 0) ? round((($spend_second - $spend_first) / $spend_first) * 100) : 0;

// Status pill + member since helpers
function getStatusPill($status) {
    switch (ucfirst(strtolower($status))) {
        case 'Completed':  return 'bg-emerald-100 text-emerald-700';
        case 'In Transit': return 'bg-sky-100 text-sky-700';
        case 'Pending':    return 'bg-amber-100 text-amber-700';
        case 'Cancelled':  return 'bg-rose-100 text-rose-700';
        default:           return 'bg-slate-100 text-slate-600';
    }
}
$member_since = $created_at ? date('M Y', strtotime($created_at)) : '—';

// Donut series for the booking-status chart
$donut_series = [$completed, $in_transit, $pending, $cancelled];
$donut_colors = ['#10b981', '#0ea5e9', '#f59e0b', '#f43f5e'];
$donut_labels = ['Completed', 'In Transit', 'Pending', 'Cancelled'];
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main data-brand="priority" class="flex-1 overflow-y-auto bg-[#F8FAFC]">

  <?php 
  $header_title = "Customer Details";
  $header_subtitle = "Detailed view of customer activity and profile.";
  include_once '../../components/top_header.php'; 
  ?>

  <div class="p-6 lg:p-8">

  <!-- BACK BUTTON -->
  <div class="mb-6">
    <a href="customer.php" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-purple-600 transition">
      <i class="fa-solid fa-arrow-left mr-2"></i>Back to Customer Directory
    </a>
  </div>

  <!-- HERO PROFILE CARD -->
  <div class="relative overflow-hidden rounded-2xl border border-slate-200 shadow-sm mb-6">
    <!-- Gradient banner -->
    <div class="h-28 bg-gradient-to-r from-[#0f1b3d] via-[#1d2e6a] to-[#3b1d6e] relative">
      <div class="absolute top-4 right-4">
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold border uppercase tracking-wider bg-white/15 text-white border-white/25 backdrop-blur-sm">
          <i class="fa-solid fa-gem"></i><?= htmlspecialchars(strtoupper($tier)) ?>
        </span>
      </div>
    </div>

    <!-- Avatar + Details overlapping the banner -->
    <div class="px-6 lg:px-8 pt-5 pb-6">
      <div class="flex flex-col items-start gap-4 md:flex-row md:items-end md:gap-5">
        <!-- Avatar -->
        <div class="shrink-0 md:-mt-12">
          <?php if (!empty($avatar_url)): ?>
            <img src="<?= htmlspecialchars($avatar_url) ?>" alt="Profile" class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg bg-white">
          <?php else: ?>
            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white font-black text-3xl flex items-center justify-center border-4 border-white shadow-lg">
              <?= getInitials($contact_person) ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Company / contact info -->
        <div class="flex-1 min-w-0 pb-1">
          <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight truncate"><?= htmlspecialchars($company_name) ?></h1>
          <p class="mt-0.5 text-sm text-slate-500">
            <span class="font-semibold text-slate-700"><?= htmlspecialchars($contact_person) ?></span> · Contact Person
          </p>
          <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-medium text-slate-600 shadow-xs">
              <i class="fa-solid fa-envelope text-purple-600"></i><?= htmlspecialchars($email) ?>
            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-medium text-slate-600 shadow-xs">
              <i class="fa-solid fa-phone text-purple-600"></i><?= htmlspecialchars($phone_number) ?>
            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-medium text-slate-600 shadow-xs">
              <i class="fa-solid fa-calendar-check text-purple-600"></i>Member since <?= htmlspecialchars($member_since) ?>
            </span>
          </div>
        </div>

        <!-- Quick actions -->
        <div class="flex items-center gap-2 pb-1 shrink-0">
          <a href="mailto:<?= htmlspecialchars($email) ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-purple-600 hover:border-purple-200 shadow-sm flex items-center justify-center transition" title="Email customer">
            <i class="fa-solid fa-envelope"></i>
          </a>
          <a href="tel:<?= htmlspecialchars($phone_number) ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-emerald-600 hover:border-emerald-200 shadow-sm flex items-center justify-center transition" title="Call customer">
            <i class="fa-solid fa-phone"></i>
          </a>
          <button type="button" class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition shadow-sm shadow-purple-200 flex items-center gap-2 active:scale-95 whitespace-nowrap">
            <i class="fa-solid fa-plus text-[10px]"></i> New Booking
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ROW 1: KPI STAT CARDS -->
  <div class="grid grid-cols-2 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <div class="flex items-start justify-between">
        <span class="text-xs font-bold text-slate-700">Total Bookings</span>
        <div class="p-1.5 rounded-lg bg-purple-100 text-purple-600"><i class="fa-solid fa-box text-xs"></i></div>
      </div>
      <p class="mt-3 text-3xl font-extrabold text-slate-900"><?= (int)$total_bookings ?: $booking_count ?></p>
      <p class="mt-2 text-[11px] font-semibold text-slate-400">Across all services</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <div class="flex items-start justify-between">
        <span class="text-xs font-bold text-slate-700">Total Spent</span>
        <div class="p-1.5 rounded-lg bg-emerald-100 text-emerald-600"><i class="fa-solid fa-peso-sign text-xs"></i></div>
      </div>
      <p class="mt-3 text-3xl font-extrabold text-slate-900">₱<?= number_format($total_spent, 0) ?></p>
      <p class="mt-2 text-[11px] font-semibold <?= $spend_trend >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
        <?= $spend_trend >= 0 ? '▲' : '▼' ?> <?= abs($spend_trend) ?>% vs prev. period
      </p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <div class="flex items-start justify-between">
        <span class="text-xs font-bold text-slate-700">Completed Rate</span>
        <div class="p-1.5 rounded-lg bg-sky-100 text-sky-600"><i class="fa-solid fa-circle-check text-xs"></i></div>
      </div>
      <p class="mt-3 text-3xl font-extrabold text-slate-900"><?= $completed_rate ?>%</p>
      <p class="mt-2 text-[11px] font-semibold text-slate-400"><?= $completed ?> of <?= $booking_count ?> fulfilled</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <div class="flex items-start justify-between">
        <span class="text-xs font-bold text-slate-700">Avg. Booking Value</span>
        <div class="p-1.5 rounded-lg bg-amber-100 text-amber-600"><i class="fa-solid fa-chart-line text-xs"></i></div>
      </div>
      <p class="mt-3 text-3xl font-extrabold text-slate-900">₱<?= number_format($avg_value, 0) ?></p>
      <p class="mt-2 text-[11px] font-semibold text-slate-400">Per active booking</p>
    </div>
  </div>

  <!-- ROW 2: MAIN DASHBOARD GRID -->
  <div class="flex flex-wrap gap-6 items-start">

    <!-- LEFT COLUMN: BOOKING HISTORY -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex-[2_1_0px] min-w-[320px]">
      <div class="p-5 border-b border-slate-100 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h3 class="text-base font-bold text-slate-800">Booking History</h3>
          <p class="text-xs text-slate-400 mt-0.5">Service records and fulfillment status</p>
        </div>
        <div class="relative">
          <input type="text" id="bookingSearch" placeholder="Search bookings..." onkeyup="filterBookings()"
                 class="w-full sm:w-56 bg-slate-50 hover:bg-white focus:bg-white text-xs text-slate-800 placeholder-slate-400 pl-8 pr-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 shadow-xs transition">
          <i class="fa-solid fa-magnifying-glass text-xs text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="bookingTable">
            <thead>
            <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                <th class="py-3 px-4">Booking Reference</th>
                <th class="py-3 px-4">Service Type</th>
                <th class="py-3 px-4">Route</th>
                <th class="py-3 px-4">Date</th>
                <th class="py-3 px-4">Amount</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Action</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
            <?php if (!empty($bookings)): ?>
              <?php foreach ($bookings as $i => $bk): ?>
                <tr class="hover:bg-slate-50/80 transition">
                  <td class="py-4 px-4 font-bold text-purple-600"><?= htmlspecialchars($bk['ref']) ?></td>
                  <td class="py-4 px-4 text-slate-600"><?= htmlspecialchars($bk['service']) ?></td>
                  <td class="py-4 px-4 text-slate-500"><?= htmlspecialchars($bk['origin']) ?> <i class="fa-solid fa-arrow-right-long mx-1 text-slate-300"></i> <?= htmlspecialchars($bk['destination']) ?></td>
                  <td class="py-4 px-4 text-slate-500 whitespace-nowrap"><?= htmlspecialchars($bk['date']) ?></td>
                  <td class="py-4 px-4 font-semibold text-slate-800">₱<?= number_format((float)$bk['amount'], 2) ?></td>
                  <td class="py-4 px-4">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= getStatusPill($bk['status']) ?>"><?= htmlspecialchars($bk['status']) ?></span>
                  </td>
                  <td class="py-4 px-4 align-middle text-right relative">
                    <button type="button" onclick="toggleBookingRowMenu(event, 'bookingRowMenu<?= (int)$i ?>')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg inline-flex items-center justify-center transition" title="Actions">
                      <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <div id="bookingRowMenu<?= (int)$i ?>" class="hidden absolute right-4 top-12 z-30 w-56 rounded-xl border border-slate-200 bg-white shadow-xl overflow-hidden text-left">
                      <button type="button" onclick="openAgentBookingModal(event, <?= (int)$i ?>)" class="w-full flex items-center gap-2.5 px-4 py-3 text-xs font-semibold text-slate-700 hover:bg-purple-50 hover:text-purple-700 transition">
                        <i class="fa-solid fa-truck-fast text-purple-600"></i>Book Shipment
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="py-12 text-center">
                  <div class="flex flex-col items-center text-slate-400">
                    <i class="fa-solid fa-box-open text-3xl mb-3"></i>
                    <p class="text-sm font-medium">No bookings found for this customer yet.</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
            </tbody>
        </table>

      </div>

      <!-- PAGINATION -->
      <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-400">
        <span>Showing <?= count($bookings) ?> record<?= count($bookings) === 1 ? '' : 's' ?></span>
        <div class="flex items-center gap-1">
          <button class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50 transition">1</button>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN WIDGETS -->
    <div class="space-y-6 flex-[1_1_0px] min-w-[260px]">

      <!-- BOOKING STATUS DONUT -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-2">
          <h3 class="text-sm font-bold text-slate-800">Booking Status</h3>
          <span class="text-[11px] font-semibold text-slate-400"><?= $booking_count ?> total</span>
        </div>
        <div id="statusDonut" class="-mt-2"></div>
        <div class="mt-2 space-y-2">
          <?php foreach ($donut_labels as $i => $lbl): ?>
            <div class="flex items-center justify-between text-xs">
              <span class="flex items-center gap-2 text-slate-600 font-medium">
                <span class="w-2.5 h-2.5 rounded-full" style="background:<?= $donut_colors[$i] ?>;"></span><?= $lbl ?>
              </span>
              <span class="font-bold text-slate-800"><?= $donut_series[$i] ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- CONTACT & QUICK ACTIONS -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-4">Contact &amp; Quick Actions</h3>
        <div class="space-y-3">
          <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
            <div class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-envelope"></i></div>
            <div class="min-w-0 flex-1">
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Email</p>
              <p class="text-xs font-semibold text-slate-700 truncate" id="copyEmail"><?= htmlspecialchars($email) ?></p>
            </div>
            <button type="button" onclick="copyText('copyEmail','Email copied')" class="text-slate-400 hover:text-purple-600 transition" title="Copy email"><i class="fa-regular fa-copy"></i></button>
          </div>
          <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-phone"></i></div>
            <div class="min-w-0 flex-1">
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Phone</p>
              <p class="text-xs font-semibold text-slate-700 truncate" id="copyPhone"><?= htmlspecialchars($phone_number) ?></p>
            </div>
            <button type="button" onclick="copyText('copyPhone','Phone copied')" class="text-slate-400 hover:text-emerald-600 transition" title="Copy phone"><i class="fa-regular fa-copy"></i></button>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-4">
          <a href="mailto:<?= htmlspecialchars($email) ?>" class="py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold text-center transition shadow-sm shadow-purple-200 active:scale-95">Email</a>
          <a href="tel:<?= htmlspecialchars($phone_number) ?>" class="py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold text-center transition active:scale-95">Call</a>
        </div>
      </div>

    </div>

  </div>

</main>

<!-- BOOKING SHIPMENT MODAL (same modal as customer dashboard; consignor pre-filled below) -->
<?php
// booking_modal.php also uses $customer_id from the session — preserve the viewed
// customer id so the prefill data below stays correct.
$__view_customer_id = $customer_id;
$booking_customer_id_preset = $customer_id;
include_once '../../components/booking_modal.php';
$customer_id = $__view_customer_id;
?>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- BOOKING SHIPMENT MODAL JS (same behaviour as customer dashboard) -->
<script src="/assets/js/customer/customer_dashboard.js"></script>
<script>
// Viewed customer snapshot for consignor pre-fill (editable, except hidden id).
// Full JSON mock rows (same data as the Booking History table) so the clicked
// row can pre-fill the whole consignment note.
window.AGENT_BOOKING_ROWS = <?= json_encode(array_values($bookings), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
window.AGENT_BOOKING_CUSTOMER = <?= json_encode([
  'id'           => $customer_id,
  'company_name' => $company_name,
  'contact_person' => $contact_person,
  'phone'        => ($phone_number !== 'N/A' ? $phone_number : ''),
  'address'      => $customer_address,
  'city'         => $customer_city,
  'country'      => $customer_country,
  'zip'          => $customer_zip,
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

function toggleBookingRowMenu(event, menuId) {
  if (event) event.stopPropagation();
  var menu = document.getElementById(menuId);
  if (!menu) return;
  var isHidden = menu.classList.contains('hidden');
  document.querySelectorAll('[id^="bookingRowMenu"]').forEach(function (m) {
    if (m.id !== menuId) m.classList.add('hidden');
  });
  menu.classList.toggle('hidden', !isHidden);
}

document.addEventListener('click', function (e) {
  if (!e.target.closest('[id^="bookingRowMenu"]') && !e.target.closest('[onclick^="toggleBookingRowMenu"]')) {
    document.querySelectorAll('[id^="bookingRowMenu"]').forEach(function (m) { m.classList.add('hidden'); });
  }
});

function setBookingField(id, value) {
  var el = document.getElementById(id);
  if (!el || value === undefined || value === null || value === '') return;
  el.value = value;
}

function setSenderName(value) {
  var sender = document.getElementById('senderName');
  if (!sender || !value) return;
  var exists = Array.prototype.some.call(sender.options, function (o) { return o.value === value; });
  if (!exists) {
    var opt = document.createElement('option');
    opt.value = value;
    opt.textContent = value;
    sender.appendChild(opt);
  }
  sender.value = value;
}

function setCheckboxGroup(nameAttr, values) {
  if (!values) return;
  var list = Array.isArray(values) ? values : [values];
  document.querySelectorAll('input[name="' + nameAttr + '"]').forEach(function (el) {
    el.checked = list.indexOf(el.value) > -1;
  });
}

function openAgentBookingModal(event, rowIndex) {
  if (event) { event.preventDefault(); event.stopPropagation(); }
  document.querySelectorAll('[id^="bookingRowMenu"]').forEach(function (m) { m.classList.add('hidden'); });

  var c = window.AGENT_BOOKING_CUSTOMER || {};
  var rows = window.AGENT_BOOKING_ROWS || [];
  var row = (typeof rowIndex === 'number' && rows[rowIndex]) ? rows[rowIndex] : {};
  var consignor = row.consignor || {};
  var consignee = row.consignee || {};
  var cargo = row.cargo || {};

  // Hidden id always comes from the viewed customer, never from mock rows.
  setBookingField('bookingCustomerId', c.id || '');

  // Consignor: mock row first, viewed customer as fallback. Editable.
  setSenderName(consignor.name || c.company_name || '');
  setBookingField('senderTel', consignor.tel || c.phone || '');
  setBookingField('senderAddress', consignor.address || c.address || '');
  setBookingField('senderCity', consignor.city || c.city || '');
  setBookingField('senderCountry', consignor.country || c.country || 'Philippines');
  setBookingField('senderZip', consignor.zip || c.zip || '');

  // Consignee + cargo: full mock pre-fill from the clicked row's JSON.
  setBookingField('consigneeAttention', consignee.attention || '');
  setBookingField('consigneeCompany', consignee.company || '');
  setBookingField('consigneeAddress', consignee.address || '');
  setBookingField('consigneeCity', consignee.city || '');
  setBookingField('consigneeState', consignee.state || '');
  setBookingField('consigneeCountry', consignee.country || '');
  setBookingField('consigneeZip', consignee.zip || '');
  setBookingField('consigneeTel', consignee.tel || '');
  setBookingField('courier_date', cargo.courier_date || '');
  setBookingField('courier_time', cargo.courier_time || '');
  setBookingField('goodsDesc', cargo.goodsDesc || '');
  setBookingField('declared_amount', cargo.declared_amount || '');
  setBookingField('qty_pcs', cargo.qty_pcs || '');
  setBookingField('wt_kilos', cargo.wt_kilos || '');
  setBookingField('wt_grams', cargo.wt_grams || '');
  setBookingField('dim_length', cargo.dim_length || '');
  setBookingField('dim_width', cargo.dim_width || '');
  setBookingField('dim_height', cargo.dim_height || '');
  setCheckboxGroup('service', cargo.service);
  setCheckboxGroup('shipment_type', cargo.shipment_type);
  setCheckboxGroup('pkg_size', cargo.pkg_size);
  setCheckboxGroup('charge', cargo.charges);

  if (typeof openBookingModal === 'function') {
    openBookingModal();
  } else {
    var modal = document.getElementById('bookingModal');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
  }
}

// ==========================================
// CLOSE MODAL FUNCTIONS
// ==========================================

function closeAgentBookingModal() {
  // 1. Kung may existing na close function ka mula sa ibang file, tawagin ito
  if (typeof closeBookingModal === 'function') {
    closeBookingModal();
  } else {
    // 2. Fallback: I-hide ang modal gamit ang ID nito
    var modal = document.getElementById('bookingModal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  }

  // 3. (Optional) I-reset ang form kung may form tag sa loob ng modal
  var bookingForm = document.getElementById('bookingForm'); // palitan ayon sa tunay na ID ng form mo
  if (bookingForm) {
    bookingForm.reset();
  }
}

// OPTIONAL: Isara ang modal kapag pinindot ang 'ESC' key sa keyboard
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    closeAgentBookingModal();
  }
});

// OPTIONAL: Isara ang modal kapag kinlick sa labas (sa dark background overlay)
document.addEventListener('click', function (e) {
  var modal = document.getElementById('bookingModal');
  if (modal && e.target === modal) {
    closeAgentBookingModal();
  }
});
</script>

<!-- BOOKING STATUS DONUT + HELPERS -->
<script>
(function () {
  if (typeof ApexCharts === "undefined") return;
  var el = document.getElementById("statusDonut");
  if (!el) return;

  var options = {
    chart: { type: "donut", height: 230, fontFamily: "Inter, sans-serif", toolbar: { show: false } },
    series: <?= json_encode($donut_series) ?>,
    labels: <?= json_encode($donut_labels) ?>,
    colors: <?= json_encode($donut_colors) ?>,
    stroke: { width: 2, colors: ["#ffffff"] },
    plotOptions: {
      pie: {
        donut: {
          size: "70%",
          labels: {
            show: true,
            total: {
              show: true,
              label: "Total",
              fontSize: "13px",
              fontWeight: 700,
              color: "#94a3b8",
              formatter: function (w) {
                return w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0);
              }
            }
          }
        }
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function (val, opts) {
        return opts.w.globals.series[opts.seriesIndex];
      },
      style: { fontSize: "11px", fontWeight: 700, colors: ["#ffffff"] }
    },
    legend: { show: false },
    tooltip: { y: { formatter: function (val) { return val + " booking" + (val === 1 ? "" : "s"); } } },
    responsive: [{ breakpoint: 480, options: { chart: { height: 210 } } }]
  };

  new ApexCharts(el, options).render();
})();

// Copy email/phone to clipboard with a transient toast
function copyText(elementId, message) {
  var text = document.getElementById(elementId);
  if (!text) return;
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text.innerText.trim()).then(function () {
      showToast(message);
    });
  }
}

function showToast(msg) {
  var t = document.createElement("div");
  t.textContent = msg;
  t.className = "fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold shadow-lg";
  document.body.appendChild(t);
  setTimeout(function () { t.remove(); }, 1600);
}

// Simple client-side filter for the booking table
function filterBookings() {
  var input = document.getElementById("bookingSearch");
  var filter = input.value.toLowerCase();
  var table = document.getElementById("bookingTable");
  if (!table) return;
  var rows = table.getElementsByTagName("tr");
  for (var i = 1; i < rows.length; i++) {
    var cells = rows[i].getElementsByTagName("td");
    var match = false;
    for (var j = 0; j < cells.length; j++) {
      if (cells[j].innerText.toLowerCase().indexOf(filter) > -1) { match = true; break; }
    }
    rows[i].style.display = match ? "" : "none";
  }
}
</script>