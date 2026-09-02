<?php
$pageTitle = "Shipment Bookings";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Parameters mula sa URL Filter at Pagination
$current_status = strtolower($_GET['status'] ?? 'all');
$search_query   = $_GET['search'] ?? '';
$current_page   = max(1, (int)($_GET['page'] ?? 1));
$limit          = 5; // Fixed 5 limit

// 2. Fetch Live Stats mula sa FastAPI
$stats_res  = make_api_request('/api/v1/shipment-bookings/stats', 'GET');
$stats_data = $stats_res['data']['data'] ?? $stats_res['data'] ?? [];

$count_all       = (int)($stats_data['all'] ?? 0);
$count_booking   = (int)($stats_data['booking'] ?? 0);
$count_quoted    = (int)($stats_data['quoted'] ?? 0);
$count_confirmed = (int)($stats_data['confirmed'] ?? 0);
$count_cancelled = (int)($stats_data['cancelled'] ?? 0);

// 3. Build API Endpoint Query String para sa Table
$api_url = "/api/v1/shipment-bookings?status=" . urlencode($current_status) . "&page=" . $current_page . "&limit=" . $limit;

if (!empty($search_query)) {
    $api_url .= "&search=" . urlencode($search_query);
}

// Fetch Bookings List
$bookings_res  = make_api_request($api_url, 'GET');
$raw_response  = $bookings_res['data'] ?? [];

$bookings_list = $raw_response['data'] ?? [];
$meta          = $raw_response['meta'] ?? [];

$total_pages   = max(1, (int)($meta['total_pages'] ?? 1));
$total_records = (int)($meta['total'] ?? count($bookings_list));

// Helper function para sa Status Badges
function getBookingStatusBadge($status) {
    switch (strtolower(trim($status ?? 'booking'))) {
        case 'confirmed':
            return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        case 'quoted':
            return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'cancelled':
            return 'bg-rose-100 text-rose-700 border-rose-200';
        case 'booking':
        case 'pending':
        default:
            return 'bg-amber-100 text-amber-800 border-amber-200';
    }
}

