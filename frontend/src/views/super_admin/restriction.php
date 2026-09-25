<?php
$page_title = "Super Admin Restrictions · PRIORITY HANDLING";

include_once '../../includes/header.php';

?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php include_once '../../components/top_header.php'; ?>
<!-- PAGE HEADER & CONTROLS -->
    <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 border border-red-200 rounded-xl flex items-center justify-center text-red-600">
                    <i class="fa-solid fa-user-lock text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Account Restrictions</h1>
                    <p class="text-slate-500 text-xs mt-0.5">Monitor failed login attempts and unrestrict locked staff accounts.</p>
                </div>
            </div>
        </div>

        <!-- ACTION CONTROLS -->
        <div class="flex items-center gap-3">
            <label class="inline-flex items-center gap-2 bg-white border border-slate-200 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 cursor-pointer shadow-sm hover:bg-slate-50 transition">
                <input type="checkbox" id="onlyRestrictedToggle" checked onchange="fetchRestrictedUsers()" class="rounded border-slate-300 text-blue-600 focus:ring-0">
                <span>Show Only Restricted</span>
            </label>

            <button onclick="fetchRestrictedUsers()" class="bg-white hover:bg-slate-50 text-slate-700 text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-2 border border-slate-200 shadow-sm font-semibold">
                <i class="fa-solid fa-rotate"></i> Refresh
            </button>
        </div>
    </div>

    <!-- ALERT NOTIFICATION BOX -->
    <div id="alertBox" class="hidden mb-6 p-4 rounded-xl text-xs font-semibold flex items-center justify-between transition-all"></div>

    <!-- RESTRICTED ACCOUNTS TABLE CONTAINER -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="p-4">User Email</th>
                        <th class="p-4 text-center">Failed Attempts</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Last Attempt Date</th>
                        <th class="p-4">Restricted Until</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="restrictedTableBody" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400">
                            <i class="fa-solid fa-circle-notch fa-spin text-xl mb-2"></i>
                            <p>Fetching restricted accounts list...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- JAVASCRIPT LOGIC -->
<script src="../../../assets/js/super_admin/restriction.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>