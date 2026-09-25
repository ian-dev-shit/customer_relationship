let currentPage = 1;
        let searchTimeout = null;

        async function fetchQuotations() {
            const search = document.getElementById('searchInput').value;
            const tableBody = document.getElementById('quotationTableBody');
            
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-8 text-gray-400">Loading documents...</td></tr>`;

            try {
               const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/sales/quotations/?page=${currentPage}&limit=10&search=${encodeURIComponent(search)}`);
                const result = await response.json();

                if (!result.success || result.data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-8 text-gray-400">No quotations found.</td></tr>`;
                    document.getElementById('paginationInfo').innerText = "Showing 0 of 0";
                    return;
                }

                tableBody.innerHTML = result.data.map(item => {
                    const createdDate = item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A';
                    const validDate = item.valid_until ? new Date(item.valid_until).toLocaleDateString() : 'N/A';

                    // Status Badge Mapping
                    let badgeClass = "bg-gray-100 text-gray-700";
                    let statusLabel = item.status.toUpperCase();

                    if (item.status === 'sent' && item.is_valid) {
                        badgeClass = "bg-green-100 text-green-700 border border-green-200";
                        statusLabel = "VALID";
                    } else if (!item.is_valid || item.status === 'expired') {
                        badgeClass = "bg-red-100 text-red-700 border border-red-200";
                        statusLabel = "EXPIRED";
                    }

                    return `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-semibold text-blue-600">${item.quote_number}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">${item.company_name}</div>
                                <div class="text-xs text-gray-400">${item.contact_person}</div>
                            </td>
                            <td class="py-3 px-4 text-gray-600">${item.service_type}</td>
                            <td class="py-3 px-4 font-semibold">₱${item.total_amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td class="py-3 px-4 text-gray-500 text-xs">${createdDate}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full ${badgeClass}">
                                    ${statusLabel}
                                </span>
                                <div class="text-[10px] text-gray-400 mt-1">Until: ${validDate}</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button onclick="openPdfModal('${item.pdf_url}', '${item.quote_number}')" 
                                        class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-lg font-medium transition">
                                    View PDF
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                // Update Pagination Info
                const p = result.pagination;
                document.getElementById('paginationInfo').innerText = `Page ${p.page} of ${p.total_pages} (${p.total} total items)`;
                document.getElementById('prevBtn').disabled = p.page <= 1;
                document.getElementById('nextBtn').disabled = p.page >= p.total_pages;

            } catch (error) {
                console.error("Error fetching quotations:", error);
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-8 text-red-500">Failed to load quotations.</td></tr>`;
            }
        }

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                fetchQuotations();
            }, 300);
        }

        function changePage(step) {
            currentPage += step;
            fetchQuotations();
        }

        function openPdfModal(pdfUrl, quoteNumber) {
            if (!pdfUrl) {
                alert("PDF file not found.");
                return;
            }
            document.getElementById('modalTitle').innerText = `Quotation PDF: ${quoteNumber}`;
            document.getElementById('pdfFrame').src = pdfUrl;
            document.getElementById('downloadPdfBtn').href = pdfUrl;
            document.getElementById('pdfModal').classList.remove('hidden');
        }

        function closePdfModal() {
            document.getElementById('pdfModal').classList.add('hidden');
            document.getElementById('pdfFrame').src = '';
        }

        // Initial Load
        document.addEventListener('DOMContentLoaded', fetchQuotations);