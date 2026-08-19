<form method="GET" action="" class="relative">
        <input type="hidden" name="status" value="<?= htmlspecialchars($current_status) ?>">
        <input 
          type="text" 
          name="search" 
          placeholder="Search company or contact..." 
          value="<?= htmlspecialchars($search_query) ?>"
          class="w-64 pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-sm"
        >
        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </form>