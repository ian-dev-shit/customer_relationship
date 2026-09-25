

    document.addEventListener("DOMContentLoaded", () => {
        fetchRestrictedUsers();
    });

    // 1. Fetch Restricted Users list mula sa FastAPI
    async function fetchRestrictedUsers() {
        const tableBody = document.getElementById("restrictedTableBody");
        const isOnlyRestricted = document.getElementById("onlyRestrictedToggle").checked;

        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-10 text-slate-400">
                    <i class="fa-solid fa-circle-notch fa-spin text-xl mb-2"></i>
                    <p>Loading accounts...</p>
                </td>
            </tr>`;

        try {
            const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/super_admin/restrictions/restricted-users?only_restricted=${isOnlyRestricted}`);
            const data = await response.json();

            if (!response.ok) throw new Error(data.detail || "Failed to fetch restriction list.");

            if (data.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400">
                            <i class="fa-solid fa-shield-check text-2xl text-emerald-500/50 mb-2"></i>
                            <p>No restricted user records found.</p>
                        </td>
                    </tr>`;
                return;
            }

            tableBody.innerHTML = data.map(user => {
                const isRestricted = user.is_restricted;
                const statusBadge = isRestricted 
                    ? `<span class="bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-flex items-center gap-1.5"><i class="fa-solid fa-lock text-[9px]"></i> Restricted</span>`
                    : `<span class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-flex items-center gap-1.5"><i class="fa-solid fa-unlock text-[9px]"></i> Active</span>`;

                const lastAttempt = user.last_attempt_at ? formatDate(user.last_attempt_at) : 'N/A';
                const restrictedUntil = user.restricted_until ? formatDate(user.restricted_until) : 'Indefinite / Permanent';

                return `
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4 font-semibold text-slate-800">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-envelope text-slate-400 text-xs"></i>
                                ${escapeHtml(user.email)}
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <span class="font-mono font-bold px-2 py-1 bg-slate-100 rounded-lg border border-slate-200 ${user.failed_attempts >= 5 ? 'text-red-600 border-red-200 bg-red-50' : 'text-amber-600'}">
                                ${user.failed_attempts} / 5
                            </span>
                        </td>
                        <td class="p-4">${statusBadge}</td>
                        <td class="p-4 text-slate-500 text-[11px]">${lastAttempt}</td>
                        <td class="p-4 text-slate-500 text-[11px]">${isRestricted ? restrictedUntil : '-'}</td>
                        <td class="p-4 text-center">
                            ${isRestricted || user.failed_attempts > 0 ? `
                                <button onclick="unrestrictUser('${escapeHtml(user.email)}')" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-xl transition text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-lock-open"></i> Unlock Account
                                </button>
                            ` : `
                                <span class="text-slate-400 text-[11px]">No Action Needed</span>
                            `}
                        </td>
                    </tr>
                `;
            }).join('');

        } catch (err) {
            showAlert(err.message, "error");
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-red-500">${err.message}</td></tr>`;
        }
    }

    // 2. Unlock / Unrestrict User
    async function unrestrictUser(email) {
        if (!confirm(`Are you sure you want to unlock and reset login attempts for ${email}?`)) {
            return;
        }

        try {
            const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/super_admin/restrictions/unrestrict-user?email=${encodeURIComponent(email)}`, {
                method: "POST"
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.detail || "Failed to unrestrict user.");

            showAlert(data.message || `Account ${email} has been unlocked!`, "success");
            fetchRestrictedUsers();

        } catch (err) {
            showAlert(err.message, "error");
        }
    }

    // Helper Functions
    function formatDate(isoString) {
        if (!isoString) return '-';
        const date = new Date(isoString);
        return date.toLocaleString('en-US', { 
            month: 'short', day: 'numeric', year: 'numeric', 
            hour: '2-digit', minute: '2-digit', hour12: true 
        });
    }

    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[m]));
    }

    function showAlert(msg, type) {
        const box = document.getElementById("alertBox");
        box.classList.remove("hidden", "bg-red-50", "text-red-700", "border-red-200", "bg-emerald-50", "text-emerald-700", "border-emerald-200");
        
        if (type === "error") {
            box.classList.add("bg-red-50", "text-red-700", "border", "border-red-200");
        } else {
            box.classList.add("bg-emerald-50", "text-emerald-700", "border", "border-emerald-200");
        }
        box.innerText = msg;

        setTimeout(() => {
            box.classList.add("hidden");
        }, 5000);
    }