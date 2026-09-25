<!-- RIGHT SIDEBAR / DRAWER FOR LEAD DETAILS -->
<div id="viewModal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
  <!-- Backdrop -->
  <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" onclick="closeViewModal()"></div>

  <!-- Sidebar Container -->
  <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
    <div class="w-screen max-w-md transform bg-white shadow-2xl transition-transform duration-300 ease-in-out border-l border-slate-100 flex flex-col">
      
      <!-- HEADER -->
      <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
        <div class="flex items-center gap-3">
          <div id="modalAvatar" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white shadow-sm">
            SF
          </div>
          <div class="min-w-0">
            <h3 class="truncate text-base font-bold text-slate-800" id="modalCompany">Company Name</h3>
            <p class="text-xs text-slate-400 font-mono" id="modalCode">INQ-CODE</p>
          </div>
        </div>
        <button type="button" onclick="closeViewModal()" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <!-- PIPELINE STEPPER (Compact) -->
      <div class="border-b border-slate-100 bg-white px-6 py-3">
        <div class="flex items-center justify-between gap-1">
          <div class="lm-step text-center" data-stage="new_inquiry">
            <div class="lm-dot mx-auto text-xs"><i class="fa-solid fa-seedling"></i></div>
            <div class="lm-label mt-1 text-[10px] font-medium text-slate-400">New</div>
          </div>
          <div class="lm-step text-center" data-stage="qualifying">
            <div class="lm-dot mx-auto text-xs"><i class="fa-solid fa-magnifying-glass"></i></div>
            <div class="lm-label mt-1 text-[10px] font-medium text-slate-400">Qualify</div>
          </div>
          <div class="lm-step text-center" data-stage="quote_sent">
            <div class="lm-dot mx-auto text-xs"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div class="lm-label mt-1 text-[10px] font-medium text-slate-400">Quote</div>
          </div>
          <div class="lm-step text-center" data-stage="negotiation">
            <div class="lm-dot mx-auto text-xs"><i class="fa-solid fa-handshake"></i></div>
            <div class="lm-label mt-1 text-[10px] font-medium text-slate-400">Negotiate</div>
          </div>
          <div class="lm-step text-center" data-stage="closed_won">
            <div class="lm-dot mx-auto text-xs"><i class="fa-solid fa-trophy"></i></div>
            <div class="lm-label mt-1 text-[10px] font-medium text-slate-400">Won</div>
          </div>
          <div class="lm-step text-center" data-stage="closed_lost">
            <div class="lm-dot mx-auto text-xs"><i class="fa-solid fa-xmark"></i></div>
            <div class="lm-label mt-1 text-[10px] font-medium text-slate-400">Lost</div>
          </div>
        </div>
      </div>

      <!-- BODY (SCROLLABLE) -->
      <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">
        
        <!-- SUMMARY CHIPS -->
        <div class="grid grid-cols-4 gap-2">
          <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-2.5 text-center">
            <span class="block text-[9px] font-semibold tracking-wider text-slate-400 uppercase">Service</span>
            <span id="modalService" class="block mt-0.5 truncate text-xs font-semibold text-slate-700">--</span>
          </div>
          <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-2.5 text-center">
            <span class="block text-[9px] font-semibold tracking-wider text-slate-400 uppercase">Route</span>
            <span id="modalRoute" class="block mt-0.5 truncate text-xs font-semibold text-slate-700">--</span>
          </div>
          <div class="rounded-xl border border-emerald-100 bg-emerald-50/30 p-2.5 text-center">
            <span class="block text-[9px] font-semibold tracking-wider text-emerald-600/70 uppercase">Value</span>
            <span id="modalValueChip" class="block mt-0.5 truncate text-xs font-bold text-emerald-600">₱0.00</span>
          </div>
        </div>

        <!-- CONTACT DETAILS -->
        <div class="space-y-2 rounded-2xl border border-slate-100 bg-slate-50/30 p-3.5 text-xs">
          <div class="flex items-center justify-between text-slate-600 py-1">
            <span class="text-slate-400">Contact Person</span>
            <span class="font-semibold text-slate-800" id="modalContact">--</span>
          </div>
          <div class="flex items-center justify-between text-slate-600 py-1 border-t border-slate-100">
            <span class="text-slate-400">Email</span>
            <span class="font-semibold text-slate-800 truncate max-w-[200px]" id="modalEmail">--</span>
          </div>
          <div class="flex items-center justify-between text-slate-600 py-1 border-t border-slate-100">
            <span class="text-slate-400">Phone</span>
            <span class="font-semibold text-slate-800" id="modalPhone">--</span>
          </div>
          <div class="flex items-center justify-between text-slate-600 py-1 border-t border-slate-100">
            <span class="text-slate-400">Platform</span>
            <span class="font-semibold text-slate-800" id="modalPlatform">--</span>
          </div>
          <div class="flex items-center justify-between text-slate-600 py-1 border-t border-slate-100">
            <span class="text-slate-400">Handling Type</span>
            <span class="font-semibold text-slate-800" id="modalHandling">--</span>
          </div>
          <div class="flex items-center justify-between text-slate-600 py-1 border-t border-slate-100">
            <span class="text-slate-400">Service</span>
            <span class="font-semibold text-slate-800" id="modalServices">--</span>
          </div> 
        </div>

        <!-- DIRECT ACTIONS WITH SEND QUOTE BUTTON -->
        <div class="space-y-2">
          <div class="grid grid-cols-2 gap-2">
            <a id="contactModalEmailBtn" href="#" target="_blank" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 py-2 px-3 text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
              <i class="fa-solid fa-envelope text-indigo-500"></i> Email Client
            </a>
            <a id="contactModalPhoneBtn" href="#" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 py-2 px-3 text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
              <i class="fa-solid fa-phone text-emerald-500"></i> Call Client
            </a>
          </div>
        </div>

        <hr class="border-slate-100">

        <!-- UPDATE STATUS & DETAILS -->
        <form id="statusUpdateForm" onsubmit="handleStatusUpdate(event)" class="space-y-4">
          <input type="hidden" id="modalLeadId" value="">

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
            <select id="modalStatusSelect" 
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="new_inquiry">NEW INQUIRY</option>
              <option value="qualifying">QUALIFYING</option>
              <option value="quote_sent">QUOTE SENT</option>
              <option value="negotiation">NEGOTIATION</option>
              <option value="closed_won">CLOSED WON</option>
              <option value="closed_lost">CLOSED LOST</option>
            </select>
          </div>
        
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Agreed Price / Quote (₱)</label>
            <input type="number" step="0.01" id="modalPriceInput" placeholder="0.00" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Cargo Details</label>
            <textarea id="modalCargo" name="cargo_details" rows="2" placeholder="Cargo description..." class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
          </div>

          <!-- PICKUP DETAILS -->
          <div class="space-y-3 rounded-2xl bg-indigo-50/40 p-3.5 border border-indigo-100/50">
            <span class="block text-xs font-bold text-indigo-900"><i class="fa-solid fa-truck-ramp-box mr-1"></i> Pickup Details</span>
            <div>
              <label class="block text-[11px] font-medium text-slate-500 mb-1">Pickup Address</label>
              <textarea id="modalPickupAddress" name="pickup_address" rows="2" placeholder="Enter complete pickup address..." class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div>
              <label class="block text-[11px] font-medium text-slate-500 mb-1">Pickup Date & Time</label>
              <input type="datetime-local" id="modalPickupDateTime" name="pickup_datetime" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>

          <!-- NOTES FIELD -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Agent Notes / Remarks</label>
            <textarea id="modalNotes" name="notes" rows="3" placeholder="Reason for deal update, win/loss remarks..." class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
          </div>

          <button type="submit" class="w-full rounded-xl bg-indigo-600 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            Save Lead Changes
          </button>
        </form>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>