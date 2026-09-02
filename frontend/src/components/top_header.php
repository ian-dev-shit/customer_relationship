<?php
// Dynamic Header Fallbacks
$pageTitle = $pageTitle ?? 'Dashboard';
$role = $userRole ?? $_SESSION['role'] ?? 'customer';
$userName = $displayName ?? $_SESSION['user_name'] ?? 'User';
$userInitials = $initials ?? strtoupper(substr($userName, 0, 2));
$hasNotifications = isset($new_inquiry) && $new_inquiry > 0;
?>

<!-- TOP NAVIGATION HEADER -->
<header class="w-full bg-white border-b border-slate-200/80 px-6 py-3 mb-6 shadow-md shadow-slate-200/60 relative z-20">
  <div class="flex items-center justify-between gap-4">
    
    <!-- LEFT SECTION: Mobile Toggle, Plain Page Title & Dropdowns -->
<div class="flex items-center gap-4">
  <!-- Mobile Sidebar Toggle -->
  <button 
    type="button" 
    onclick="toggleSidebar()" 
    class="md:hidden text-slate-600 hover:text-slate-900 p-1.5 rounded-lg bg-slate-100 border border-slate-200 transition shrink-0"
    aria-label="Toggle Sidebar"
  >
    <i class="fa-solid fa-bars text-sm"></i>
  </button>

  <!-- Plain Text Title -->
  <span class="text-sm font-bold text-slate-800 whitespace-nowrap">
    <?= htmlspecialchars($pageTitle) ?>
  </span>

</div>

    <!-- RIGHT SECTION: Search, Action, Clock & Profile Pill -->
    <div class="flex items-center gap-3 shrink-0">
      
      <!-- EXPANDABLE SEARCH BAR -->
      <div class="relative flex items-center">
        <input 
          type="text" 
          placeholder="Search..." 
          class="w-10 focus:w-48 sm:focus:w-64 transition-all duration-300 ease-in-out bg-slate-50 hover:bg-slate-100 focus:bg-white text-xs text-slate-800 placeholder-slate-400 pl-8 pr-3 py-1.5 rounded-full border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 cursor-pointer focus:cursor-text shadow-xs"
        />
        <i class="fa-solid fa-magnifying-glass text-xs text-slate-400 absolute left-3 pointer-events-none"></i>
      </div>

      <!-- Action Button -->
      <?php if ($role === 'sales_agent'): ?>
        <button type="button" onclick="openLeadModal()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition shadow-sm hover:shadow-indigo-200 flex items-center gap-1.5 active:scale-95 whitespace-nowrap">
          <i class="fa-solid fa-plus text-[10px]"></i>
          <span class="hidden sm:inline">New Leads</span>
        </button>
      <?php elseif ($role === 'admin'): ?>
        <a href="tickets.php?action=new" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-1.5 active:scale-95 whitespace-nowrap">
          <i class="fa-solid fa-plus text-[10px]"></i>
          <span class="hidden sm:inline">Create Ticket</span>
        </a>
      <?php elseif ($role === 'customer'): ?>
        <a href="shipments.php?action=new" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-sm hover:shadow-blue-200 flex items-center gap-1.5 active:scale-95 whitespace-nowrap">
          <i class="fa-solid fa-box-archive text-[10px]"></i>
          <span class="hidden sm:inline">New Booking</span>
        </a>
      <?php endif; ?>

      <!-- Notification Bell -->
      <button 
        type="button" 
        class="relative p-1.5 text-slate-500 hover:text-slate-800 transition shrink-0 rounded-lg hover:bg-slate-100"
        title="Notifications"
      >
        <i class="fa-regular fa-bell text-sm"></i>
        <?php if ($hasNotifications): ?>
          <span class="absolute top-1 right-1 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white animate-pulse"></span>
        <?php endif; ?>
      </button>

      <!-- TIME & PROFILE PILL BADGE -->
      <div class="flex items-center bg-slate-100 border border-slate-200/80 rounded-full pl-3 pr-1 py-1 gap-2.5 text-xs font-semibold text-slate-700 shadow-xs">
        <div class="flex items-center gap-1.5 text-[11px] font-mono text-slate-600">
          <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>
          <span id="headerClock">--:-- --</span>
        </div>

        <div class="w-6 h-6 rounded-full bg-slate-800 text-white text-[10px] font-bold flex items-center justify-center shrink-0 shadow-xs">
          <?= htmlspecialchars($userInitials) ?>
        </div>
      </div>

    </div>

  </div>
</header>

<!-- JAVASCRIPT FOR CLOCK & DROPDOWNS -->
<script>
  // Real-time Clock Script
  function updateHeaderClock() {
    const clockElement = document.getElementById('headerClock');
    if (!clockElement) return;
    
    const now = new Date();
    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    
    hours = hours % 12;
    hours = hours ? hours : 12;
    
    clockElement.textContent = `${hours}:${minutes} ${ampm}`;
  }
  updateHeaderClock();
  setInterval(updateHeaderClock, 1000);

  // Dropdown Toggle Script
  function toggleHeaderDropdown(event, menuId) {
    event.stopPropagation();
    const targetMenu = document.getElementById(menuId);
    
    // Close other open dropdowns
    document.querySelectorAll('.header-dropdown > div').forEach(menu => {
      if (menu.id !== menuId) menu.classList.add('hidden');
    });

    targetMenu.classList.toggle('hidden');
  }

  // Close dropdowns when clicking outside
  document.addEventListener('click', () => {
    document.querySelectorAll('.header-dropdown > div').forEach(menu => {
      menu.classList.add('hidden');
    });
  });
</script>