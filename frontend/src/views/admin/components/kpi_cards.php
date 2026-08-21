<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

  <!-- Card 1: Active Customers (May Portal Account Na) -->
  <a href="customers.php" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all group">
    <div class="flex items-center justify-between mb-3">
      <span class="text-xs font-semibold text-slate-500 group-hover:text-indigo-600 transition-colors">Active Customer</span>
      <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
        <i class="fa-solid fa-user text-xs"></i>
      </div>
    </div>
    <div>
      <div class="text-3xl font-bold text-slate-900 mb-2"><?= $active_customers ?></div>
      <div class="flex items-center text-xs font-medium text-emerald-600">
        <i class="fa-solid fa-check-circle mr-1 text-[10px]"></i>
        <span>Registered Users</span>
      </div>
    </div>
  </a>

  <!-- Card 2: Customer Ticket (Closed Won - Gagawan Pa Lang ng Account) -->
  <a href="tickets.php" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-rose-300 transition-all group">
    <div class="flex items-center justify-between mb-3">
      <span class="text-xs font-semibold text-slate-500 group-hover:text-rose-600 transition-colors">Customer Ticket</span>
      <div class="w-8 h-8 bg-rose-100 text-rose-500 rounded-lg flex items-center justify-center">
        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
      </div>
    </div>
    <div>
      <div class="text-3xl font-bold text-slate-900 mb-2"><?= $ticket_count ?></div>
      <div class="flex items-center text-xs font-medium text-rose-500">
        <i class="fa-solid fa-clock mr-1 text-[10px]"></i>
        <span>Needs Account Provisioning</span>
      </div>
    </div>
  </a>

</div>