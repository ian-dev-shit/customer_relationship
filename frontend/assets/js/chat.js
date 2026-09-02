const BACKEND_HOST = "127.0.0.1:8000";
let activeWs = null;
let activeConversationId = null;
let allConversations = [];
let currentFilter = 'ALL';

// Global state para sa active chat participant info
let activeCustomerInfo = {
  name: 'Customer',
  initial: 'C',
  avatarUrl: null
};

const container = document.getElementById('chat-app-container');
const LOGGED_USER_ID = container?.dataset.userId || '';
const USER_ROLE = container?.dataset.userRole || 'sales_agent';

// SMART RELATIVE TIME PARSER
function formatRelativeTime(isoString) {
  if (!isoString) return '';

  const past = new Date(isoString);
  const now = new Date();

  if (isNaN(past.getTime())) return isoString;

  const diffInSeconds = Math.floor((now - past) / 1000);

  if (diffInSeconds >= 0 && diffInSeconds < 60) {
    return 'Just now';
  }

  const diffInMinutes = Math.floor(diffInSeconds / 60);
  if (diffInMinutes < 60 && diffInMinutes > 0) {
    return `${diffInMinutes}m`;
  }

  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24 && diffInHours > 0) {
    return `${diffInHours}h`;
  }

  const diffInDays = Math.floor(diffInHours / 24);
  if (diffInDays < 7 && diffInDays > 0) {
    return `${diffInDays}d ago`;
  }

  return past.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

async function loadConversations() {
  const containerEl = document.getElementById('conversation-list');

  try {
    let endpoint = (USER_ROLE === 'sales_agent' || USER_ROLE === 'admin') 
      ? `http://${BACKEND_HOST}/agent/v1/chat/conversations`
      : `http://${BACKEND_HOST}/customer/v1/chat/conversations/${LOGGED_USER_ID}`;

    const res = await fetch(endpoint);
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

    allConversations = await res.json();
    filterConversations();

  } catch (err) {
    console.error("Error loading conversations:", err);
    if (containerEl) {
      containerEl.innerHTML = `<div class="p-6 text-center text-xs text-rose-500">Failed to load conversations.</div>`;
    }
  }
}

// FILTER SYSTEM: ALL | NEW | UNREAD
function setFilter(filterType) {
  currentFilter = filterType;
  
  ['ALL', 'NEW', 'UNREAD'].forEach(f => {
    const btn = document.getElementById(`filter-${f}`);
    if (btn) {
      if (f === filterType) {
        btn.className = "flex-1 py-1.5 rounded-lg bg-white shadow-sm text-blue-600 transition-all font-bold";
      } else {
        btn.className = "flex-1 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition-all font-semibold";
      }
    }
  });

  filterConversations();
}

function filterConversations() {
  const searchQuery = document.getElementById('search-conv')?.value.toLowerCase() || '';
  
  let filtered = allConversations.filter(c => {
    const matchesSearch = (c.customer_name || '').toLowerCase().includes(searchQuery) ||
                          (c.last_message || '').toLowerCase().includes(searchQuery);
    
    if (!matchesSearch) return false;

    if (currentFilter === 'NEW') {
      return c.status === 'new' || c.status === 'ai_handling';
    } else if (currentFilter === 'UNREAD') {
      return c.unread_count > 0 || c.unread === true;
    }
    return true; // ALL
  });

  renderConversations(filtered);
}

function renderConversations(list) {
  const containerEl = document.getElementById('conversation-list');
  if (!containerEl) return;

  if (!list || list.length === 0) {
    containerEl.innerHTML = `<div class="p-6 text-center text-xs text-slate-400">No conversations found.</div>`;
    return;
  }

  containerEl.innerHTML = list.map(c => {
    const displayName = c.customer_name || 'Customer';
    const initial = c.customer_initial || displayName.charAt(0).toUpperCase() || 'C';
    const avatarHtml = c.avatar_url 
      ? `<img src="${c.avatar_url}" class="w-10 h-10 rounded-full object-cover flex-shrink-0" alt="Avatar" />`
      : `<div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">${initial}</div>`;

    return `
      <div onclick="openChat('${c.id}', '${displayName.replace(/'/g, "\\'")}')" 
        id="conv-item-${c.id}"
        class="p-4 flex items-center gap-3 hover:bg-slate-100/70 cursor-pointer transition-colors border-l-4 border-transparent">
        ${avatarHtml}
        <div class="flex-1 min-w-0">
          <div class="flex justify-between items-baseline mb-0.5">
            <h2 class="text-xs font-bold text-slate-800 truncate">
              ${displayName}
            </h2>
            <span class="text-[10px] text-slate-400">
              ${formatRelativeTime(c.last_message_time || c.updated_at || c.created_at)}
            </span>
          </div>
          <p class="text-xs text-slate-500 truncate">${c.last_message || 'No messages yet'}</p>
        </div>
      </div>
    `;
  }).join('');
}

