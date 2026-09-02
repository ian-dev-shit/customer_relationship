<?php 
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';

$logged_user_role = $_SESSION['role'] ?? $_SESSION['user_role'] ?? 'sales_agent';

if ($logged_user_role === "sales_agent" || $logged_user_role === "admin") {
    $logged_user_id = $_SESSION['agent_id'] ?? $_SESSION['user_id'] ?? $_SESSION['id'] ?? 'agent_1';
} else {
    $logged_user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 'customer_1';
}
?>

<main class="flex-1 overflow-hidden bg-[#F8FAFC] p-4 lg:p-6 flex flex-col h-screen">

    <?php include_once '../../components/top_header.php' ?>

    <!-- MAIN MESSENGER CONTAINER -->
    <div id="chat-app-container" 
        data-user-id="<?php echo htmlspecialchars($logged_user_id); ?>" 
        data-user-role="<?php echo htmlspecialchars($logged_user_role); ?>" 
        class="mt-4 flex-1 flex bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden min-h-0">
        
        <!-- LEFT SIDEBAR -->
        <div id="sidebar-container" class="w-80 lg:w-96 border-r border-slate-200 flex flex-col bg-slate-50/30">
            <!-- Header & Search -->
            <div class="p-4 border-b border-slate-100 bg-white">
                <div class="flex items-center gap-2 mb-1">
                    <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-slate-800" id="sidebar-title">Conversations</h1>
                        <p class="text-[11px] text-slate-400 font-medium">Priority Handling Inbox</p>
                    </div>
                </div>

                <!-- Search Input -->
                <div class="mt-3 relative">
                    <input type="text" id="search-conv" oninput="filterConversations()" placeholder="Search client or ticket..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-100/80 text-xs rounded-xl border-none focus:ring-2 focus:ring-blue-500 outline-none text-slate-700">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- FILTER TABS: ALL, NEW, UNREAD -->
                <div class="flex gap-1 mt-3 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                    <button onclick="setFilter('ALL')" id="filter-ALL" class="flex-1 py-1.5 rounded-lg bg-white shadow-sm text-blue-600 transition-all">ALL</button>
                    <button onclick="setFilter('NEW')" id="filter-NEW" class="flex-1 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition-all">NEW</button>
                    <button onclick="setFilter('UNREAD')" id="filter-UNREAD" class="flex-1 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition-all">UNREAD</button>
                </div>
            </div>

            <!-- Conversation List -->
            <div id="conversation-list" class="flex-1 overflow-y-auto divide-y divide-slate-100">
                <div class="p-6 text-center text-xs text-slate-400">Loading inbox...</div>
            </div>
        </div>

        <!-- RIGHT CHAT PANEL -->
        <div class="flex-1 flex flex-col bg-white">
            <!-- Top Chat Header -->
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm" id="active-avatar">?</div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800" id="active-chat-name">Select a customer</h2>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-slate-300" id="status-dot"></span>
                            <p class="text-xs text-slate-400 font-medium" id="active-status">Active Session</p>
                        </div>
                    </div>
                </div>

                <!-- TAKEOVER CHAT BUTTON (Unchanged) -->
                <button id="takeover-btn" onclick="toggleAgentTakeover()" class="hidden bg-amber-500 hover:bg-amber-600 text-white text-xs px-3.5 py-2 rounded-lg font-medium transition-colors shadow-sm">
                    Takeover Chat
                </button>
            </div>

            <!-- Messages Container -->
            <div id="messages-box" class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/20 flex flex-col justify-center items-center">
                <!-- Aesthetic Empty State Icon -->
                <div class="text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-3 border border-blue-100 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700">No conversation selected</h3>
                    <p class="text-xs text-slate-400 max-w-xs mt-1">Choose a customer from the inbox to view the conversation and reply in real time.</p>
                </div>
            </div>

            <!-- Input Form -->
            <div class="p-4 border-t border-slate-100 bg-white">
                <form onsubmit="sendMessage(event)" class="flex items-center gap-2">
                    <input type="text" id="message-input" disabled placeholder="Type your message..." 
                           class="flex-1 bg-slate-100/80 text-slate-700 text-sm rounded-xl px-4 py-3 border-none focus:ring-2 focus:ring-blue-500 outline-none disabled:opacity-50">
                    
                    <button type="submit" id="send-btn" disabled
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-5 py-3 rounded-xl font-medium text-sm transition-colors flex items-center gap-2 shadow-sm">
                        <span>Send</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="../../../assets/js/chat.js" defer></script>
<?php include_once '../../includes/footer.php'; ?>