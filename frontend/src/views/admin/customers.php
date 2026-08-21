<?php
$page_title = "Customer Accounts · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// Fetch Customer Accounts mula sa FastAPI endpoint
$customers_res  = make_api_request('/api/v1/admin/customer-accounts', 'GET');

// Handle single-wrapped or double-wrapped JSON response
$raw_list       = $customers_res['data'] ?? [];
$customers_list = isset($raw_list['data']) && is_array($raw_list['data']) ? $raw_list['data'] : $raw_list;

if (!is_array($customers_list)) {
    $customers_list = [];
}

$total_customers = count($customers_list);
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER -->
  <?php include_once 'components/top_header.php'; ?>

  <!-- FILTER PILLS -->
  <div class="flex items-center gap-2 mb-6">
    <button class="px-5 py-2 bg-indigo-600 text-white rounded-full text-xs font-bold shadow-sm shadow-indigo-200">
      All Accounts (<?= $total_customers ?>)
    </button>
  </div>

  <!-- TABLE CONTAINER CARD -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- CARD HEADER -->
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Customer Accounts</h2>
        <p class="text-xs text-slate-400 mt-0.5">List of created portal accounts for clients</p>
      </div>
      <button type="button" onclick="window.location.reload()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1.5 transition-colors">
        <i class="fa-solid fa-rotate-right"></i> Refresh List
      </button>
    </div>

    <!-- DATA TABLE -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <!-- TABLE HEADER -->
        <thead>
          <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
            <th class="py-4 px-6">Customer / Company</th>
            <th class="py-4 px-6">Email Address</th>
            <th class="py-4 px-6">Account Status</th>
            <th class="py-4 px-6 text-center">Action</th>
          </tr>
        </thead>

        <!-- TABLE BODY -->
        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
          <?php if (empty($customers_list)): ?>
            <tr>
              <td colspan="4" class="py-12 text-center text-slate-400">
                <i class="fa-solid fa-users-slash text-2xl mb-2 text-slate-300 block"></i>
                No customer accounts created yet.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($customers_list as $user): ?>
              <?php 
                $firstName = $user['first_name'] ?? '';
                $lastName  = $user['last_name'] ?? '';
                $fullName  = trim("$firstName $lastName");

                if (empty($fullName)) {
                    $fullName = $user['full_name'] ?? 'Customer';
                }

                $email     = $user['email'] ?? 'N/A';
                $company   = !empty($user['company_name']) ? $user['company_name'] : 'Individual Client';
                $phone     = $user['phone_number'] ?? 'N/A';
                $createdAt = !empty($user['created_at']) ? date('M d, Y h:i A', strtotime($user['created_at'])) : 'N/A';
              ?>
              <tr class="hover:bg-slate-50/80 transition-colors">
                
                <!-- CUSTOMER / COMPANY -->
                <td class="py-4 px-6">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 font-bold flex items-center justify-center text-xs shrink-0 uppercase">
                      <?= !empty($fullName) ? substr($fullName, 0, 1) : 'C' ?>
                    </div>
                    <div>
                      <div class="font-bold text-slate-900"><?= htmlspecialchars($fullName) ?></div>
                      <div class="text-[11px] text-slate-400"><?= htmlspecialchars($company) ?></div>
                    </div>
                  </div>
                </td>

                <!-- EMAIL -->
                <td class="py-4 px-6 font-medium text-slate-600">
                  <?= htmlspecialchars($email) ?>
                </td>

                <!-- STATUS -->
                <td class="py-4 px-6">
                  <span class="px-3 py-1 bg-emerald-100 text-emerald-700 font-semibold text-[10px] rounded-full inline-flex items-center gap-1.5 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active Portal
                  </span>
                </td>

                <!-- ACTION BUTTON -->
                <td class="py-4 px-6 text-center">
                  <button 
                    type="button" 
                    onclick='viewCustomerDetails(<?= json_encode([
                      "first_name" => $firstName,
                      "last_name" => $lastName,
                      "email" => $email,
                      "company_name" => $company,
                      "phone_number" => $phone,
                      "created_at" => $createdAt
                    ]) ?>)'
                    class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition-all active:scale-95 inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-eye text-[10px] text-slate-500"></i> View Details
                  </button>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>

</main>

<!-- VIEW DETAILS MODAL -->
<div id="viewDetailsModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
    
    <!-- MODAL HEADER -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-lg font-bold">
          <i class="fa-solid fa-address-card"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-900">Customer Details</h3>
          <p class="text-xs text-slate-400">Portal Account Information</p>
        </div>
      </div>
      <button type="button" onclick="closeDetailsModal()" class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- MODAL BODY -->
    <div class="py-5 space-y-3.5">
      
      <!-- FIRST NAME & LAST NAME -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">First Name</label>
          <div id="modal_first_name" class="text-xs font-bold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100">--</div>
        </div>
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Last Name</label>
          <div id="modal_last_name" class="text-xs font-bold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100">--</div>
        </div>
      </div>

      <!-- EMAIL -->
      <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Email Address</label>
        <div id="modal_email" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex items-center gap-2">
          <i class="fa-regular fa-envelope text-slate-400"></i>
          <span>--</span>
        </div>
      </div>

      <!-- COMPANY NAME -->
      <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Company Name</label>
        <div id="modal_company" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex items-center gap-2">
          <i class="fa-solid fa-building text-slate-400"></i>
          <span>--</span>
        </div>
      </div>

      <!-- PHONE NUMBER & CREATED AT -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Phone Number</label>
          <div id="modal_phone" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex items-center gap-1.5">
            <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
            <span>--</span>
          </div>
        </div>
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Date Created</label>
          <div id="modal_created_at" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex items-center gap-1.5">
            <i class="fa-regular fa-calendar-check text-slate-400 text-[10px]"></i>
            <span>--</span>
          </div>
        </div>
      </div>

    </div>

    <!-- MODAL FOOTER -->
    <div class="pt-4 border-t border-slate-100 flex justify-end">
      <button 
        type="button" 
        onclick="closeDetailsModal()" 
        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all active:scale-95">
        Close
      </button>
    </div>

  </div>
</div>

<!-- JAVASCRIPT -->
<script src="../../../assets/js/view.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>