async function openChat(conversationId, displayName) {
  activeConversationId = conversationId;
  
  // Hanapin sa local list ang conversation data
  const convData = allConversations.find(c => c.id === conversationId);
  const initial = convData?.customer_initial || displayName.charAt(0).toUpperCase() || 'C';
  const avatarUrl = convData?.avatar_url || null;

  // I-update ang global active info
  activeCustomerInfo = {
    name: displayName,
    initial: initial,
    avatarUrl: avatarUrl
  };

  document.getElementById('active-chat-name').innerText = displayName;
  
  const activeAvatarEl = document.getElementById('active-avatar');
  if (activeAvatarEl) {
    if (avatarUrl) {
      activeAvatarEl.outerHTML = `<img id="active-avatar" src="${avatarUrl}" class="w-8 h-8 rounded-full object-cover" />`;
    } else {
      activeAvatarEl.outerHTML = `<div id="active-avatar" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs">${initial}</div>`;
    }
  }

  document.getElementById('active-status').innerText = "Active Session";
  document.getElementById('status-dot').className = "w-2 h-2 rounded-full bg-emerald-500";

  document.getElementById('message-input').disabled = false;
  document.getElementById('send-btn').disabled = false;

  const takeoverBtn = document.getElementById('takeover-btn');
  if (takeoverBtn && (USER_ROLE === 'sales_agent' || USER_ROLE === 'admin')) {
    takeoverBtn.classList.remove('hidden');
  }

  const msgBox = document.getElementById('messages-box');
  if (msgBox) {
    msgBox.className = "flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/20";
    msgBox.innerHTML = '';
  }

  // Load Past Messages
  try {
    const res = await fetch(`http://${BACKEND_HOST}/agent/v1/chat/messages/${conversationId}`);
    if (res.ok) {
      const messages = await res.json();
      messages.forEach(msg => {
        appendMessageToThread(msg.sender_type, msg.message, msg.formatted_time);
      });
    }
  } catch (err) {
    console.error("Failed to fetch chat history:", err);
  }

  // WebSocket Connection
  if (activeWs) activeWs.close();
  activeWs = new WebSocket(`ws://${BACKEND_HOST}/customer/v1/chat/ws/chat/${conversationId}`);

  activeWs.onmessage = function(event) {
    const data = JSON.parse(event.data);
    appendMessageToThread(data.sender_type, data.message);
  };
}

function appendMessageToThread(senderType, text, formattedTime = null) {
  const msgBox = document.getElementById('messages-box');
  if (!msgBox) return;

  const displayTime = formattedTime || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

  let isMyMessage = false;
  if (USER_ROLE === 'customer' && senderType === 'customer') {
    isMyMessage = true;
  } else if ((USER_ROLE === 'sales_agent' || USER_ROLE === 'admin') && (senderType === 'agent' || senderType === 'sales_agent')) {
    isMyMessage = true;
  }

  // AVATAR LOGIC FOR INCOMING MESSAGES
  let avatarHtml = '';
  if (senderType === 'customer') {
    if (activeCustomerInfo.avatarUrl) {
      avatarHtml = `<img src="${activeCustomerInfo.avatarUrl}" class="w-7 h-7 rounded-full object-cover flex-shrink-0 mt-1" alt="Avatar" />`;
    } else {
      avatarHtml = `<div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-1">${activeCustomerInfo.initial}</div>`;
    }
  } else if (senderType === 'ai') {
    avatarHtml = `<div class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-1">AI</div>`;
  } else {
    avatarHtml = `<div class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-1">A</div>`;
  }

  const msgHtml = !isMyMessage ? `
    <div class="flex items-start gap-2 max-w-[70%] mb-3">
      ${avatarHtml}
      <div class="bg-slate-100 text-slate-800 text-xs p-3 rounded-2xl rounded-tl-none border border-slate-200/50">
        <p class="leading-relaxed">${text}</p>
        <span class="block text-[9px] text-slate-400 mt-1">${displayTime}</span>
      </div>
    </div>
  ` : `
    <div class="flex items-start gap-2 max-w-[70%] ml-auto flex-row-reverse mb-3">
      <div class="bg-blue-600 text-white text-xs p-3 rounded-2xl rounded-tr-none shadow-sm">
        <p class="leading-relaxed">${text}</p>
        <span class="block text-[9px] text-blue-200 mt-1 text-right">${displayTime}</span>
      </div>
    </div>
  `;

  msgBox.insertAdjacentHTML('beforeend', msgHtml);
  msgBox.scrollTop = msgBox.scrollHeight;
}

function sendMessage(e) {
  e.preventDefault();
  const input = document.getElementById('message-input');
  const message = input.value.trim();

  if (!message || !activeWs || activeWs.readyState !== WebSocket.OPEN) return;

  const payload = {
    sender_type: USER_ROLE === 'sales_agent' ? 'agent' : 'customer',
    sender_id: LOGGED_USER_ID,
    message: message
  };

  activeWs.send(JSON.stringify(payload));
  input.value = '';
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadConversations);
} else {
  loadConversations();
}