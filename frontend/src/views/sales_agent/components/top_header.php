<!-- TOP HEADER & NAVBAR -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
  <div class="flex items-center gap-3">
    <button onclick="toggleSidebar()" class="sm:hidden text-slate-600 hover:text-slate-900 p-1">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
    </button>
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight italic">Dashboard</h1>
    </div>
  </div>

  <!-- Global Search Bar -->
  <div class="flex-1 max-w-md mx-4 hidden md:block">
    <div class="relative">
      <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
      </span>
      <input type="text" placeholder="Search leads, customer, quotes...." 
             class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-sm">
    </div>
  </div>

  <!-- Header Actions -->
  <div class="flex items-center gap-3">
    <button type="button" class="p-2 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-slate-800 shadow-sm transition relative">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
      <?php if ($new_inquiry > 0): ?>
        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full"></span>
      <?php endif; ?>
    </button>

    <a href="/quotes/new" class="px-4 py-2 text-xs font-semibold rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow-sm flex items-center gap-1.5 transition">
      <span class="text-base font-bold leading-none">+</span> New Quote
    </a>
  </div>
</div>