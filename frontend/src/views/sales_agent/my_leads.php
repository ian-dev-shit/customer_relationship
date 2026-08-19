<?php
$page_title = "My Leads · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Parameters mula sa URL Filter & Pagination
$current_status = $_GET['status'] ?? 'all';
$search_query   = $_GET['search'] ?? '';
$page           = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit          = 10;

// 2. Fetch Live Stats mula sa FastAPI /api/v1/leads/stats
$stats_res  = make_api_request('/api/v1/leads/stats', 'GET');
$stats_data = $stats_res['data'] ?? [];

$count_all         = (int)($stats_data['all'] ?? 0);
$count_new         = (int)($stats_data['new_inquiry'] ?? 0);
$count_qualifying  = (int)($stats_data['qualifying'] ?? 0);
$count_quote_sent  = (int)($stats_data['quote_sent'] ?? 0);
$count_negotiation = (int)($stats_data['negotiation'] ?? 0);

// 3. Build API Endpoint Query String para sa Leads Table
$api_url = "/api/v1/leads/?page={$page}&limit={$limit}";

if (!empty($current_status) && $current_status !== 'all') {
    $api_url .= "&status=" . urlencode($current_status);
}

if (!empty($search_query)) {
    $api_url .= "&search=" . urlencode($search_query);
}

// Fetch Leads Data
$leads_res  = make_api_request($api_url, 'GET');
$leads_list = $leads_res['data']['data'] ?? [];
$total_rows = $leads_res['data']['total'] ?? 0;
$total_pages = ceil($total_rows / $limit);

// Helper function para sa Status Badge Styles
function getLeadStatusBadge($status) {
    switch (strtolower(trim($status))) {
        case 'new_inquiry':
            return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'qualifying':
            return 'bg-amber-100 text-amber-700 border-amber-200';
        case 'quote_sent':
            return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'negotiation':
            return 'bg-indigo-100 text-indigo-700 border-indigo-200';
        case 'closed_won':
            return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        case 'closed_lost':
            return 'bg-rose-100 text-rose-700 border-rose-200';
        default:
            return 'bg-slate-100 text-slate-700 border-slate-200';
    }
}
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">My Leads</h1>
      <p class="text-sm text-slate-500">Manage and track customer inquiries assigned to you</p>
    </div>

    <!-- SEARCH & ACTION BUTTONS -->
    <div class="flex items-center gap-3">
      <?php include 'components/search.php'; ?>

      <button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-medium transition shadow-sm">
        + New Quote
      </button>
    </div>
  </div>

  <!-- STATUS FILTER TABS -->
  <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
    <a href="?status=all<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'all' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      All (<?= $count_all ?>)
    </a>
    <a href="?status=new_inquiry<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'new_inquiry' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      New (<?= $count_new ?>)
    </a>
    <a href="?status=qualifying<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'qualifying' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Qualifying (<?= $count_qualifying ?>)
    </a>
    <a href="?status=quote_sent<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'quote_sent' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Quote Sent (<?= $count_quote_sent ?>)
    </a>
    <a href="?status=negotiation<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'negotiation' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Negotiation (<?= $count_negotiation ?>)
    </a>
  </div>

  <!-- LEADS TABLE CARD -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    
    <!-- CARD HEADER -->
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h2 class="text-base font-bold text-slate-800">Inquiry Leads Directory</h2>
        <p class="text-xs text-slate-400">Manage status updates and customer inquiries</p>
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
            <th class="py-3 px-4">Inquiry Code</th>
            <th class="py-3 px-4">Company & Contact</th>
            <th class="py-3 px-4">Service Type</th>
            <th class="py-3 px-4">Estimated Price</th>
            <th class="py-3 px-4">Status</th>
            <th class="py-3 px-4">Date Submitted</th>
            <th class="py-3 px-4 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
          <?php if (empty($leads_list)): ?>
            <tr>
              <td colspan="6" class="py-8 text-center text-slate-400">
                No inquiries found.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($leads_list as $lead): ?>
              <tr class="hover:bg-slate-50/80 transition">
                
                <!-- 1. INQUIRY CODE / ID -->
                <td class="py-4 px-4 font-bold text-purple-600">
                  <?= htmlspecialchars($lead['inquiry_code'] ?? 'INQ-'.substr($lead['id'], 0, 8)) ?>
                </td>

                <!-- 2. COMPANY NAME & CONTACT PERSON -->
                <td class="py-4 px-4">
                  <div class="font-bold text-slate-800">
                    <?= htmlspecialchars($lead['company_name'] ?? 'N/A') ?>
                  </div>
                  <div class="text-xs text-slate-500">
                     <?= htmlspecialchars($lead['contact_person'] ?? 'No Contact Person') ?>
                  </div>
                </td>

                <!-- 3. SERVICE TYPE -->
                <td class="py-4 px-4 text-slate-600 font-medium">
                  <?= htmlspecialchars($lead['service_type'] ?? 'General Freight') ?>
                  <?php if (!empty($lead['origin']) && !empty($lead['destination'])): ?>
                    <div class="text-xs text-slate-400">
                      <?= htmlspecialchars($lead['origin']) ?> ➔ <?= htmlspecialchars($lead['destination']) ?>
                    </div>
                  <?php endif; ?>
                </td>

                <!--  ESTIMATED PRICE DISPLAY -->
                <td class="py-4 px-4 font-bold text-slate-800">
                  ₱<?= number_format((float)($lead['estimated_amount'] ?? 0), 2) ?>
                </td>

                <!-- 4. STATUS -->
                <td class="py-4 px-4 align-middle">
                  <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold border tracking-wide whitespace-nowrap leading-none <?= getLeadStatusBadge($lead['status'] ?? 'new_inquiry') ?>">
                    <?= htmlspecialchars(str_replace('_', ' ', strtoupper($lead['status'] ?? 'NEW INQUIRY'))) ?>
                  </span>
                </td>

                <!-- 5. CREATED AT -->
                <td class="py-4 px-4 text-xs text-slate-500">
                  <?= date('M d, Y • h:i A', strtotime($lead['created_at'])) ?>
                </td>

                <!-- 6. ACTIONS (VIEW & CONTACT) -->
                <td class="py-4 px-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    
                    <!-- VIEW DETAILS MODAL TRIGGER -->
                    <button 
                      onclick="openViewModal(<?= htmlspecialchars(json_encode($lead)) ?>)" 
                      class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-600 font-semibold rounded-lg text-xs transition border border-purple-200">
                      View
                    </button>

                    <!-- DIRECT CONTACT ACTION -->
                    <button 
                      onclick="openContactModal('<?= htmlspecialchars(addslashes($lead['company_name'] ?? 'N/A')) ?>', '<?= htmlspecialchars(addslashes($lead['contact_person'] ?? '')) ?>', '<?= htmlspecialchars($lead['email'] ?? '') ?>', '<?= htmlspecialchars($lead['phone_number'] ?? '') ?>')" 
                      class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs transition border border-slate-200 flex items-center gap-1">
                      ✉️ Contact
                    </button>

                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION FOOTER -->
    <?php if ($total_pages > 1): ?>
      <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <div>Showing page <strong><?= $page ?></strong> of <strong><?= $total_pages ?></strong></div>
        <div class="flex items-center gap-1">
          <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&status=<?= $current_status ?>" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg font-medium hover:bg-slate-50">Previous</a>
          <?php endif; ?>
          <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&status=<?= $current_status ?>" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg font-medium hover:bg-slate-50">Next</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>

