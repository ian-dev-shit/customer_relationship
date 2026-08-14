<?php
$page_title = "Documents · Rising Red Dragon";

include_once '../../includes/header.php';
?>

<div class="app-container">

  <!-- SIDEBAR INCLUDE -->
  <?php include_once '../../includes/sidebar.php'; ?>

  <!-- MAIN CONTENT – DOCUMENTS DASHBOARD (Pure Static) -->
  <main class="main-content mesh-bg relative overflow-y-auto">

    <!-- Mobile toggle button -->
    <button onclick="toggleSidebar()" class="mobile-toggle fixed top-4 left-4 z-30 p-2 rounded-lg bg-slate-800/80 backdrop-blur border border-slate-700 text-slate-300 hover:text-white transition" aria-label="Open sidebar">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
      </svg>
    </button>

    <div class="max-w-7xl mx-auto fade-in">

      <!-- PAGE HEADER -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-tight">Documents</h1>
          <p class="text-sm text-slate-400 mt-0.5">Documents Management - uploads &amp; approved</p>
        </div>
        <!-- Session time -->
        <div class="text-right">
          <p class="text-xs text-slate-500">Session Active</p>
          <p class="text-sm font-mono text-sky-400 font-semibold" id="sessionTime">5:38:45 PM</p>
        </div>
      </div>

      <!-- TWO-COLUMN LAYOUT -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN (2/3) – MY DOCUMENTS TABLE -->
        <div class="lg:col-span-2 space-y-6">

          <div class="glass-card rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">My Documents</h2>
              <span class="text-xs text-slate-500">4 files</span>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="text-left text-xs text-slate-500 border-b border-slate-700/50">
                    <th class="pb-2 font-medium">TYPE</th>
                    <th class="pb-2 font-medium">NAME</th>
                    <th class="pb-2 font-medium">DATE</th>
                    <th class="pb-2 font-medium">STATUS</th>
                    <th class="pb-2 font-medium text-center">ACTION</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/30">
                  <!-- Row 1 – Approved -->
                  <tr class="hover:bg-white/5 transition">
                    <td class="py-3">
                      <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-800 text-rose-400 text-xs font-bold">PDF</span>
                    </td>
                    <td class="py-3 text-slate-300">Service-Contract2026.pdf</td>
                    <td class="py-3 text-slate-400 text-xs">8/8/2026</td>
                    <td class="py-3"><span class="status-badge status-approved">Approved</span></td>
                    <td class="py-3 text-center">
                      <button class="download-btn text-slate-400 hover:text-sky-400 transition" title="Download">
                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                  <!-- Row 2 – Pending Review -->
                  <tr class="hover:bg-white/5 transition">
                    <td class="py-3">
                      <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-800 text-rose-400 text-xs font-bold">PDF</span>
                    </td>
                    <td class="py-3 text-slate-300">Invoice-INV2026.pdf</td>
                    <td class="py-3 text-slate-400 text-xs">Jan 08, 2026</td>
                    <td class="py-3"><span class="status-badge status-pending">Pending Review</span></td>
                    <td class="py-3 text-center">
                      <button class="download-btn text-slate-400 hover:text-sky-400 transition" title="Download">
                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                  <!-- Row 3 – Approved -->
                  <tr class="hover:bg-white/5 transition">
                    <td class="py-3">
                      <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-800 text-rose-400 text-xs font-bold">PDF</span>
                    </td>
                    <td class="py-3 text-slate-300">POD-WB2026.pdf</td>
                    <td class="py-3 text-slate-400 text-xs">Jan 08, 2026</td>
                    <td class="py-3"><span class="status-badge status-approved">Approved</span></td>
                    <td class="py-3 text-center">
                      <button class="download-btn text-slate-400 hover:text-sky-400 transition" title="Download">
                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                  <!-- Row 4 – Rejected -->
                  <tr class="hover:bg-white/5 transition">
                    <td class="py-3">
                      <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-800 text-rose-400 text-xs font-bold">PDF</span>
                    </td>
                    <td class="py-3 text-slate-300">Insurance-Cert2026.pdf</td>
                    <td class="py-3 text-slate-400 text-xs">Jan 08, 2026</td>
                    <td class="py-3"><span class="status-badge status-rejected">Rejected - Expired Scan</span></td>
                    <td class="py-3 text-center">
                      <button class="download-btn text-slate-400 hover:text-sky-400 transition" title="Download">
                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>

        <!-- RIGHT COLUMN (1/3) – UPLOAD FORM -->
        <div class="space-y-6">

          <div class="glass-card rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Upload New Documents</h2>

            <form class="space-y-4">
              <!-- Document Type -->
              <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Document Type</label>
                <select class="w-full px-3 py-2 text-sm rounded-xl bg-slate-800/60 border border-slate-700/50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent text-slate-200 transition">
                  <option value="contract">Contract</option>
                  <option value="invoice">Invoice</option>
                  <option value="bill_of_lading">Bill of Lading</option>
                  <option value="certificate">Certificate</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <!-- File Input -->
              <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">File</label>
                <div class="flex items-center gap-3">
                  <div class="file-input-wrapper flex-1">
                    <button type="button" class="w-full px-4 py-2 text-sm rounded-xl bg-slate-800/60 border border-slate-700/50 hover:bg-slate-700/60 text-slate-300 transition text-left">
                      Choose File
                    </button>
                    <input type="file" id="fileInput" />
                  </div>
                </div>
                <p class="text-xs text-slate-500 mt-1.5" id="fileNameDisplay">No file chosen</p>
              </div>

              <!-- Upload Button -->
              <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-white font-medium text-sm shadow-lg shadow-sky-500/20 transition active:scale-[0.98]">
                Upload
              </button>
            </form>
          </div>

        </div>
      </div>

      <!-- FOOTER COPYRIGHT -->
      <p class="text-center text-[10px] text-slate-500 mt-8 pt-4 border-t border-white/5">© 2026 CargoNet Systems. Global Logistics Solutions.</p>

    </div>
  </main>

</div>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>