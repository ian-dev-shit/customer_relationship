<?php
$page_title = "Customer Dashboard · SwiftFreight";

include_once '../../includes/header.php';
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8 relative">

  <!-- TOP HEADER -->
  <?php include_once '../../components/top_header.php'; ?>

  <!-- WELCOME BANNER -->
  <div class="mt-6 mb-8 bg-slate-900 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
          <h1 class="text-2xl font-bold">Welcome to Priority Handling Logistics!</h1>
          <p class="text-slate-400 text-xs mt-1">Suriin ang mga pinakabagong promo, anunsyo, at mga update mula sa aming sales team.</p>
      </div>
      <button onclick="loadCampaignPosts()" class="self-start md:self-auto bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2.5 rounded-xl transition font-semibold flex items-center gap-2 shadow-sm">
          <i class="fa-solid fa-rotate"></i> Refresh Campaigns
      </button>
  </div>

  <!-- CAMPAIGN POSTS SECTION -->
  <div class="mb-8 w-full max-w-5xl mx-auto">
      <div class="flex items-center justify-between mb-4">
          <div>
              <h2 class="text-lg font-bold text-slate-900">Featured Campaigns & Announcements</h2>
              <p class="text-slate-500 text-xs">Aktibong special offers at announcements para sa iyo.</p>
          </div>
      </div>

      <!-- DYNAMIC CAMPAIGN CARDS CONTAINER -->
      <div id="campaignPostsContainer" class="w-full flex flex-col gap-6">
          <div class="w-full text-center py-12 bg-white border border-slate-200 rounded-2xl shadow-sm">
              <i class="fa-solid fa-circle-notch fa-spin text-2xl text-blue-600 mb-2"></i>
              <p class="text-slate-500 text-xs font-medium">Ikinakarga ang mga campaign posts...</p>
          </div>
      </div>
  </div>

</main>

<!-- FLOATING CHAT BUTTON & MODAL -->
<div class="fixed bottom-6 right-6 z-40">
    <button onclick="toggleChatModal()" class="w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-lg transition hover:scale-105">
        <i class="fa-solid fa-comments text-xl"></i>
    </button>
</div>

<div id="chatModal" class="fixed bottom-24 right-6 w-96 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 hidden flex-col overflow-hidden">
    <div class="bg-slate-900 text-white p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
            <span class="font-bold text-sm">Customer Support Chat</span>
        </div>
        <button onclick="toggleChatModal()" class="text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div id="chatMessages" class="p-4 h-80 overflow-y-auto text-xs space-y-3 bg-slate-50">
        <div class="bg-white p-3 rounded-xl border border-slate-200 max-w-[80%] shadow-sm">
            Kumusta! May maipaglilingkod ba kami sa inyong booking o shipment?
        </div>
    </div>
    <div class="p-3 bg-white border-t border-slate-200 flex gap-2">
        <input type="text" id="chatInput" placeholder="Mag-type ng mensahe..." class="flex-1 bg-slate-100 text-xs px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500">
        <button onclick="sendChatMessage()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-xl text-xs font-semibold">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </div>
</div>

<!-- IMAGE MODAL FOR FULLSCREEN -->
<div id="imageModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-5xl w-full max-h-[90vh] flex flex-col items-center justify-center" onclick="event.stopPropagation()">
        <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white text-sm hover:text-slate-300 font-semibold">
            <i class="fa-solid fa-xmark"></i> Isara
        </button>
        <img id="modalImage" src="" alt="Campaign Image" class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl">
    </div>
</div>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- JAVASCRIPT LOGIC -->
<script src="../../../assets/js/customer/dashboard.js"></script>