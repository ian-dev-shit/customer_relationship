<?php
// 1. I-import at tawagin ang Service
require_once __DIR__ . '/../Services/SidebarService.php';

use App\Services\SidebarService;

$sidebarService = new SidebarService();
$sidebar = $sidebarService->getSidebarData();

// 2. Extract Variables para sa HTML View
$userRole    = $sidebar['userRole'];
$agentId     = $sidebar['agentId'];
$displayName = $sidebar['displayName'];
$initials    = $sidebar['initials'];
$activePage  = $sidebar['activePage'];
$portalLabel = $sidebar['portalLabel'];
$navSections = $sidebar['navSections'];
?>

<!-- MOBILE OVERLAY BACKDROP -->
<div id="sidebarOverlay" class="fixed inset-0 bg-slate-950/60 z-30 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

<!-- SIDEBAR NAVIGATION CONTAINER -->
<aside id="sidebar" class="w-64 bg-[#080e1e] text-slate-300 min-h-screen flex flex-col justify-between p-4 border-r border-slate-800 shrink-0 z-40 transition-transform duration-300 -translate-x-full md:translate-x-0 fixed md:relative">
    <div class="space-y-6">
        
        <!-- Brand Logo & Dynamic Badge -->
        <div class="flex items-center gap-3 px-2 py-2">
            <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center font-black text-lg shadow-lg shadow-blue-500/30">
                SF
            </div>
            <div class="leading-none">
                <h1 class="text-base font-black text-white tracking-wider">SwiftFreight</h1>
                <span class="text-[9px] text-blue-400 uppercase tracking-widest font-bold"><?= $portalLabel ?></span>
            </div>
        </div>

        <!-- Dynamic Navigation Links Loop -->
        <nav class="space-y-5 text-xs font-medium">
            <?php foreach ($navSections as $sectionTitle => $items): ?>
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">
                        <?= htmlspecialchars($sectionTitle) ?>
                    </span>
                    <ul class="space-y-1">
                        <?php foreach ($items as $key => $item): 
                            $isActive = ($activePage === $key);
                            $linkClass = $isActive 
                                ? 'bg-blue-600/20 text-white rounded-xl font-semibold border border-blue-500/30 shadow-sm' 
                                : 'text-slate-400 hover:text-white hover:bg-white/5 rounded-lg';
                            $iconClass = $isActive ? 'text-blue-400' : 'text-slate-400';
                        ?>
                            <li>
                                <a href="<?= $item['url'] ?>" class="flex items-center justify-between px-3 py-2 <?= $linkClass ?> transition-colors">
                                    <span class="flex items-center gap-3">
                                        <i class="fa-solid <?= $item['icon'] ?> text-sm <?= $iconClass ?>"></i> 
                                        <?= htmlspecialchars($item['label']) ?>
                                    </span>
                                    <?php if (isset($item['badge'])): ?>
                                        <span class="<?= $item['badgeColor'] ?? 'bg-blue-500/20 text-blue-400' ?> font-bold px-1.5 py-0.5 rounded text-[10px]">
                                            <?= $item['badge'] ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Sidebar Bottom Footer -->
    <div class="space-y-4 pt-4 border-t border-slate-800/80">
        <?php if ($userRole === 'customer'): ?>
            <!-- SLA Widget (Customer Only) -->
            <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-xs">
                <div class="flex justify-between items-center text-[10px] font-semibold text-slate-400 mb-1.5">
                    <span>SLA compliance</span>
                    <span class="text-emerald-400 font-bold">94%</span>
                </div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full w-[94%] rounded-full"></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Profile Card & Logout Button -->
        <div class="flex items-center justify-between p-2 bg-slate-900/80 border border-slate-800 rounded-xl">
            <div class="flex items-center gap-2.5 min-w-0">
                <!-- Dynamic Initials -->
                <div class="w-9 h-9 bg-purple-600/20 text-purple-400 font-bold rounded-lg flex items-center justify-center text-xs border border-purple-500/30 shrink-0">
                    <?= htmlspecialchars($initials) ?>
                </div>
                
                <!-- Dynamic Name -->
                <div class="leading-tight min-w-0 flex-1">
                    <h4 class="text-xs font-bold text-white truncate">
                        <?= htmlspecialchars($displayName) ?>
                    </h4>
                    <span class="text-[10px] text-slate-400 block truncate">
                        <?= $userRole === 'sales_agent' 
                            ? 'Sales Agent • ' . htmlspecialchars($agentId) 
                            : htmlspecialchars($displayName) . ' • Acct #' . htmlspecialchars($agentId) ?>
                    </span>
                </div>
            </div>
            
            <!-- Logout Button -->
            <a href="logout.php" id="logoutBtn" title="Logout" class="text-slate-400 hover:text-red-400 p-1.5 transition-colors shrink-0">
                <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
            </a>
        </div>
    </div>
</aside>