// Helper para sa Pagination URLs
function buildUrl($page, $status, $search) {
    $url = "?status=" . urlencode($status) . "&page=" . $page;
    if (!empty($search)) {
        $url .= "&search=" . urlencode($search);
    }
    return $url;
}
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php include_once '../../components/top_header.php'; ?>

  <!-- PAGE TITLE & HEADER -->
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-xl font-bold text-slate-800">Shipment Bookings</h1>
      <p class="text-xs text-slate-400">Manage incoming chatbot inquiries and customer shipment bookings</p>
    </div>
  </div>

  <!-- STATUS FILTER TABS -->
  <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
    <a href="<?= buildUrl(1, 'all', $search_query) ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'all' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      All (<?= $count_all ?>)
    </a>
    <a href="<?= buildUrl(1, 'booking', $search_query) ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'booking' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      New Inquiries (<?= $count_booking ?>)
    </a>
    <a href="<?= buildUrl(1, 'quoted', $search_query) ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'quoted' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Quoted (<?= $count_quoted ?>)
    </a>
    <a href="<?= buildUrl(1, 'confirmed', $search_query) ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'confirmed' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Confirmed (<?= $count_confirmed ?>)
    </a>
    <a href="<?= buildUrl(1, 'cancelled', $search_query) ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'cancelled' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Cancelled (<?= $count_cancelled ?>)
    </a>
  </div>

  <!-- MAIN SHIPMENT TABLE CARD -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
    
    <div>
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-800">Booking Requests</h2>
        <span class="text-xs text-slate-400">Total Found: <b><?= $total_records ?></b></span>
      </div>

      <!-- TABLE -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
              <th class="py-3 px-4">Booking Code & Customer</th>
              <th class="py-3 px-4">Service Type</th>
              <th class="py-3 px-4">Route (Origin ➔ Dest)</th>
              <th class="py-3 px-4">Cargo Details</th>
              <th class="py-3 px-4">Agreed Amount</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm">
            <?php if (empty($bookings_list)): ?>
              <tr>
                <td colspan="7" class="py-8 text-center text-slate-400">
                  No shipment bookings found.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($bookings_list as $item): ?>
                <?php
                  $cust          = $item['customers'] ?? [];
                  $booking_code  = $item['booking_code'] ?? 'N/A';
                  $company       = $cust['company_name'] ?? 'N/A';
                  $contact       = $cust['contact_person'] ?? 'Unknown Contact';
                  $service       = $item['service_type'] ?? 'Standard';
                  $origin        = $item['origin'] ?? 'N/A';
                  $destination   = $item['destination'] ?? 'N/A';
                  $cargo         = $item['cargo_details'] ?? 'No specs provided';
                  $amount        = (float)($item['agreed_amount'] ?? 0);
                  $status        = $item['booking_status'] ?? 'booking';
                  $booking_id    = $item['id'] ?? '';
                ?>
                <tr class="hover:bg-slate-50/80 transition">
                  
                  <!-- CODE & CUSTOMER -->
                  <td class="py-4 px-4">
                    <div class="font-bold text-purple-600 text-xs">
                      <?= htmlspecialchars($booking_code) ?>
                    </div>
                    <div class="font-semibold text-slate-800">
                      <?= htmlspecialchars($company) ?>
                    </div>
                    <div class="text-xs text-slate-400">
                      <?= htmlspecialchars($contact) ?>
                    </div>
                  </td>

                  <!-- SERVICE TYPE -->
                  <td class="py-4 px-4 font-medium text-slate-700">
                    <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-md text-xs">
                      <?= htmlspecialchars($service) ?>
                    </span>
                  </td>

                  <!-- ROUTE -->
                  <td class="py-4 px-4 text-xs text-slate-600">
                    <div class="font-medium text-slate-800"><?= htmlspecialchars($origin) ?></div>
                    <div class="text-slate-400">➔ <?= htmlspecialchars($destination) ?></div>
                  </td>

                  <!-- CARGO DETAILS -->
                  <td class="py-4 px-4 text-xs text-slate-500 max-w-[200px] truncate" title="<?= htmlspecialchars($cargo) ?>">
                    <?= htmlspecialchars($cargo) ?>
                  </td>

                  <!-- AGREED AMOUNT -->
                  <td class="py-4 px-4 font-bold text-slate-800">
                    <?= $amount > 0 ? '₱' . number_format($amount, 2) : '<span class="text-xs font-normal text-amber-600 italic">Pending Quote</span>' ?>
                  </td>

                  <!-- STATUS BADGE -->
                  <td class="py-4 px-4 align-middle">
                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold border tracking-wide whitespace-nowrap leading-none <?= getBookingStatusBadge($status) ?>">
                      <?= htmlspecialchars(strtoupper($status)) ?>
                    </span>
                  </td>

                  <!-- ACTIONS -->
                  <td class="py-4 px-4 align-middle text-right">
                    <a href="process_booking.php?id=<?= urlencode($booking_id) ?>" 
                       class="px-3 py-1.5 bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white font-semibold text-xs rounded-lg transition inline-flex items-center gap-1">
                      Manage ➔
                    </a>
                  </td>

                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 5 ITEMS LIMIT PAGINATION FOOTER -->
    <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500">
      <span>
        Page <?= $current_page ?> of <?= $total_pages ?>
      </span>
      <div class="flex items-center gap-1.5">
        <!-- PREVIOUS BUTTON -->
        <?php if ($current_page > 1): ?>
          <a href="<?= buildUrl($current_page - 1, $current_status, $search_query) ?>" 
             class="px-3 py-1 text-xs font-semibold rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
            ‹ Prev
          </a>
        <?php else: ?>
          <span class="px-3 py-1 text-xs font-semibold rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">
            ‹ Prev
          </span>
        <?php endif; ?>

        <!-- NEXT BUTTON -->
        <?php if ($current_page < $total_pages): ?>
          <a href="<?= buildUrl($current_page + 1, $current_status, $search_query) ?>" 
             class="px-3 py-1 text-xs font-semibold rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
            Next ›
          </a>
        <?php else: ?>
          <span class="px-3 py-1 text-xs font-semibold rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">
            Next ›
          </span>
        <?php endif; ?>
      </div>
    </div>

  </div>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>