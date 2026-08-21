<?php
$page_title = "Closed Won Tickets · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// Fetch Tickets mula sa FastAPI endpoint
$tickets_res  = make_api_request('/api/v1/admin/close-won-tickets', 'GET');
$tickets_list = $tickets_res['data'] ?? [];
$total_tickets = count($tickets_list);
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
      All (<?= $total_tickets ?>)
    </button>
    <button class="px-5 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-full text-xs font-semibold shadow-sm transition-all">
      Pending Account (<?= $total_tickets ?>)
    </button>
  </div>

  <!-- TABLE CONTAINER CARD -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- CARD HEADER -->
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Closed Won Tickets</h2>
        <p class="text-xs text-slate-400 mt-0.5">Tickets waiting for customer portal account creation</p>
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
                <th class="py-4 px-6">Company / Lead</th>
                <th class="py-4 px-6">Contact Email</th>
                <th class="py-4 px-6">Agreed Amount</th>
                <th class="py-4 px-6">Status</th>
                <th class="py-4 px-6 text-center">Action</th>
            </tr>
            </thead>

            <!-- TABLE BODY -->
            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            <?php if (empty($tickets_list)): ?>
                <tr>
                <td colspan="5" class="py-12 text-center text-slate-400">
                    <i class="fa-solid fa-circle-check text-2xl mb-2 text-emerald-500 block"></i>
                    No pending customer tickets found. All accounts created!
                </td>
                </tr>
            <?php else: ?>
                <?php foreach ($tickets_list as $ticket): ?>
                <?php 
                    $contactPerson = $ticket['contact_person'] ?? 'Client Contact';
                    $nameParts = explode(' ', trim($contactPerson));
                    $firstName = $nameParts[0] ?? '';
                    $lastName = count($nameParts) > 1 ? end($nameParts) : '';
                    
                    // Pickup ISO Timestamp
                    $pickupISO = $ticket['pickup_datetime'] ?? $ticket['pickup_date'] ?? '';
                ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    
                    <!-- COMPANY / LEAD -->
                    <td class="py-4 px-6">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <div>
                        <div class="font-bold text-slate-900"><?= htmlspecialchars($ticket['company_name'] ?? 'Individual Client') ?></div>
                        <div class="text-[11px] text-slate-400">Contact: <?= htmlspecialchars($contactPerson) ?></div>
                        </div>
                    </div>
                    </td>

                    <!-- EMAIL -->
                    <td class="py-4 px-6 font-medium text-slate-600">
                    <?= htmlspecialchars($ticket['email']) ?>
                    </td>

                    <!-- AGREED AMOUNT -->
                    <td class="py-4 px-6 font-bold text-slate-900">
                    ₱<?= number_format((float)($ticket['agreed_amount'] ?? 0), 2) ?>
                    </td>

                    <!-- STATUS WITH COUNTDOWN BADGE -->
                    <td class="py-4 px-6">
                    <div class="flex items-center gap-2">
                        <!-- Status Badge -->
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 font-semibold text-[10px] rounded-full inline-flex items-center gap-1 shrink-0">
                        <i class="fa-solid fa-clock text-[9px]"></i> Needs Account
                        </span>

                        <!-- Dynamic Countdown Badge -->
                        <?php if (!empty($pickupISO)): ?>
                        <span 
                            class="pickup-timer inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition-all shadow-sm"
                            data-pickup="<?= htmlspecialchars($pickupISO) ?>">
                            <i class="fa-solid fa-spinner fa-spin text-[9px]"></i> Loading...
                        </span>
                        <?php endif; ?>
                    </div>
                    </td>

                    <!-- ACTION BUTTON -->
                    <td class="py-4 px-6 text-center">
                    <button 
                        type="button"
                        onclick='openCreateModal(<?= json_encode([
                        "ticket_id" => $ticket["id"],
                        "email" => $ticket["email"],
                        "first_name" => $firstName,
                        "last_name" => $lastName,
                        "company_name" => $ticket["company_name"] ?? "",
                        "phone_number" => $ticket["phone_number"] ?? ""
                        ]) ?>)'
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-all shadow-sm active:scale-95 inline-flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-user-plus text-[11px]"></i> Create Account
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

<!-- CREATE CUSTOMER ACCOUNT MODAL -->
<div id="accountModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 transform transition-all">
    
    <!-- MODAL HEADER -->
    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
      <div>
        <h3 class="text-lg font-black text-slate-900 italic">Create Portal Account</h3>
        <p class="text-xs text-slate-400">Generate customer login credentials</p>
      </div>
      <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 text-sm">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- FORM -->
    <form id="createAccountForm" onsubmit="submitCreateAccount(event)">
      <input type="hidden" id="modal_ticket_id" name="ticket_id">

      <div class="space-y-3.5 text-xs">
        
        <div>
          <label class="block font-bold text-slate-700 mb-1">Email Address</label>
          <input type="email" id="modal_email" name="email" required readonly class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-600 font-semibold focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">First Name</label>
            <input type="text" id="modal_first_name" name="first_name" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Last Name</label>
            <input type="text" id="modal_last_name" name="last_name" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Company Name</label>
          <input type="text" id="modal_company_name" name="company_name" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
          <input type="text" id="modal_phone_number" name="phone_number" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block font-bold text-slate-700">Generated Password</label>
            <button type="button" onclick="generatePassword()" class="text-[10px] font-bold text-indigo-600 hover:underline">Auto Generate</button>
          </div>
          <input type="text" id="modal_password" name="password" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono text-indigo-600 font-bold focus:outline-none focus:border-indigo-500">
        </div>

      </div>

      <!-- MODAL ACTIONS -->
      <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
        <button type="button" onclick="closeCreateModal()" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">
          Cancel
        </button>
        <button type="submit" id="submitBtn" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-all shadow-md inline-flex items-center gap-2">
          <span>Create Account</span>
          <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </button>
      </div>

    </form>

  </div>
</div>

<!-- JAVASCRIPT LOGIC FOR COUNTDOWN ENGINE -->
<script src="../../../assets/js/countdown.js"></script>

<script src="../../../assets/js/tickets.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>