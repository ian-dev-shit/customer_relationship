<?php
$page_title = "Super Admin Dashboard · PRIORITY HANDLING";
include_once '../../includes/header.php';
?>

<?php include_once '../../includes/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">
  <?php include_once '../../components/top_header.php'; ?>

  <!-- WELCOME BANNER -->
  <div class="mt-6 mb-8 bg-slate-900 text-white rounded-2xl p-6 shadow-sm flex items-center justify-between">
      <div>
          <h1 class="text-2xl font-bold">Welcome back, Super Admin!</h1>
          <p class="text-slate-400 text-xs mt-1">Here is the quick system overview and security status for today.</p>
      </div>
      <a href="restriction.php" class="bg-red-500 hover:bg-red-600 text-white text-xs px-4 py-2.5 rounded-xl transition font-semibold flex items-center gap-2">
          <i class="fa-solid fa-user-lock"></i> Manage Restrictions
      </a>
  </div>

  <!-- STATS CARDS GRID -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      
      <!-- Restricted Users Card -->
      <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
          <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-slate-500">Restricted Accounts</span>
              <div class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                  <i class="fa-solid fa-user-slash"></i>
              </div>
          </div>
          <p id="statRestrictedCount" class="text-2xl font-extrabold text-slate-900 mt-2">--</p>
          <span class="text-[11px] text-slate-400">Locked due to failed logins</span>
      </div>

      <!-- Login Activity Card -->
      <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
          <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-slate-500">Today's Logins</span>
              <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                  <i class="fa-solid fa-right-to-bracket"></i>
              </div>
          </div>
          <p id="statLoginCount" class="text-2xl font-extrabold text-slate-900 mt-2">--</p>
          <span class="text-[11px] text-emerald-600 font-medium">Active user sessions</span>
      </div>

      <!-- Total Audit Logs Card -->
      <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
          <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-slate-500">Audit Logs Today</span>
              <div class="w-8 h-8 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center">
                  <i class="fa-solid fa-list-check"></i>
              </div>
          </div>
          <p id="statAuditCount" class="text-2xl font-extrabold text-slate-900 mt-2">--</p>
          <span class="text-[11px] text-slate-400">Total recorded system events</span>
      </div>

      <!-- System Health Card -->
      <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
          <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-slate-500">System Status</span>
              <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                  <i class="fa-solid fa-server"></i>
              </div>
          </div>
          <p class="text-lg font-bold text-emerald-600 mt-2">Operational</p>
          <span class="text-[11px] text-slate-400">FastAPI & Supabase Connected</span>
      </div>

  </div>

  <!-- MAIN WIDGETS SECTION (2 COLUMNS) -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Recent Restricted Users (2 Cols) -->
      <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
              <h2 class="text-sm font-bold text-slate-900">Recent Restricted Accounts</h2>
              <a href="restriction.php" class="text-xs text-blue-600 font-semibold hover:underline">View All →</a>
          </div>
          <div class="overflow-x-auto">
              <table class="w-full text-left text-xs text-slate-600">
                  <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                      <tr>
                          <th class="p-3">Email</th>
                          <th class="p-3 text-center">Attempts</th>
                          <th class="p-3 text-center">Action</th>
                      </tr>
                  </thead>
                  <tbody id="dashRestrictedBody" class="divide-y divide-slate-100">
                      <!-- Dynamic rows via JavaScript -->
                  </tbody>
              </table>
          </div>
      </div>

      <!-- Recent Audit Trail Activity (1 Col) -->
      <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
              <h2 class="text-sm font-bold text-slate-900">Live Audit Activity</h2>
              <a href="audit_logs.php" class="text-xs text-blue-600 font-semibold hover:underline">View All →</a>
          </div>
          <div id="dashAuditList" class="space-y-4">
              <!-- Dynamic activity items via JavaScript -->
          </div>
      </div>

  </div>
</main>

<script src="../../../assets/js/super_admin/dashboard.js"></script>
<?php include_once '../../includes/footer.php'; ?>