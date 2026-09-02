<!-- components/header.php -->

<!-- 1. TOP CONTACT BAR (Nipis na bar sa pinakataas) -->
<div class="bg-[#030712] text-[11px] text-slate-400 border-b border-white/5 py-1.5 px-6">
    <div class="max-w-[1400px] mx-auto flex justify-between items-center">
        <!-- Left: Phone & Email -->
        <div class="flex items-center gap-5">
            <a href="tel:+6328437484" class="flex items-center gap-1.5 hover:text-white transition">
                <i class="fa-solid fa-phone text-sky-400 text-[10px]"></i> (632) 843-7484
            </a>
            <a href="mailto:cs@priority-ph.com" class="flex items-center gap-1.5 hover:text-white transition">
                <i class="fa-solid fa-envelope text-sky-400 text-[10px]"></i> cs@priority-ph.com
            </a>
        </div>
        
        <!-- Right: Location & Status -->
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-location-dot text-sky-400 text-[10px]"></i> Makati City, Philippines
            </span>
            <span class="text-emerald-400 font-medium flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Open 24/7
            </span>
        </div>
    </div>
</div>

<!-- 2. MAIN HEADER (Logo sa Left, Internal Network sa Right) -->
<header class="bg-[#050b14]/90 backdrop-blur-md border-b border-white/10 py-3 px-6 sticky top-0 z-40">
    <div class="max-w-[1400px] mx-auto flex justify-between items-center">
        
        <!-- LEFT SIDE: Logo & Company Name -->
        <a href="index.php" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-700 to-indigo-900 flex items-center justify-center text-white shadow-lg shadow-blue-900/40">
                <i class="fa-solid fa-cube text-lg"></i>
            </div>
            <div>
                <h1 class="text-base font-black tracking-wider text-white leading-none">
                    PRIORITY <span class="text-blue-500">HANDLING</span>
                </h1>
                <p class="text-[9px] font-bold text-slate-400 tracking-widest uppercase mt-1">
                    LOGISTICS INC. • SINCE 2005
                </p>
            </div>
        </a>

        <!-- RIGHT SIDE: Internal Network Tag -->
        <div class="text-xs text-slate-400 font-medium">
            Internal Network - Ops
        </div>

    </div>
</header>