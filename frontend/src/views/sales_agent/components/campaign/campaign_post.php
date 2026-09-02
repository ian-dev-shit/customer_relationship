<!-- CAMPAIGN & EVENT MANAGEMENT PAGE (INLINE FORM) -->
<div class="space-y-6">

  <!-- TOP HEADER BANNER -->
  <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-slate-800 tracking-tight">Campaign & Event Manager</h2>
      <p class="text-xs text-slate-500 mt-1">Create promo announcements, posters, and limited-time banners for the Customer Dashboard.</p>
    </div>
    <div class="flex items-center gap-2">
      <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        Live Feed Sync Active
      </span>
    </div>
  </div>

  <!-- MAIN 2-COLUMN CONTENT: FORM (LEFT) & ACTIVE CAMPAIGNS FEED (RIGHT) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- LEFT COLUMN: CREATE CAMPAIGN FORM (5 COLUMNS) -->
    <div class="lg:col-span-5 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm space-y-5">
      <div class="pb-4 border-b border-slate-100">
        <h3 class="text-base font-bold text-slate-800">Publish New Poster</h3>
        <p class="text-[11px] text-slate-400">Fill in the details to deploy to client dashboards</p>
      </div>

      <form id="campaignForm" class="space-y-4" enctype="multipart/form-data">
        
        <!-- Dropzone Poster Image Upload -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Poster Design / Image</label>
          <div id="imageDropzone" class="border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-2xl p-4 text-center cursor-pointer transition-colors bg-slate-50 relative">
            <input type="file" id="posterImageInput" name="image" accept="image/*" class="hidden" required />
            <div id="uploadPlaceholder" class="space-y-1">
              <svg class="w-8 h-8 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <p class="text-xs font-semibold text-slate-600">Click to upload poster</p>
              <p class="text-[10px] text-slate-400">PNG, JPG, or WEBP up to 5MB</p>
            </div>
            <img id="imagePreview" class="hidden w-full h-44 object-cover rounded-xl" alt="Preview" />
          </div>
        </div>

        <!-- Campaign Title -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Campaign Title</label>
          <input type="text" name="title" required placeholder="e.g. Free Tumbler Promo on All Shipments" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
        </div>

        <!-- Description -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Promo Mechanics / Description</label>
          <textarea name="description" rows="3" placeholder="Write full offer details or mechanic rules here..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"></textarea>
        </div>

        <!-- Post Duration Configuration -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <span class="text-xs font-bold text-slate-800">Permanent Campaign</span>
              <p class="text-[10px] text-slate-400">No expiration time. Visible until manually deleted.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" id="isPermanentToggle" name="is_permanent" value="true" class="sr-only peer">
              <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
          </div>

          <!-- Date Selector (Toggled off if Permanent) -->
          <div id="dateRangeContainer" class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-200">
            <div>
              <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Start Date</label>
              <input type="datetime-local" id="startDateInput" name="start_date" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>
            <div>
              <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Expiration Date</label>
              <input type="datetime-local" id="endDateInput" name="end_date" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="submitBtn" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs transition-all shadow-md shadow-indigo-100 flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
          </svg>
          Publish to Client Dashboard
        </button>

      </form>
    </div>

    <!-- RIGHT COLUMN: ACTIVE CAMPAIGNS FEED (7 COLUMNS) -->
    <div class="lg:col-span-7 space-y-4">
      <div class="flex items-center justify-between px-1">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Active Customer Banners</h3>
        <span id="campaignCountBadge" class="text-xs font-semibold text-slate-500 bg-slate-200/60 px-2.5 py-0.5 rounded-full">0 Active</span>
      </div>

      <!-- CARDS FEED CONTAINER -->
      <div id="campaignGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Dynamic Cards Loaded Here -->
        <div class="col-span-full py-12 text-center text-slate-400 text-sm bg-white rounded-[2rem] border border-slate-100">
          Loading campaign posts...
        </div>
      </div>
    </div>

  </div>

</div>