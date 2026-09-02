<!-- UNIFIED LEAD DETAILS, CONTACT & STATUS MODAL (Redesigned) -->
<div id="viewModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300">
  <!-- Backdrop -->
  <div class="absolute inset-0 bg-slate-900/55 backdrop-blur-sm" onclick="closeViewModal()"></div>

  <!-- Main Panel -->
  <div class="lm-panel relative flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl shadow-indigo-900/20 ring-1 ring-slate-900/5">

    <!-- HEADER BANNER -->
    <div class="relative overflow-hidden bg-gradient-to-br from-violet-600 via-indigo-600 to-indigo-700 px-6 py-5 sm:px-8">
      <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10"></div>
      <div class="pointer-events-none absolute -bottom-12 -left-8 h-40 w-40 rounded-full bg-white/5"></div>

      <div class="relative flex items-start gap-4">
        <!-- Generated initials avatar -->
        <div id="modalAvatar" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-base font-extrabold text-white ring-1 ring-white/30 backdrop-blur">
          SF
        </div>
        <div class="min-w-0 flex-1 pr-10">
          <h3 class="truncate text-lg font-bold text-white" id="modalCompany">Company Name</h3>
          <p class="mt-0.5 text-xs font-medium text-indigo-100" id="modalCode">INQ-CODE</p>
        </div>
        <span id="modalStatusBadge" class="hidden shrink-0 rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wide ring-1 ring-white/30 bg-white/15 text-white">Status</span>
      </div>

      <button type="button" onclick="closeViewModal()" class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full bg-white/15 text-white ring-1 ring-white/30 transition hover:bg-white hover:text-indigo-700">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- PIPELINE STEPPER -->
    <div class="border-b border-slate-100 bg-slate-50/60 px-6 py-3 sm:px-8">
      <div class="flex items-start justify-between gap-1">
        <div class="lm-step" data-stage="new_inquiry">
          <div class="lm-dot mx-auto"><i class="fa-solid fa-seedling"></i></div>
          <div class="lm-label mt-1 text-center font-semibold text-slate-500">New</div>
        </div>
        <div class="lm-step" data-stage="qualifying">
          <div class="lm-dot mx-auto"><i class="fa-solid fa-magnifying-glass"></i></div>
          <div class="lm-label mt-1 text-center font-semibold text-slate-500">Qualify</div>
        </div>
        <div class="lm-step" data-stage="quote_sent">
          <div class="lm-dot mx-auto"><i class="fa-solid fa-file-invoice-dollar"></i></div>
          <div class="lm-label mt-1 text-center font-semibold text-slate-500">Quote</div>
        </div>
        <div class="lm-step" data-stage="negotiation">
          <div class="lm-dot mx-auto"><i class="fa-solid fa-handshake"></i></div>
          <div class="lm-label mt-1 text-center font-semibold text-slate-500">Negotiate</div>
        </div>
        <div class="lm-step" data-stage="closed_won">
          <div class="lm-dot mx-auto"><i class="fa-solid fa-trophy"></i></div>
          <div class="lm-label mt-1 text-center font-semibold text-slate-500">Won</div>
        </div>
        <div class="lm-step" data-stage="closed_lost">
          <div class="lm-dot mx-auto"><i class="fa-solid fa-xmark"></i></div>
          <div class="lm-label mt-1 text-center font-semibold text-slate-500">Lost</div>
        </div>
      </div>
    </div>

    <!-- BODY -->
    <div class="flex-1 overflow-y-auto px-6 py-6 sm:px-8">
      <form id="statusUpdateForm" onsubmit="handleStatusUpdate(event)">
        <input type="hidden" id="modalLeadId" value="">

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

          <!-- LEFT COLUMN -->
          <div class="space-y-5">
            <div>
              <h4 class="mb-3 flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                <i class="fa-solid fa-circle-info text-indigo-400"></i> Lead &amp; Contact Information
              </h4>

              <!-- Quick glance summary chips -->
              <div class="mb-3 grid grid-cols-3 gap-2">
                <div class="rounded-2xl border border-slate-100 bg-gradient-to-br from-indigo-50/70 to-white p-2.5 text-center">
                  <div class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Service</div>
                  <div id="modalService" class="mt-0.5 truncate text-[11px] font-bold text-slate-700">--</div>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-gradient-to-br from-indigo-50/70 to-white p-2.5 text-center">
                  <div class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Route</div>
                  <div id="modalRoute" class="mt-0.5 truncate text-[11px] font-bold text-slate-700">--</div>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-gradient-to-br from-emerald-50/70 to-white p-2.5 text-center">
                  <div class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Value</div>
                  <div id="modalValueChip" class="mt-0.5 truncate text-[11px] font-bold text-emerald-600">₱0.00</div>
                </div>
              </div>

              <div class="space-y-2.5 rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white px-3 py-2.5 shadow-sm">
                  <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-user text-xs"></i></div>
                  <div class="min-w-0 flex-1">
                    <div class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Contact Person</div>
                    <div class="truncate text-xs font-bold text-slate-800" id="modalContact">--</div>
                  </div>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white px-3 py-2.5 shadow-sm">
                  <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-envelope text-xs"></i></div>
                  <div class="min-w-0 flex-1">
                    <div class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Email Address</div>
                    <div class="truncate text-xs font-bold text-slate-800" id="modalEmail">--</div>
                  </div>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white px-3 py-2.5 shadow-sm">
                  <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-phone text-xs"></i></div>
                  <div class="min-w-0 flex-1">
                    <div class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Phone Number</div>
                    <div class="truncate text-xs font-bold text-slate-800" id="modalPhone">--</div>
                  </div>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white px-3 py-2.5 shadow-sm">
                  <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-globe text-xs"></i></div>
                  <div class="min-w-0 flex-1">
                    <div class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Platform</div>
                    <div class="truncate text-xs font-bold text-slate-800" id="modalPlatform">--</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- DIRECT ACTIONS -->
            <div class="space-y-2">
              <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Direct Actions</label>
              <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                <a id="contactModalEmailBtn" href="#" target="_blank" class="group flex items-center gap-3 rounded-2xl bg-gradient-to-r from-rose-500 to-red-500 px-4 py-3 text-white shadow-md shadow-rose-500/20 transition hover:shadow-lg hover:shadow-rose-500/30 hover:-translate-y-0.5 active:scale-[0.98]">
                  <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L12 9.545l8.073-6.052C21.69 2.28 24 3.434 24 5.457z"/></svg>
                  </div>
                  <div class="min-w-0 flex-1 text-left">
                    <div class="text-xs font-bold">Send Email</div>
                    <div class="truncate text-[11px] text-white/80" id="contactModalEmailText">email@example.com</div>
                  </div>
                  <i class="fa-solid fa-arrow-right text-xs text-white/80 transition group-hover:translate-x-0.5"></i>
                </a>

                <a id="contactModalPhoneBtn" href="#" class="group flex items-center gap-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-green-600 px-4 py-3 text-white shadow-md shadow-emerald-500/20 transition hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5 active:scale-[0.98]">
                  <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                  </div>
                  <div class="min-w-0 flex-1 text-left">
                    <div class="text-xs font-bold">Call Phone</div>
                    <div class="truncate text-[11px] text-white/80" id="contactModalPhoneText">+63 900 000 0000</div>
                  </div>
                  <i class="fa-solid fa-arrow-right text-xs text-white/80 transition group-hover:translate-x-0.5"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN -->
          <div class="space-y-4">
            <h4 class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
              <i class="fa-solid fa-pen-to-square text-indigo-400"></i> Update Lead &amp; Quote
            </h4>

            <div class="space-y-4 rounded-2xl border border-slate-100 bg-gradient-to-br from-indigo-50/60 to-white p-5">
              <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Cargo Details</label>
                <textarea id="modalCargo" name="cargo_details" rows="3" placeholder="Enter cargo details..." class="w-full resize-none rounded-xl border border-slate-200 bg-white p-2.5 text-xs text-slate-700 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
              </div>

              <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Update Status</label>
                <div class="relative">
                  <select id="modalStatusSelect" class="w-full appearance-none rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="new_inquiry">NEW INQUIRY</option>
                    <option value="qualifying">QUALIFYING</option>
                    <option value="quote_sent">QUOTE SENT</option>
                    <option value="negotiation">NEGOTIATION</option>
                    <option value="closed_won">CLOSED WON</option>
                    <option value="closed_lost">CLOSED LOST</option>
                  </select>
                  <i class="fa-solid fa-chevron-down pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400"></i>
                </div>
              </div>

              <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Agreed Price / Quote (₱)</label>
                <input type="number" step="0.01" id="modalPriceInput" placeholder="0.00" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
              </div>

              <div id="pickupFieldsSection" class="hidden space-y-3 border-t border-slate-100 pt-3">
                <div class="flex items-center gap-1.5 text-xs font-bold text-indigo-600">
                  <i class="fa-solid fa-truck-ramp-box"></i> Pickup Details <span class="text-rose-500">*</span>
                </div>
                <div>
                  <label class="mb-1 block text-[11px] font-semibold text-slate-500">Full Pickup Address</label>
                  <textarea id="modalPickupAddress" name="pickup_address" rows="2" placeholder="Enter complete street address, landmark, floor/unit..." class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs text-slate-700 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                  <label class="mb-1 block text-[11px] font-semibold text-slate-500">Pickup Date &amp; Time</label>
                  <input type="datetime-local" id="modalPickupDateTime" name="pickup_datetime" class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs text-slate-700 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
              </div>

              <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 py-3 text-xs font-bold text-white shadow-md shadow-indigo-500/30 transition hover:from-violet-700 hover:to-indigo-700 hover:-translate-y-0.5 active:scale-[0.98]">
                <i class="fa-solid fa-floppy-disk"></i> Save Status Update
              </button>
            </div>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>