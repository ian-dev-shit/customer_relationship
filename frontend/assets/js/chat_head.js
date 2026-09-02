const BACKEND_HOST = "127.0.0.1:8000";
let chatHeadWs = null;
let currentConversationId = null;
let isChatOpen = false;

// Safe retrieval ng User ID
const LOGGED_USER_ID = document.body ? (document.body.dataset.userId || '') : '';

// Audio Notification Context
let audioCtx = null;
function playChatNotificationSound() {
    try {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(587.33, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.15);
        gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.25);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.25);
    } catch(e) {
        console.log("Audio not allowed yet");
    }
}

// Utility: Format Time
function formatMessageTime(isoOrTimeStr) {
    if (!isoOrTimeStr) {
        return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    }
    const parsedDate = new Date(isoOrTimeStr);
    if (!isNaN(parsedDate.getTime())) {
        return parsedDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    }
    return isoOrTimeStr;
}

// 1. TOGGLE WINDOW
function toggleChatWindow() {
    const windowEl = document.getElementById('chat-head-window');
    const badgeEl = document.getElementById('chat-head-badge');
    
    if (!windowEl) return;
    
    isChatOpen = !isChatOpen;
    
    if (isChatOpen) {
        windowEl.classList.remove('hidden');
        if (badgeEl) {
            badgeEl.innerText = "0";
            badgeEl.classList.add('hidden');
        }
        loadAgentConversations();
    } else {
        windowEl.classList.add('hidden');
    }
}

// 2. FETCH ALL CONVERSATIONS
async function loadAgentConversations() {
    const inboxList = document.getElementById('customer-inbox-list');
    if (!inboxList) return; // Exit kung wala sa layout

    try {
        const res = await fetch(`http://${BACKEND_HOST}/agent/v1/chat/conversations`);
        if (res.ok) {
            const conversations = await res.json();
            
            if (!conversations || conversations.length === 0) {
                inboxList.innerHTML = `<div class="p-4 text-xs text-slate-400 text-center">Walang conversations</div>`;
                return;
            }

            inboxList.innerHTML = '';
            conversations.forEach((conv, index) => {
                const isActive = conv.id === currentConversationId;
                const displayName = conv.customer_name || conv.customer_email || `Customer #${conv.customer_id ? conv.customer_id.substring(0, 5) : 'User'}`;
                const initial = displayName.charAt(0).toUpperCase();

                const item = document.createElement('div');
                item.className = `p-3 cursor-pointer flex items-center gap-3 transition-colors ${isActive ? 'bg-blue-50/80 border-l-4 border-blue-600' : 'hover:bg-slate-100'}`;
                item.onclick = () => selectConversation(conv.id, displayName);
                
                item.innerHTML = `
                    <div class="relative flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shadow-sm">
                            ${initial}
                        </div>
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-white ${conv.status === 'ai_handling' ? 'bg-amber-400' : 'bg-emerald-500'}"></span>
                    </div>
                    <div class="overflow-hidden flex-1">
                        <p class="text-xs font-bold text-slate-800 truncate">${displayName}</p>
                        <p class="text-[11px] text-slate-500 truncate">${conv.last_message || 'No messages yet'}</p>
                    </div>
                `;
                inboxList.appendChild(item);

                if (!currentConversationId && index === 0) {
                    selectConversation(conv.id, displayName);
                }
            });
        }
    } catch (err) {
        console.error("Error fetching inbox conversations:", err);
    }
}

// 3. SELECT CONVERSATION
async function selectConversation(convId, customerName) {
    currentConversationId = convId;
    
    const headerTitle = document.getElementById('chat-header-title');
    if (headerTitle) {
        const initial = customerName.charAt(0).toUpperCase();
        headerTitle.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                    ${initial}
                </div>
                <span class="text-xs font-bold text-slate-800">${customerName}</span>
            </div>
        `;
    }
    
    await loadChatHeadHistory(convId);
    connectChatHeadWs(convId);
}

// 4. LOAD MESSAGES
async function loadChatHeadHistory(convId) {
    const msgBox = document.getElementById('chat-head-messages');
    if (!msgBox) return;

    try {
        const res = await fetch(`http://${BACKEND_HOST}/agent/v1/chat/messages/${convId}`);
        if (res.ok) {
            const messages = await res.json();
            msgBox.innerHTML = '';

            if (messages.length === 0) {
                msgBox.innerHTML = `
                    <div class="flex items-center justify-center h-full text-slate-400 text-xs">
                        No message..
                    </div>
                `;
                return;
            }

            messages.forEach(msg => appendChatHeadBubble(msg.sender_type, msg.message, msg.created_at || msg.formatted_time));
        }
    } catch (err) {
        console.error("Error fetching history:", err);
    }
}

