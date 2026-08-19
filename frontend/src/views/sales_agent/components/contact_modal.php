<!-- CONTACT OPTIONS MODAL COMPONENT -->
<div id="contactOptionsModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in duration-150">
    
    <!-- CLOSE BUTTON -->
    <button onclick="closeContactModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-lg">
      ✕
    </button>

    <div class="text-center mb-5">
      <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
      </div>
      <h3 class="text-base font-bold text-slate-800" id="contactModalCompany">Company Name</h3>
      <p class="text-xs text-slate-500 mt-0.5" id="contactModalPerson">Contact Person</p>
    </div>

    <!-- CONTACT OPTIONS LIST -->
    <div class="space-y-3">
      
      <!-- GMAIL / EMAIL OPTION -->
      <a id="contactModalEmailBtn" href="#" target="_blank" class="w-full flex items-center justify-between p-3 bg-slate-50 hover:bg-purple-50 hover:border-purple-200 border border-slate-200 rounded-xl transition group">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center group-hover:scale-105 transition-transform">
            <!-- Gmail SVG Icon -->
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L12 9.545l8.073-6.052C21.69 2.28 24 3.434 24 5.457z"/>
            </svg>
          </div>
          <div class="text-left">
            <div class="text-xs font-bold text-slate-800 group-hover:text-purple-700">Send Email</div>
            <div class="text-[11px] text-slate-400" id="contactModalEmailText">email@example.com</div>
          </div>
        </div>
        <span class="text-slate-400 group-hover:text-purple-600 text-xs">➔</span>
      </a>

      <!-- PHONE CALL / DIAL OPTION -->
      <a id="contactModalPhoneBtn" href="#" class="w-full flex items-center justify-between p-3 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 border border-slate-200 rounded-xl transition group">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
            <!-- Phone Call SVG Icon -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
          </div>
          <div class="text-left">
            <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700">Call Phone</div>
            <div class="text-[11px] text-slate-400" id="contactModalPhoneText">+63 900 000 0000</div>
          </div>
        </div>
        <span class="text-slate-400 group-hover:text-emerald-600 text-xs">➔</span>
      </a>

    </div>

  </div>
</div>

<script src="../../../../assets/js/contact_modal.js"></script>