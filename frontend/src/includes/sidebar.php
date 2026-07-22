

<!-- Sidebar -->
  <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 border-r border-slate-800 transform -translate-x-full lg:translate-x-0 transition-transform duration-200">
    <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800">
      <div class="w-8 h-8 rounded bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-19.5-3h19.5m-19.5-3h19.5m-19.5-3h19.5M4.5 3h15a2.25 2.25 0 012.25 2.25v.75a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25v-.75A2.25 2.25 0 014.5 3z"/></svg>
      </div>
      <span class="font-bold text-white tracking-tight">CargoNet</span>
    </div>
    <nav class="p-4 space-y-1" id="sidebar-nav">
      <!-- Will be built by JS -->
    </nav>
    <div class="absolute bottom-0 w-full p-4 border-t border-slate-800">
      <div class="flex items-center gap-3 px-3">
        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center text-xs font-bold text-white">JD</div>
        <div class="text-left">
          <div class="text-sm font-medium text-white">John Doe</div>
          <div class="text-xs text-slate-400">Customer</div>
        </div>
        <button onclick="App.logout()" class="ml-auto text-slate-400 hover:text-rose-400" title="Logout">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 12h-6m6 0l-3-3m3 3l-3 3"/></svg>
        </button>
      </div>
    </div>
  </aside>