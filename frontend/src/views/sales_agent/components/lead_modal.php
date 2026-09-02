<!-- CREATE NEW LEAD MODAL -->
<div id="newLeadModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
    
    <!-- MODAL HEADER -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-lg font-bold">
          <i class="fa-solid fa-user-plus"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-900">Add New Lead</h3>
          <p class="text-xs text-slate-400">Fill in the primary client details below</p>
        </div>
      </div>
      <button type="button" onclick="closeLeadModal()" class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- MODAL FORM -->
    <form id="createLeadForm" onsubmit="submitNewLead(event)" class="py-5 space-y-4">
      
      <!-- COMPANY NAME -->
      <div>
        <label class="text-[11px] font-bold text-slate-600 block mb-1">Company Name</label>
        <input type="text" name="company_name" required placeholder="e.g. Smartech Solutions" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
      </div>

      <!-- CONTACT PERSON & EMAIL -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[11px] font-bold text-slate-600 block mb-1">Contact Person</label>
          <input type="text" name="contact_person" required placeholder="e.g. Juan Cruz" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
        </div>
        <div>
          <label class="text-[11px] font-bold text-slate-600 block mb-1">Email Address</label>
          <input type="email" name="email" required placeholder="juan@company.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
        </div>
      </div>

      <!-- PHONE NUMBER & SERVICE TYPE -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[11px] font-bold text-slate-600 block mb-1">Phone Number</label>
          <input type="text" name="phone_number" required placeholder="09123456789" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
        </div>
        <div>
          <label class="text-[11px] font-bold text-slate-600 block mb-1">Service Type</label>
          <select name="service_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all bg-white">
            <option value="" disabled selected>Select Service</option>
            <option value="Full Truckload (FTL)">Full Truckload (FTL)</option>
            <option value="Less Than Truckload (LTL)">Less Than Truckload (LTL)</option>
            <option value="Sea Freight">Sea Freight</option>
            <option value="Air Freight">Air Freight</option>
          </select>
        </div>
      </div>

      <!-- ORIGIN & DESTINATION -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[11px] font-bold text-slate-600 block mb-1">Origin</label>
          <input type="text" name="origin" required placeholder="e.g. Manila Port" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
        </div>
        <div>
          <label class="text-[11px] font-bold text-slate-600 block mb-1">Destination</label>
          <input type="text" name="destination" required placeholder="e.g. Cebu City" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
        </div>
      </div>

      <!-- MODAL FOOTER -->
      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" onclick="closeLeadModal()" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">
          Cancel
        </button>
        <button type="submit" id="submitLeadBtn" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all active:scale-95 flex items-center gap-2">
          <span>Save Lead</span>
        </button>
      </div>

    </form>

  </div>
</div>