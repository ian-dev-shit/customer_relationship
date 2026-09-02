<?php
$pageTitle = "Customer Directory";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Parameters mula sa URL Filter at Pagination
$current_tier  = strtolower($_GET['tier'] ?? 'all');
$search_query  = $_GET['search'] ?? '';
$current_page  = max(1, (int)($_GET['page'] ?? 1));
$limit         = 5; // 5 Items per page limit

// 2. Fetch Live Stats 
$stats_res  = make_api_request('/api/v1/customers/stats', 'GET');
$stats_data = $stats_res['data']['data'] ?? $stats_res['data'] ?? [];

$count_all      = (int)($stats_data['all'] ?? $stats_data['total'] ?? 0);
$count_bronze   = (int)($stats_data['bronze'] ?? 0);
$count_silver   = (int)($stats_data['silver'] ?? 0);
$count_gold     = (int)($stats_data['gold'] ?? 0);
$count_platinum = (int)($stats_data['platinum'] ?? 0);

// 3. Build API Endpoint Query String
$api_url = "/api/v1/customers?tier=" . urlencode($current_tier) . "&page=" . $current_page . "&limit=" . $limit;

if (!empty($search_query)) {
    $api_url .= "&search=" . urlencode($search_query);
}

// Fetch Customers List mula sa API
$customers_res  = make_api_request($api_url, 'GET');
$raw_data       = $customers_res['data'] ?? [];

// Dynamic array response extraction
$customers_list = $raw_data['data'] ?? (is_array($raw_data) && !isset($raw_data['total']) ? $raw_data : []);

// Alamin ang total items 
switch ($current_tier) {
    case 'bronze':   $total_items = $count_bronze; break;
    case 'silver':   $total_items = $count_silver; break;
    case 'gold':     $total_items = $count_gold; break;
    case 'platinum': $total_items = $count_platinum; break;
    default:         $total_items = $count_all > 0 ? $count_all : count($customers_list); break;
}

// Client-side Slicing Fallback 
if (count($customers_list) > $limit) {
    $total_items    = count($customers_list);
    $offset         = ($current_page - 1) * $limit;
    $customers_list = array_slice($customers_list, $offset, $limit);
}

$total_pages = max(1, ceil($total_items / $limit));

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

// Helper para sa URL building sa Pagination buttons
function buildPageUrl($page, $tier, $search) {
    $url = "?tier=" . urlencode($tier) . "&page=" . $page;
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

  <!-- TIER FILTER TABS  -->
  <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
    <a href="?tier=all<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_tier === 'all' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      All (<?= $count_all ?>)
    </a>
    <a href="?tier=bronze<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_tier === 'bronze' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Bronze (<?= $count_bronze ?>)
    </a>
    <a href="?tier=silver<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_tier === 'silver' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Silver (<?= $count_silver ?>)
    </a>
    <a href="?tier=gold<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_tier === 'gold' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Gold (<?= $count_gold ?>)
    </a>
    <a href="?tier=platinum<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_tier === 'platinum' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Platinum (<?= $count_platinum ?>)
    </a>
  </div>

  <!-- CUSTOMERS TABLE CARD -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
    
    <div>
      <!-- CARD HEADER -->
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-800">Customer Directory</h2>
          <p class="text-xs text-slate-400">View and manage customer tiers and booking accounts</p>
        </div>
        <button class="text-sm font-semibold text-purple-600 hover:text-purple-700 flex items-center gap-1">
          Export CSV ➔
        </button>
      </div>

      <!-- MAIN TABLE -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
              <th class="py-3 px-4">Company & Contact</th>
              <th class="py-3 px-4">Email Address</th>
              <th class="py-3 px-4">Phone Number</th>
              <th class="py-3 px-4">Total Bookings</th>
              <th class="py-3 px-4">Tier Status</th>
              <th class="py-3 px-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm">
            <?php if (empty($customers_list)): ?>
              <tr>
                <td colspan="6" class="py-8 text-center text-slate-400">
                  No customers found.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($customers_list as $customer): ?>
                <?php
                  $company  = $customer['company_name'] ?? $customer['company'] ?? 'N/A';
                  $contact  = $customer['contact_person'] ?? $customer['name'] ?? 'No Contact Person';
                  $email    = $customer['email'] ?? 'N/A';
                  $phone    = $customer['phone_number'] ?? $customer['phone'] ?? 'N/A';
                  $bookings = $customer['total_bookings'] ?? $customer['bookings'] ?? 0;
                  $tier     = $customer['tier'] ?? 'BRONZE';
                  $cust_id  = $customer['id'] ?? $customer['_id'] ?? '';
                ?>
                <tr class="hover:bg-slate-50/80 transition">
                  
                  <td class="py-4 px-4">
                    <div class="font-bold text-slate-800">
                      <?= htmlspecialchars($company) ?>
                    </div>
                    <div class="text-xs text-slate-500">
                       <?= htmlspecialchars($contact) ?>
                    </div>
                  </td>

                  <td class="py-4 px-4 text-slate-600 font-medium">
                    <?= htmlspecialchars($email) ?>
                  </td>

                  <td class="py-4 px-4 text-slate-600">
                    <?= htmlspecialchars($phone) ?>
                  </td>

                  <td class="py-4 px-4 font-bold text-slate-800">
                    <?= (int)$bookings ?>
                  </td>

                  <td class="py-4 px-4 align-middle">
                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold border tracking-wide whitespace-nowrap leading-none <?= getCustomerTierBadge($tier) ?>">
                      <?= htmlspecialchars(strtoupper($tier)) ?>
                    </span>
                  </td>

                  <td class="py-4 px-4 align-middle text-right">
                    <a href="view_customer.php?id=<?= urlencode($cust_id) ?>" 
                       class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg inline-flex items-center justify-center transition"
                       title="View Details">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                      </svg>
                    </a>
                  </td>

                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PAGINATION  -->
    <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500">
      <span>
        Page <?= $current_page ?> of <?= $total_pages ?>
      </span>
      <div class="flex items-center gap-1.5">
        <!-- PREVIOUS BUTTON -->
        <?php if ($current_page > 1): ?>
          <a href="<?= buildPageUrl($current_page - 1, $current_tier, $search_query) ?>" 
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
          <a href="<?= buildPageUrl($current_page + 1, $current_tier, $search_query) ?>" 
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