<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

// 1. Extract Agent ID (UUID mula sa profiles/auth table)
$agentId = $_SESSION['user_id'] 
    ?? $_SESSION['id'] 
    ?? $_SESSION['user']['id'] 
    ?? $_SESSION['agent_id'] 
    ?? '';

// 2. Extract Name (Gamit ang first_name at last_name columns mo)
$firstName = $_SESSION['first_name'] ?? $_SESSION['user']['first_name'] ?? '';
$lastName  = $_SESSION['last_name']  ?? $_SESSION['user']['last_name']  ?? '';

if (!empty($firstName) && !empty($lastName)) {
    $agentName = strtoupper($firstName[0]) . '. ' . ucfirst($lastName);
} elseif (!empty($firstName)) {
    $agentName = ucfirst($firstName);
} else {
    $agentName = $_SESSION['full_name'] ?? $_SESSION['user_name'] ?? 'M. Reyes';
}

// 3. Extract Email (Mula sa Auth Session)
$agentEmail = $_SESSION['email'] 
    ?? $_SESSION['user']['email'] 
    ?? $_SESSION['agent_email'] 
    ?? '';

// 4. Check kung nasa main chat page (chat.php)
$currentScript = $_SERVER['SCRIPT_NAME'] ?? '';
$isChatPage = (strpos($currentScript, 'chat.php') !== false);

// 5. Resolve ang role ng naka-login ('customer' | 'sales_agent' | 'admin').
// Ang "chat-head" floating widget ay AGENT/ADMIN inbox lang; kung customer
// ang naka-login, ang AI chat widget ang dapat lumabas (iwas doubled button).
$currentUserRole = strtolower((string) ($_SESSION['user_role'] ?? $_SESSION['role'] ?? 'customer'));
$showAgentChatHead = !$isChatPage && $currentUserRole !== 'customer';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Priority Handling Logistics - Sales Portal' ?></title>
    
    <!-- Browser Tab Favicon (New Logo) -->
     <link rel="icon" type="image/png" href="../../../assets/image/new_logo.png">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Consolidated custom CSS (style + view_leads_modal + theme + auth),
         processed by the Tailwind Play CDN via <style type="text/tailwindcss">.
         No external .css files. -->
    <?php include_once __DIR__ . '/tailwind_css.php'; ?>
    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style type="text/tailwindcss">
    /* Minimal Purple Map Theme Filter */
        #routes-map .leaflet-tile-pane {
        filter: grayscale(100%) brightness(105%) contrast(90%);
        }
    </style>

    <?php 
    if (file_exists(__DIR__ . '/tailwind_config.php')) {
        include_once __DIR__ . '/tailwind_config.php';
    } 
    ?>

    <!-- SAVED-PREFERENCE BOOTSTRAP: applies saved theme/accent/density before first paint -->
    <script>
    (function () {
        try {
            var raw = window.localStorage.getItem('crm_customer_prefs');
            var prefs = raw ? JSON.parse(raw) : {};
            if (prefs.dark_mode === true) document.documentElement.classList.add('dark');
            var accent = (typeof prefs.accent_color === 'string') ? prefs.accent_color : '';
            if (accent) document.documentElement.setAttribute('data-accent', accent);
            if (prefs.density === 'compact') document.documentElement.setAttribute('data-density', 'compact');
        } catch (e) { /* fall back to defaults silently */ }
    })();
    </script>

    <!-- DESIGN TOKEN BRIDGE: exposes the design tokens (includes/tailwind_css.php) to ApexCharts/Chart.js -->
    <script>
    window.crmPalette = function () {
        var cs = getComputedStyle(document.documentElement);
        var v = function (name, fallback) { var val = cs.getPropertyValue(name).trim(); return val || fallback; };
        return {
            chart: [v('--chart-1','#1d2e6a'),v('--chart-2','#084163'),v('--chart-3','#4e83c5'),v('--chart-4','#8d9dd0'),v('--chart-5','#b9c4e3'),v('--chart-6','#dbe1f1')],
            stage: { new: v('--stage-new','#8d9dd0'), qualifying: v('--stage-qualifying','#4e83c5'), quote: v('--stage-quote','#2b3f7c'), negotiation: v('--stage-negotiation','#1d2e6a'), won: v('--stage-won','#047857'), wonSoft: v('--stage-won-soft','#ecfdf5'), lost: v('--stage-lost','#b91c1c'), lostSoft: v('--stage-lost-soft','#fef2f2') },
            navy: v('--navy-700','#1d2e6a'), sky: v('--sky-brand','#4e83c5'),
            ink: v('--fg-heading','#1b1b1c'), muted: v('--fg-muted','#6e6e6e'),
            grid: v('--line','#dfe3ee'), surface: v('--surface','#ffffff'), canvas: v('--canvas','#f2f4f9')
        };
    };
    window.CRM_COLORS = window.crmPalette();
    window.crmSetDarkMode = function (on) {
        document.documentElement.classList.toggle('dark', on);
        try {
            var raw = window.localStorage.getItem('crm_customer_prefs');
            var prefs = raw ? JSON.parse(raw) : {};
            prefs.dark_mode = !!on;
            window.localStorage.setItem('crm_customer_prefs', JSON.stringify(prefs));
        } catch (e) {}
        document.dispatchEvent(new CustomEvent('crm:theme-change', { detail: { dark: !!on } }));
    };
    </script>

    <!-- Optional per-page <head> assets -->
    <?php if (!empty($extraHead)) { echo $extraHead; } ?>

    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