// 5. WEBSOCKET CONNECTION
function connectChatHeadWs(convId) {
    if (chatHeadWs) chatHeadWs.close();
    
    chatHeadWs = new WebSocket(`ws://${BACKEND_HOST}/customer/v1/chat/ws/chat/${convId}`);

    chatHeadWs.onmessage = function(event) {
        const data = JSON.parse(event.data);
        
        const msgBox = document.getElementById('chat-head-messages');
        if (msgBox && msgBox.querySelector('.text-slate-400')) {
            msgBox.innerHTML = '';
        }

        if (data.conversation_id === currentConversationId || !data.conversation_id) {
            appendChatHeadBubble(data.sender_type, data.message, data.created_at);
        }

        if (data.sender_type === 'customer') {
            playChatNotificationSound();
            
            if (!isChatOpen) {
                const badgeEl = document.getElementById('chat-head-badge');
                if (badgeEl) {
                    let count = parseInt(badgeEl.innerText || '0') + 1;
                    badgeEl.innerText = count;
                    badgeEl.classList.remove('hidden');
                }
            }
        }

        loadAgentConversations();
    };
}

// 6. RENDER BUBBLE 
function appendChatHeadBubble(senderType, text, rawTime = null) {
    const msgBox = document.getElementById('chat-head-messages');
    if (!msgBox) return;

    const formattedTime = formatMessageTime(rawTime);
    const isSelf = senderType === 'agent' || senderType === 'sales_agent';

    const headerTitleEl = document.getElementById('chat-header-title');
    const customerInitial = headerTitleEl ? headerTitleEl.innerText.trim().charAt(0).toUpperCase() : 'C';

    const html = isSelf ? `
        <div class="flex justify-end mb-3">
            <div class="bg-blue-600 text-white rounded-2xl rounded-tr-none px-3.5 py-2 max-w-[80%] shadow-sm">
                <p class="leading-relaxed">${text}</p>
                <span class="block text-[9px] text-blue-100 mt-0.5 text-right font-medium">${formattedTime}</span>
            </div>
        </div>
    ` : `
        <div class="flex items-start gap-2 mb-3">
            ${senderType === 'ai' ? `
                <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 text-sm flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                    🤖
                </div>
            ` : `
                <div class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                    ${customerInitial}
                </div>
            `}
            <div class="bg-slate-100 text-slate-800 rounded-2xl rounded-tl-none px-3.5 py-2 max-w-[75%] border border-slate-200">
                <p class="leading-relaxed">${text}</p>
                <span class="block text-[9px] text-slate-400 mt-0.5 font-medium">${formattedTime}</span>
            </div>
        </div>
    `;

    msgBox.insertAdjacentHTML('beforeend', html);
    msgBox.scrollTop = msgBox.scrollHeight;
}

// 7. SEND MESSAGE
function sendChatHeadMessage(e) {
    e.preventDefault();
    const input = document.getElementById('chat-head-input');
    if (!input) return;

    const message = input.value.trim();
    if (!message || !currentConversationId) return;

    if (!chatHeadWs || chatHeadWs.readyState !== WebSocket.OPEN) {
        connectChatHeadWs(currentConversationId);
        setTimeout(() => sendChatHeadMessage(e), 500);
        return;
    }

    const payload = {
        sender_type: 'sales_agent',
        sender_id: LOGGED_USER_ID,
        message: message
    };

    chatHeadWs.send(JSON.stringify(payload));
    input.value = '';
}

// Safe Initialization
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('customer-inbox-list')) {
        loadAgentConversations();
    }
});