<?php
$page_title = "Audit Trail · PRIORITY HANDLING";

include_once '../../includes/header.php';
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php include_once '../../components/top_header.php'; ?>

  <!-- PAGE HEADER & FILTERS -->
  <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      <div>
          <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-blue-100 border border-blue-200 rounded-xl flex items-center justify-center text-blue-600">
                  <i class="fa-solid fa-list-check text-lg"></i>
              </div>
              <div>
                  <h1 class="text-2xl font-bold text-slate-900">Audit Trail Logs</h1>
                  <p class="text-slate-500 text-xs mt-0.5">Track system logins and actions executed by admins, sales agents, and customers.</p>
              </div>
          </div>
      </div>

      <!-- FILTER CONTROLS -->
      <div class="flex items-center gap-3">
          <select id="roleFilter" onchange="fetchAuditLogs()" class="bg-white border border-slate-200 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 shadow-sm focus:outline-none">
              <option value="">All Roles</option>
              <option value="super_admin">Super Admin</option>
              <option value="admin">Admin</option>
              <option value="sales_agent">Sales Agent</option>
              <option value="customer">Customer</option>
          </select>

          <button onclick="fetchAuditLogs()" class="bg-white hover:bg-slate-50 text-slate-700 text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-2 border border-slate-200 shadow-sm font-semibold">
              <i class="fa-solid fa-rotate"></i> Refresh
          </button>
      </div>
  </div>

  <!-- AUDIT LOGS TABLE CONTAINER -->
  <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
      <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-600">
              <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                  <tr>
                      <th class="p-4">Timestamp</th>
                      <th class="p-4">User Email</th>
                      <th class="p-4">Role</th>
                      <th class="p-4">Action</th>
                      <th class="p-4">Module</th>
                      <th class="p-4">Details</th>
                  </tr>
              </thead>
              <tbody id="auditTableBody" class="divide-y divide-slate-100">
                  <tr>
                      <td colspan="6" class="text-center py-10 text-slate-400">
                          <i class="fa-solid fa-circle-notch fa-spin text-xl mb-2"></i>
                          <p>Fetching audit logs...</p>
                      </td>
                  </tr>
              </tbody>
          </table>
      </div>
  </div>

</main>

<script src="../../../assets/js/super_admin/audith.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

