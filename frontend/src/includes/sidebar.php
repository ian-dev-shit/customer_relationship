<?php
// 1. I-import at tawagin ang Service
require_once __DIR__ . '/../services/SidebarService.php';

use App\services\SidebarService;

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
<div id="sidebarOverlay" class="fixed inset-0 bg-navy-950/60 z-30 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

<!-- SIDEBAR NAVIGATION CONTAINER -->
<!--
  Desktop pinning: h-screen (NOT min-h-screen) caps the rail at one viewport so
  it can slide within the taller body, and md:sticky md:top-0 holds it there
  while <main> scrolls past. md:self-start stops flex `stretch` from re-growing
  it, and md:overflow-y-auto lets the nav list scroll internally if it is taller
  than the viewport. Every added utility is md:-scoped (Tailwind md = 768px),
  which matches the off-canvas breakpoint in includes/tailwind_css.php section 16 (MOBILE RAIL),
  so the -translate-x-full / fixed drawer behaviour is untouched on mobile.
  Do not reintroduce md:relative or min-h-screen here - either one breaks the pin.
-->
<aside id="sidebar" class="group crm-sidebar w-20 hover:w-64 text-slate-300 h-screen flex flex-col justify-between p-4 shrink-0 z-40 transition-all duration-300 ease-in-out -translate-x-full md:translate-x-0 fixed md:sticky md:top-0 md:self-start overflow-x-hidden md:overflow-y-auto">
    <div class="space-y-6">

        <!-- Brand Logo & Dynamic Badge -->
        <div class="flex items-center gap-3 px-1.5 py-2">
            <div class="w-10 h-10 rounded-xl overflow-hidden shadow-lg shadow-navy flex items-center justify-center bg-white/5 border border-white/10 shrink-0">
                <img src="../../../assets/image/new_logo.png" alt="Company Logo" class="w-full h-full object-contain p-1">
            </div>
            <div class="leading-none crm-reveal whitespace-nowrap overflow-hidden">
                <h1 class="crm-brand-name">PRIORITY <span class="text-brand-blue">HANDLING</span></h1>
                <span class="crm-brand-badge"><?= htmlspecialchars($portalLabel) ?></span>
            </div>
        </div>

        <!-- Dynamic Navigation Links Loop -->
        <nav class="space-y-5 text-xs font-medium">
            <?php foreach ($navSections as $sectionTitle => $items): ?>
                <div>
                    <span class="crm-nav-group crm-reveal whitespace-nowrap overflow-hidden">
                        <?= htmlspecialchars($sectionTitle) ?>
                    </span>
                    <ul class="space-y-1">
                        <?php foreach ($items as $key => $item):
                            $isActive = ($activePage === $key);
                            $hasSubmenu = isset($item['submenu']) && is_array($item['submenu']);
                        ?>
                            <li>
                                <?php if ($hasSubmenu): ?>
                                    <!-- DROPDOWN ITEM -->
                                    <button type="button" onclick="toggleSubmenu('sub-<?= $key ?>', 'arrow-<?= $key ?>')" class="crm-nav-item <?= $isActive ? 'is-active' : '' ?> group/btn">
                                        <span class="flex items-center gap-2.5 min-w-0 shrink-0">
                                            <span class="crm-nav-ico"><i class="fa-solid <?= $item['icon'] ?>"></i></span>
                                            <span class="crm-reveal whitespace-nowrap overflow-hidden">
                                                <?= htmlspecialchars($item['label']) ?>
                                            </span>
                                        </span>
                                        <i id="arrow-<?= $key ?>" class="fa-solid fa-chevron-down text-[10px] crm-reveal opacity-60 transition-transform duration-200"></i>
                                    </button>

                                    <!-- SUBMENU LIST -->
                                    <?php $isChildActive = in_array($activePage, array_keys($item['submenu'])); ?>
                                    <ul id="sub-<?= $key ?>" class="<?= $isChildActive ? '' : 'hidden ' ?>pl-4 pr-1 pt-1 pb-1 space-y-0.5 crm-reveal">
                                        <?php foreach ($item['submenu'] as $subKey => $subItem):
                                            $isSubActive = ($activePage === $subKey);
                                        ?>
                                            <li>
                                                <a href="<?= $subItem['url'] ?>" class="crm-sub-item <?= $isSubActive ? 'is-active' : '' ?>">
                                                    <?php if (isset($subItem['icon'])): ?>
                                                        <i class="fa-solid <?= $subItem['icon'] ?> crm-sub-ico"></i>
                                                    <?php endif; ?>
                                                    <span class="truncate"><?= htmlspecialchars($subItem['label']) ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                <?php else: ?>
                                    <!-- STANDARD ITEM -->
                                    <a href="<?= $item['url'] ?>" class="crm-nav-item <?= $isActive ? 'is-active' : '' ?>">
                                        <span class="flex items-center gap-2.5 min-w-0 shrink-0">
                                            <span class="crm-nav-ico"><i class="fa-solid <?= $item['icon'] ?>"></i></span>
                                            <span class="crm-reveal whitespace-nowrap overflow-hidden">
                                                <?= htmlspecialchars($item['label']) ?>
                                            </span>
                                        </span>
                                        <?php if (isset($item['badge'])): ?>
                                            <span class="crm-nav-badge crm-reveal"><?= htmlspecialchars($item['badge']) ?></span>
                                        <?php endif; ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Sidebar Bottom Footer -->
    <div class="crm-side-foot space-y-3 pt-4">
        <?php if ($userRole === 'customer'): ?>
            <!-- SLA Widget (Customer Only) -->
            <div class="crm-sla-box crm-reveal">
                <div class="flex justify-between items-center text-[10px] font-semibold text-slate-400 mb-1.5 whitespace-nowrap">
                    <span>SLA compliance</span>
                    <span class="text-emerald-400 font-bold">94%</span>
                </div>
                <div class="crm-sla-track">
                    <div class="crm-sla-fill w-[94%]"></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Profile Card & Logout Button -->
        <div class="crm-side-card">
            <div class="flex items-center gap-2.5 min-w-0">
                <!-- Dynamic Initials -->
                <div class="crm-avatar-navy">
                    <?= htmlspecialchars($initials) ?>
                </div>

                <!-- Dynamic Name -->
                <div class="leading-tight min-w-0 flex-1 crm-reveal whitespace-nowrap overflow-hidden">
                    <h4 class="crm-side-name">
                        <?= htmlspecialchars($displayName) ?>
                    </h4>
                    <span class="crm-side-meta">
                        <?= $userRole === 'sales_agent'
                            ? 'Sales Agent • ' . htmlspecialchars($agentId)
                            : htmlspecialchars($displayName) . ' • Acct #' . htmlspecialchars($agentId) ?>
                    </span>
                </div>
            </div>

            <!-- Logout Button -->
            <a href="../../php/logout.php" id="logoutBtn" title="Logout" class="crm-logout-btn crm-reveal">
                <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
            </a>
        </div>
    </div>
</aside>

<script>
    function toggleSubmenu(id, arrowId) {
        const submenu = document.getElementById(id);
        const arrow = document.getElementById(arrowId);
        submenu.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }
</script>