
<div id="quotationModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-100">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-slate-900 text-white flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold">Generate Freight Quotation</h3>
                <p class="text-xs text-slate-400">Fill out details and item charges to send via email</p>
            </div>
            <button onclick="closeQuotationModal()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Modal Body / Form -->
        <form id="quotationForm" onsubmit="handleSendQuotation(event)" class="p-6 overflow-y-auto space-y-6 flex-1">
            
            <!-- Hidden Inquiry ID -->
            <input type="hidden" id="quote_inquiry_id" name="inquiry_id">

            <!-- Customer & Route Summary (Pre-filled Readonly) -->
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase">Customer Name</label>
                    <input type="text" id="quote_customer_name" readonly class="w-full bg-transparent font-medium text-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase">Email Address</label>
                    <input type="email" id="quote_customer_email" readonly class="w-full bg-transparent font-medium text-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase">Origin ➔ Destination</label>
                    <div class="font-medium text-slate-800"><span id="quote_origin"></span> ➔ <span id="quote_destination"></span></div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase">Service Type</label>
                    <span id="quote_service_type" class="inline-block px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded mt-1"></span>
                </div>
            </div>

            <!-- Additional Quote Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Company Name</label>
                    <input type="text" id="quote_company_name" placeholder="Optional" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Valid Until</label>
                    <input type="date" id="quote_valid_until" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <!-- Dynamic Line Items Section -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="text-xs font-bold text-slate-700 uppercase">Particulars / Charges Breakdown</label>
                    <button type="button" onclick="addQuotationRow()" class="px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-bold rounded-md transition-colors flex items-center gap-1">
                        + Add Charge Item
                    </button>
                </div>

                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-100 text-slate-600 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="p-2.5">Description</th>
                                <th class="p-2.5 w-20 text-center">Qty</th>
                                <th class="p-2.5 w-32 text-right">Unit Price (PHP)</th>
                                <th class="p-2.5 w-32 text-right">Total</th>
                                <th class="p-2.5 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="quotationItemsContainer" class="divide-y divide-slate-200">
                            <!-- Rows dynamically added here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Financial Summary Box -->
            <div class="flex justify-end">
                <div class="w-full md:w-64 space-y-2 bg-slate-50 p-3 rounded-lg border border-slate-200 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span id="quote_subtotal_text" class="font-semibold">₱0.00</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Tax / VAT:</span>
                        <input type="number" id="quote_tax_amount" value="0" min="0" oninput="calculateQuoteTotals()" class="w-20 text-right px-1.5 py-0.5 border border-slate-300 rounded outline-none">
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Discount:</span>
                        <input type="number" id="quote_discount_amount" value="0" min="0" oninput="calculateQuoteTotals()" class="w-20 text-right px-1.5 py-0.5 border border-slate-300 rounded outline-none">
                    </div>
                    <div class="flex justify-between text-sm font-bold text-blue-900 border-t border-slate-300 pt-2">
                        <span>Grand Total:</span>
                        <span id="quote_grand_total_text">₱0.00</span>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
                <button type="button" onclick="closeQuotationModal()" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" id="btnSubmitQuotation" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-md flex items-center gap-2">
                    <span>Send Official Quotation</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script src="../../../../assets/js/sales_agent/quote_modal.js"></script>