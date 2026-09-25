
    document.addEventListener("DOMContentLoaded", () => {
        loadDashboardData();
    });

    async function loadDashboardData() {
        try {
            const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/super_admin/dashboard/stats`);
            const data = await response.json();

            if (!response.ok) throw new Error(data.detail || "Failed to load dashboard data.");

            // 1. Update Stat Cards
            document.getElementById("statRestrictedCount").innerText = data.restricted_count;
            document.getElementById("statLoginCount").innerText = data.today_logins;
            document.getElementById("statAuditCount").innerText = data.audit_count;

            // 2. Render Recent Restricted Accounts Table
            const restrictedBody = document.getElementById("dashRestrictedBody");
            if (data.recent_restricted.length === 0) {
                restrictedBody.innerHTML = `<tr><td colspan="3" class="p-4 text-center text-slate-400">No restricted accounts found.</td></tr>`;
            } else {
                restrictedBody.innerHTML = data.recent_restricted.map(user => `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3 font-semibold text-slate-800">${escapeHtml(user.email)}</td>
                        <td class="p-3 text-center font-bold text-red-600">${user.failed_attempts} / 5</td>
                        <td class="p-3 text-center">
                            <a href="restriction.php" class="text-blue-600 font-semibold hover:underline">Manage</a>
                        </td>
                    </tr>
                `).join('');
            }

            // 3. Render Recent Audit Activity List
            const auditList = document.getElementById("dashAuditList");
            if (data.recent_audits.length === 0) {
                auditList.innerHTML = `<p class="text-xs text-slate-400 text-center py-4">No recent activity logs.</p>`;
            } else {
                auditList.innerHTML = data.recent_audits.map(log => `
                    <div class="flex items-start gap-3 text-xs border-b border-slate-100 pb-2.5 last:border-0">
                        <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-800 truncate">${escapeHtml(log.user_email)}</p>
                            <p class="text-slate-500 text-[11px] truncate">${escapeHtml(log.action)} - <span class="text-slate-400">${escapeHtml(log.module)}</span></p>
                        </div>
                    </div>
                `).join('');
            }

        } catch (err) {
            console.error("Dashboard Error:", err);
        }
    }

    function escapeHtml(str) {
        return (str || '').replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[m]));
    }