</main>

<!-- VIEW INQUIRY DETAILS MODAL -->
<div id="viewModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative max-h-[90vh] overflow-y-auto">
    
    <button onclick="closeViewModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
      ✕
    </button>

    <h3 class="text-lg font-bold text-slate-800 mb-1" id="modalCompany">Company Name</h3>
    <p class="text-xs text-purple-600 font-semibold mb-4" id="modalCode">INQ-CODE</p>

    <div class="space-y-3 text-xs text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100 mb-5">
      <div><strong>Contact Person:</strong> <span id="modalContact"></span></div>
      <div><strong>Email Address:</strong> <span id="modalEmail"></span></div>
      <div><strong>Phone Number:</strong> <span id="modalPhone"></span></div>
      <div><strong>Platform:</strong> <span id="modalPlatform"></span></div>
      <div><strong>Service Requested:</strong> <span id="modalService"></span></div>
      <div><strong>Route:</strong> <span id="modalRoute"></span></div>
      <div><strong>Cargo Details:</strong> <p id="modalCargo" class="mt-1 text-slate-700 italic bg-white p-2 rounded border border-slate-200"></p></div>
    </div>

    <!-- UPDATE STATUS FORM -->
    <form id="statusUpdateForm" onsubmit="handleStatusUpdate(event)" class="space-y-4">
      <input type="hidden" id="modalLeadId" value="">
      
      <!-- TOP ROW: Status and Price -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Update Status:</label>
          <select id="modalStatusSelect" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-purple-500">
            <option value="new_inquiry">NEW INQUIRY</option>
            <option value="qualifying">QUALIFYING</option>
            <option value="quote_sent">QUOTE SENT</option>
            <option value="negotiation">NEGOTIATION</option>
            <option value="closed_won">CLOSED WON</option>
            <option value="closed_lost">CLOSED LOST</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Agreed Price / Quote (₱):</label>
          <input 
            type="number" 
            step="0.01" 
            id="modalPriceInput" 
            placeholder="0.00" 
            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-purple-500 font-bold text-slate-800"
          >
        </div>
      </div>

      <!-- DYNAMIC PICKUP FIELDS SECTION  -->
      <div id="pickupFieldsSection" class="hidden space-y-3 pt-3 border-t border-slate-200">
        <div class="text-xs font-bold text-slate-700 flex items-center gap-1">
           Pickup Details <span class="text-rose-500">*</span>
        </div>
        
        <div>
          <label class="block text-xs text-slate-500 mb-1">Full Pickup Address</label>
          <textarea 
            id="modalPickupAddress"
            name="pickup_address" 
            rows="2" 
            placeholder="Enter complete street address, landmark, floor/unit..."
            class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          ></textarea>
        </div>

        <div>
          <label class="block text-xs text-slate-500 mb-1">Pickup Date & Time</label>
          <input 
            type="datetime-local" 
            id="modalPickupDateTime" 
            name="pickup_datetime"
            class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          />
        </div>
      </div>

      <!-- SUBMIT BUTTON -->
      <button type="submit" class="w-full py-2.5 bg-purple-600 text-white font-semibold rounded-xl text-xs hover:bg-purple-700 transition shadow-md shadow-purple-200">
        Save Status
      </button>
    </form>

  </div>
</div>

<?php include_once 'components/contact_modal.php'; ?>

<!-- JAVASCRIPT FOR MODAL -->
<script src="../../../assets/js/myleads.js"></script>

<?php include_once 'components/alert.php'; ?>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>