</head>
<body 
    class="crm-body bg-canvas text-slate-800 font-sans antialiased h-screen overflow-hidden flex"
    data-agent-id="<?= htmlspecialchars($agentId) ?>"
    data-agent-name="<?= htmlspecialchars($agentName) ?>"
    data-agent-email="<?= htmlspecialchars($agentEmail) ?>"
>

<?php if ($showAgentChatHead): ?>
    <!-- FLOATING CHAT HEAD BUTTON (Agent/Admin portal lang; HINDI chat.php) -->
    <div id="chat-head-button" onclick="toggleChatWindow()" class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-2xl flex items-center justify-center cursor-pointer transition-transform hover:scale-105">
        <i class="fa-solid fa-comments text-xl"></i>
        <!-- Unread Badge -->
        <span id="chat-head-badge" class="hidden absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white">0</span>
    </div>

    <!-- FLOATING CHAT WINDOW -->
    <div id="chat-head-window" class="hidden fixed bottom-24 right-6 z-50 w-[680px] h-[520px] bg-white rounded-2xl shadow-2xl border border-slate-200 flex overflow-hidden font-sans">
        
        <!-- LEFT SIDE: Customer Inbox List -->
        <div class="w-1/3 border-r border-slate-200 bg-slate-50/50 flex flex-col">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-white">
                <span class="font-bold text-slate-800 text-sm">Customer Inbox</span>
                <button onclick="loadAgentConversations()" class="text-blue-600 hover:text-blue-700 text-xs font-semibold">Refresh</button>
            </div>
            <div id="customer-inbox-list" class="flex-1 overflow-y-auto divide-y divide-slate-100">
                <!-- Dynamic Items loaded via JS -->
            </div>
        </div>

        <!-- RIGHT SIDE: Active Chat Area -->
        <div class="w-2/3 flex flex-col bg-white">
            <!-- Header -->
            <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-white">
                <div id="chat-header-title" class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-800">Choose Customer</span>
                </div>
                <button onclick="toggleChatWindow()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
            </div>

            <!-- Messages Area -->
            <div id="chat-head-messages" class="flex-1 p-4 overflow-y-auto space-y-3 text-xs bg-white">
                <div class="flex items-center justify-center h-full text-slate-400 text-xs">
                    Pumili ng customer para magsimula.
                </div>
            </div>

            <!-- Input Box -->
            <form onsubmit="sendChatHeadMessage(event)" class="p-3 bg-white border-t border-slate-100 flex items-center gap-2">
                <input 
                    type="text" 
                    id="chat-head-input" 
                    placeholder="I-type ang mensahe..." 
                    class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 transition-colors" 
                />
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition-colors">
                    Send
                </button>
            </form>
        </div>
    </div>

    <!-- EXTERNAL CHAT HEAD SCRIPT -->
    <script src="/assets/js/chat_head.js"></script>
<?php endif; ?>

<script>
    window.APP_CONFIG = {
        
        API_BASE_URL: <?= json_encode(PUBLIC_API_URL) ?>
    };
</script>