<?php
$pageTitle  = "Quotation Documents";

include_once '../../includes/header.php';

?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->

<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">
    <!-- TOP HEADER & NAVBAR -->
  <?php include_once '../../components/top_header.php'; ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sent Quotations & Documents</h1>
                <p class="text-sm text-gray-500">Track and view generated PDF quotations sent to clients.</p>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="relative w-full sm:w-96">
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search Quote #, Company, Client..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onkeyup="debounceSearch()"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            <button onclick="fetchQuotations()" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-sm font-medium transition">
                Refresh
            </button>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Quote Number</th>
                            <th class="py-3 px-4">Client / Company</th>
                            <th class="py-3 px-4">Service</th>
                            <th class="py-3 px-4">Total Amount</th>
                            <th class="py-3 px-4">Issued Date</th>
                            <th class="py-3 px-4">Validity</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="quotationTableBody" class="divide-y divide-gray-100">
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">Loading quotations...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-600">
                <span id="paginationInfo">Showing 0 of 0</span>
                <div class="flex gap-2">
                    <button id="prevBtn" onclick="changePage(-1)" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50">Previous</button>
                    <button id="nextBtn" onclick="changePage(1)" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50">Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Viewer Modal -->
    <div id="pdfModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-5xl h-[90vh] rounded-xl flex flex-col overflow-hidden shadow-2xl">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h3 id="modalTitle" class="font-bold text-gray-800">Quotation Preview</h3>
                <div class="flex items-center gap-3">
                    <a id="downloadPdfBtn" href="#" target="_blank" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700">
                        Download PDF
                    </a>
                    <button onclick="closePdfModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold px-2">&times;</button>
                </div>
            </div>
            <div class="flex-1 bg-gray-100">
                <iframe id="pdfFrame" class="w-full h-full border-0" src=""></iframe>
            </div>
        </div>
    </div>
</main>

    
<script src="../../../assets/js/sales_agent/document.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
