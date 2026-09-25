

    document.addEventListener("DOMContentLoaded", () => {
        loadCampaignPosts();
    });

    async function loadCampaignPosts() {
        const container = document.getElementById("campaignPostsContainer");

        container.innerHTML = `
            <div class="w-full text-center py-12 bg-white border border-slate-200 rounded-2xl shadow-sm">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl text-blue-600 mb-2"></i>
                <p class="text-slate-500 text-xs font-medium">Ikinakarga ang mga campaign posts...</p>
            </div>
        `;

        try {
            const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/customer/dashboard/campaign-posts`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: Hindi ma-fetch ang mga campaign post.`);
            }

            const posts = await response.json();

            if (!Array.isArray(posts) || posts.length === 0) {
                container.innerHTML = `
                    <div class="w-full text-center py-12 bg-white border border-slate-200 rounded-2xl shadow-sm">
                        <i class="fa-solid fa-bullhorn text-3xl text-slate-300 mb-2"></i>
                        <p class="text-slate-500 text-xs font-semibold">Walang available na campaign o promo sa kasalukuyan.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = posts.map(post => {
                const imageUrl = post.image_url ? escapeHtml(post.image_url) : null;
                const title = escapeHtml(post.title || 'Priority Handling Services');
                const description = escapeHtml(post.description || '');
                const formattedDate = formatDate(post.created_at);
                const isPermanent = Boolean(post.is_permanent);

                const badgeHtml = isPermanent 
                    ? `<span class="bg-purple-50 text-purple-700 border border-purple-200 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">ANNOUNCEMENT</span>`
                    : `<span class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">PROMO</span>`;

                return `
                    <div class="w-full bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col">
                        ${imageUrl ? `
                            <div class="w-full bg-slate-900 overflow-hidden relative cursor-pointer group flex justify-center" onclick="openImageModal('${imageUrl}')">
                                <img src="${imageUrl}" alt="${title}" class="w-full h-auto max-h-[600px] object-contain group-hover:scale-[1.01] transition duration-300">
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-2">
                                    <i class="fa-solid fa-magnifying-glass-plus text-base"></i> I-click para i-fullscreen
                                </div>
                            </div>
                        ` : ''}
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    ${badgeHtml}
                                    <span class="text-xs text-slate-400 font-medium">
                                        ${formattedDate}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-1">${title}</h3>
                                ${description ? `<p class="text-slate-600 text-xs leading-relaxed mt-1">${description}</p>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

        } catch (err) {
            console.error("Error loading campaigns:", err);
            container.innerHTML = `
                <div class="w-full text-center py-8 bg-red-50 border border-red-200 rounded-2xl text-red-600 text-xs font-medium">
                    <p class="font-bold mb-1">Hindi maikarga ang data:</p>
                    <p>${escapeHtml(err.message)}</p>
                </div>
            `;
        }
    }

    function toggleChatModal() {
        const chatModal = document.getElementById('chatModal');
        chatModal.classList.toggle('hidden');
        chatModal.classList.toggle('flex');
    }

    function sendChatMessage() {
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if (!text) return;

        const messagesContainer = document.getElementById('chatMessages');
        messagesContainer.innerHTML += `
            <div class="bg-blue-600 text-white p-3 rounded-xl ml-auto max-w-[80%] shadow-sm">
                ${escapeHtml(text)}
            </div>
        `;
        input.value = '';
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function openImageModal(url) {
        document.getElementById('modalImage').src = url;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    function formatDate(isoString) {
        if (!isoString) return '';
        const date = new Date(isoString);
        return date.toLocaleDateString('en-US', {
            month: 'short', day: 'numeric', year: 'numeric'
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[m]));
    }