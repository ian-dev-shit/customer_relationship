 

    document.addEventListener("DOMContentLoaded", () => {
        fetchAuditLogs();
    });

    async function fetchAuditLogs() {
        const tableBody = document.getElementById("auditTableBody");
        const roleFilter = document.getElementById("roleFilter").value;

        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-10 text-slate-400">
                    <i class="fa-solid fa-circle-notch fa-spin text-xl mb-2"></i>
                    <p>Loading audit logs...</p>
                </td>
            </tr>`;

        try {
            let url = `${window.APP_CONFIG.API_BASE_URL}/api/v1/super_admin/audit/logs`;
            if (roleFilter) url += `?role_filter=${roleFilter}`;

            const response = await fetch(url);
            const logs = await response.json();

            if (!response.ok) throw new Error(logs.detail || "Failed to fetch audit logs.");

            if (logs.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400">
                            No logs found for the selected criteria.
                        </td>
                    </tr>`;
                return;
            }

            tableBody.innerHTML = logs.map(log => {
                const badgeColor = {
                    'super_admin': 'bg-purple-50 text-purple-700 border-purple-200',
                    'admin': 'bg-blue-50 text-blue-700 border-blue-200',
                    'sales_agent': 'bg-amber-50 text-amber-700 border-amber-200',
                    'customer': 'bg-emerald-50 text-emerald-700 border-emerald-200'
                }[log.user_role] || 'bg-slate-50 text-slate-700 border-slate-200';

                return `
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4 text-slate-500 font-mono text-[11px]">${formatDate(log.created_at)}</td>
                        <td class="p-4 font-semibold text-slate-800">${escapeHtml(log.user_email)}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border ${badgeColor}">
                                ${log.user_role}
                            </span>
                        </td>
                        <td class="p-4 font-bold text-slate-700">${escapeHtml(log.action)}</td>
                        <td class="p-4 text-slate-500">${escapeHtml(log.module)}</td>
                        <td class="p-4 text-slate-600 max-w-xs truncate">${escapeHtml(log.details || '-')}</td>
                    </tr>
                `;
            }).join('');

        } catch (err) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-red-500">${err.message}</td></tr>`;
        }
    }

    function formatDate(isoString) {
        if (!isoString) return '-';
        return new Date(isoString).toLocaleString('en-US', {
            month: 'short', day: 'numeric', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[m]));
    }
