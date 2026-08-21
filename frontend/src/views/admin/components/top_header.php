<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
  
  <!-- Page Title / Dashboard Header -->
  <div>
    <h1 class="text-2xl font-black text-slate-900 tracking-tight italic">Dashboard</h1>
  </div>

  <!-- Right Actions: Global Search Bar & Notification Bell -->
  <div class="flex items-center gap-3">
    
    <!-- Search Bar -->
    <div class="relative w-full md:w-80">
      <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
        <i class="fa-solid fa-magnifying-glass text-xs"></i>
      </span>
      <input 
        type="text" 
        placeholder="Search leads, customer, quotes..." 
        class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all shadow-sm"
      />
    </div>

    <!-- Notification Bell Button -->
    <button 
      type="button" 
      class="relative p-2.5 bg-white border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 rounded-xl transition-all shadow-sm shrink-0 flex items-center justify-center w-9 h-9"
      title="Notifications"
    >
      <i class="fa-regular fa-bell text-sm"></i>
      <!-- Red Badge Indicator -->
      <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
    </button>

  </div>

</div>