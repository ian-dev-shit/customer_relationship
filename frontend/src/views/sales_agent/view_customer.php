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

// Data Mapping
$contact_person = $customer_data['contact_person'] ?? $customer_data['name'] ?? 'N/A';
$company_name   = $customer_data['company_name'] ?? $customer_data['company'] ?? 'N/A';
$email          = $customer_data['email'] ?? 'N/A';
$phone_number   = $customer_data['phone_number'] ?? $customer_data['phone'] ?? 'N/A';
$total_bookings = $customer_data['total_bookings'] ?? $customer_data['bookings'] ?? 0;
$tier           = $customer_data['tier'] ?? 'BRONZE';
$avatar_url     = $customer_data['avatar_url'] ?? $customer_data['profile_picture'] ?? null;

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
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php include_once '../../components/top_header.php'; ?>

  <!-- BACK BUTTON -->
  <div class="mb-6">
    <a href="customer.php" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-purple-600 transition">
      ← Back to Customer Directory
    </a>
  </div>

  <!-- CUSTOMER DETAILS CARD -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 lg:p-8 mb-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
      
      <!-- LEFT SIDE: AVATAR & BASIC DETAILS -->
      <div class="flex items-start sm:items-center gap-5">
        <!-- Profile Picture / Initials Avatar -->
        <div class="shrink-0">
          <?php if (!empty($avatar_url)): ?>
            <img src="<?= htmlspecialchars($avatar_url) ?>" alt="Profile" class="w-20 h-20 rounded-full object-cover border-2 border-slate-100 shadow-sm">
          <?php else: ?>
            <div class="w-20 h-20 rounded-full bg-purple-100 text-purple-700 font-bold text-2xl flex items-center justify-center border-2 border-purple-200 shadow-sm">
              <?= getInitials($contact_person) ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Contact, Company, & Email Info -->
        <div class="space-y-1.5">
          <div class="text-sm">
            <span class="text-slate-400 font-medium">Contact Person:</span>
            <span class="font-bold text-slate-800 ml-1"><?= htmlspecialchars($contact_person) ?></span>
          </div>
          <div class="text-sm">
            <span class="text-slate-400 font-medium">Company Name:</span>
            <span class="font-semibold text-slate-700 ml-1"><?= htmlspecialchars($company_name) ?></span>
          </div>
          <div class="text-sm">
            <span class="text-slate-400 font-medium">Email:</span>
            <span class="text-slate-600 ml-1"><?= htmlspecialchars($email) ?></span>
          </div>
        </div>
      </div>

      <!-- RIGHT SIDE: PHONE, TOTAL BOOKINGS, & TIER -->
      <div class="flex flex-col items-start md:items-end justify-between self-stretch space-y-3 border-t md:border-t-0 pt-4 md:pt-0 border-slate-100">
        <!-- Tier Badge -->
        <div>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border uppercase tracking-wider <?= getCustomerTierBadge($tier) ?>">
            <?= htmlspecialchars(strtoupper($tier)) ?>
          </span>
        </div>

        <!-- Phone & Total Bookings -->
        <div class="space-y-1.5 md:text-right">
          <div class="text-sm">
            <span class="text-slate-400 font-medium">Phone no:</span>
            <span class="text-slate-700 font-medium ml-1"><?= htmlspecialchars($phone_number) ?></span>
          </div>
          <div class="text-sm">
            <span class="text-slate-400 font-medium">Total Booking:</span>
            <span class="font-bold text-slate-800 ml-1"><?= (int)$total_bookings ?></span>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- BOOKING HISTORY CONTAINER -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100">
      <h3 class="text-base font-bold text-slate-800">Booking History</h3>
    </div>

    <!-- TABLE AREA -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
            <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                <th class="py-3 px-4">Booking Reference</th>
                <th class="py-3 px-4">Service Type</th>
                <th class="py-3 px-4">Date</th>
                <th class="py-3 px-4">Amount</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Action</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
            <!-- Row 1 -->
            <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-4 font-bold text-purple-600">#BK-94820</td>
                <td class="py-4 px-4 text-slate-600">Freight Transport</td>
                <td class="py-4 px-4 text-slate-500">Aug 20, 2026</td>
                <td class="py-4 px-4 font-semibold text-slate-800">₱15,400.00</td>
                <td class="py-4 px-4">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Completed</span>
                </td>
                <td class="py-4 px-4 align-middle text-right">
                <a href="#" 
                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg inline-flex items-center justify-center transition"
                    title="View Details">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 14a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                    </svg>
                </a>
                </td>
            </tr>

            <!-- Row 2 -->
            <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-4 font-bold text-purple-600">#BK-93102</td>
                <td class="py-4 px-4 text-slate-600">Cargo Logistics</td>
                <td class="py-4 px-4 text-slate-500">Jul 14, 2026</td>
                <td class="py-4 px-4 font-semibold text-slate-800">₱8,200.00</td>
                <td class="py-4 px-4">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Completed</span>
                </td>
                <td class="py-4 px-4 align-middle text-right">
                <a href="#" 
                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg inline-flex items-center justify-center transition"
                    title="View Details">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 14a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                    </svg>
                </a>
                </td>
            </tr>
            </tbody>
        </table>

    </div>

    <!-- PAGINATION PLACEHOLDER -->
    <div class="p-4 border-t border-slate-100 flex items-center justify-center text-xs font-semibold text-slate-400">
      .. 1 ..
    </div>
  </div>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>