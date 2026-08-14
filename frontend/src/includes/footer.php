</div> <!-- Isinara ang Main Layout Flex Wrapper mula sa header.php -->

  <!-- LOGOUT MODAL CONTAINER -->
  <div id="logoutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
    
    <div class="w-full max-w-sm bg-[#0F172A] border border-slate-800 rounded-2xl shadow-2xl p-6 text-center transform scale-95 transition-all duration-200" id="logoutModalContainer">
      
      <!-- STATE 1: CONFIRMATION VIEW -->
      <div id="logoutConfirmState">
        <!-- Warning Icon -->
        <div class="w-12 h-12 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center mx-auto mb-4">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25" />
          </svg>
        </div>

        <h3 class="text-base font-bold text-white mb-1">Confirm Logout</h3>
        <p class="text-xs text-slate-400 mb-6 leading-relaxed">Are you sure you want to sign out of your account?</p>

        <div class="grid grid-cols-2 gap-3">
          <button id="cancelLogoutBtn" type="button" 
                  class="py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition active:scale-95">
            Cancel
          </button>
          <button id="confirmLogoutBtn" type="button" 
                  class="py-2.5 px-4 bg-red-600 hover:bg-red-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-red-600/20 transition active:scale-95">
            Yes, Logout
          </button>
        </div>
      </div>

      <!-- STATE 2: LOADING VIEW -->
      <div id="logoutLoadingState" class="hidden py-4">
        <!-- Spinner -->
        <div class="relative w-10 h-10 mx-auto mb-4">
          <div class="w-10 h-10 rounded-full border-2 border-blue-500/20"></div>
          <div class="w-10 h-10 rounded-full border-2 border-blue-500 border-t-transparent animate-spin absolute top-0 left-0"></div>
        </div>

        <h4 class="text-xs font-semibold text-white tracking-wide" id="logoutStatusText">Logging out...</h4>
        <p class="text-[10px] text-slate-400 mt-1">Clearing secure session variables</p>
      </div>

    </div>
  </div>

  <!-- JS Script File Includes -->
  <script src="/assets/js/dashboard.js"></script>
  <script src="/assets/js/logout.js"></script>
</body>
</html>