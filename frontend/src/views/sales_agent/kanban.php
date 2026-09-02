<?php
$page_title = "Kanban Pipeline · PRIORITY HANDLING";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Fetch lahat ng leads mula sa FastAPI
$leads_res = make_api_request('/api/v1/leads/?limit=100&include_closed=true', 'GET');

// Robust check para sa iba't ibang wrapper ng API response
$all_leads = [];
if (isset($leads_res['data']['data']) && is_array($leads_res['data']['data'])) {
    $all_leads = $leads_res['data']['data'];
} elseif (isset($leads_res['data']) && is_array($leads_res['data'])) {
    $all_leads = $leads_res['data'];
} elseif (is_array($leads_res)) {
    $all_leads = $leads_res;
}

// 2. I-group ang leads ayon sa Sales Pipeline Stage
$columns = [
    'new_inquiry' => ['title' => 'NEW INQUIRY', 'items' => []],
    'qualifying'  => ['title' => 'QUALIFYING',  'items' => []],
    'quote_sent'  => ['title' => 'QUOTE SENT',  'items' => []],
    'negotiation' => ['title' => 'NEGOTIATION', 'items' => []],
    'closed_won'  => ['title' => 'WON',         'items' => []],
];

foreach ($all_leads as $lead) {
    // Kunin ang status at i-clean
    $st = strtolower(trim($lead['status'] ?? 'new_inquiry'));

    // Handle variations ng status string mula sa DB
    if ($st === 'won' || $st === 'closed won' || $st === 'closed_won') {
        $st = 'closed_won';
    }

    if (isset($columns[$st])) {
        $columns[$st]['items'][] = $lead;
    } else {
        $columns['new_inquiry']['items'][] = $lead;
    }
}
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Kanban Pipeline</h1>
      <p class="text-sm text-slate-500">Real-time status updates synced across My Leads & Kanban</p>
    </div>

    <!-- SEARCH & ACTION BUTTON -->
    <div class="flex items-center gap-3">
      <div class="relative">
        <input 
          type="text" 
          id="kanbanSearch" 
          placeholder="Search leads..." 
          onkeyup="filterKanbanCards()"
          class="w-64 pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-sm"
        >
        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>

      
    </div>
  </div>

  <!-- KANBAN BOARD CONTAINER -->
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 items-start">
    
    <?php foreach ($columns as $stage_key => $column): ?>
      <div 
        class="bg-slate-100/70 p-3 rounded-2xl border border-slate-200/60 flex flex-col min-h-[75vh]"
        ondragover="allowDrop(event)"
        ondrop="dropLead(event, '<?= $stage_key ?>')"
        data-stage="<?= $stage_key ?>"
      >
        <!-- COLUMN HEADER -->
        <div class="flex items-center justify-between mb-3 px-1">
          <h2 class="text-xs font-bold text-slate-600 tracking-wider uppercase">
            <?= $column['title'] ?>
          </h2>
          <span class="bg-white text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full border border-slate-200">
            <?= count($column['items']) ?>
          </span>
        </div>

        <!-- CARDS CONTAINER -->
        <div class="space-y-3 flex-1 overflow-y-auto">
          <?php if (empty($column['items'])): ?>
            <div class="p-4 border-2 border-dashed border-slate-200/80 rounded-xl text-center text-xs text-slate-400">
              No leads in this stage
            </div>
          <?php else: ?>
            <?php foreach ($column['items'] as $lead): ?>
              
              <!-- CLEAN KANBAN CARD -->
              <div 
                draggable="true" 
                ondragstart="dragLead(event, '<?= $lead['id'] ?>')"
                id="lead-card-<?= $lead['id'] ?>"
                class="kanban-card bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm hover:shadow-md transition cursor-grab active:cursor-grabbing group relative"
              >
                <!-- Company Name -->
                <h3 class="font-bold text-slate-800 text-sm group-hover:text-purple-600 transition mb-1">
                  <?= htmlspecialchars($lead['company_name'] ?? 'Unassigned Company') ?>
                </h3>

                <!-- Contact Person -->
                <p class="text-xs text-slate-400 mb-3">
                   <?= htmlspecialchars($lead['contact_person'] ?? 'No contact person') ?>
                </p>

                <!-- ESTIMATED PRICE BADGE -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                  <div class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                    <span>₱<?= number_format((float)($lead['estimated_amount'] ?? 0), 2) ?></span>
                  </div>
                </div>

              </div>

            <?php endforeach; ?>
          <?php endif; ?>
        </div>

      </div>
    <?php endforeach; ?>

  </div>

</main>

<!-- JAVASCRIPT FOR REAL-TIME SYNC & DRAG & DROP -->
<script src="../../../assets/js/sales_agent/kanban.js"></script>

<?php include_once 'components/alert.php'; ?>
<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>