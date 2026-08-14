<?php
$page_title = "Sales Agent Dashboard · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Fetch Dynamic Leads Stats mula sa FastAPI (/api/v1/leads/stats)
$stats_res  = make_api_request('/api/v1/leads/stats', 'GET');
$stats_data = $stats_res['data'] ?? [];

// Extract Live Counts
$total_leads = (int)($stats_data['all'] ?? 0);
$new_inquiry = (int)($stats_data['new_inquiry'] ?? 0);
$qualifying  = (int)($stats_data['qualifying'] ?? 0);
$quote_sent  = (int)($stats_data['quote_sent'] ?? 0);
$negotiation = (int)($stats_data['negotiation'] ?? 0);
$closed_won  = (int)($stats_data['closed_won'] ?? 0);

// Compute Percentage para sa Pipeline Progress Bars
$max_count = max(1, $total_leads); 
$pipeline  = [
    'new_inquiry' => ['count' => $new_inquiry, 'percentage' => min(100, round(($new_inquiry / $max_count) * 100))],
    'qualifying'  => ['count' => $qualifying,  'percentage' => min(100, round(($qualifying / $max_count) * 100))],
    'quote_sent'  => ['count' => $quote_sent,  'percentage' => min(100, round(($quote_sent / $max_count) * 100))],
    'negotiation' => ['count' => $negotiation, 'percentage' => min(100, round(($negotiation / $max_count) * 100))],
    'won_mtd'     => ['count' => $closed_won,  'percentage' => min(100, round(($closed_won / $max_count) * 100))],
];

// 2. Fetch Pending Leads para sa Escalation Queue Widget 
$recent_leads_res = make_api_request('/api/v1/leads/?status=new_inquiry&limit=5', 'GET');
$escalations      = $recent_leads_res['data']['data'] ?? [];

// Helper function para sa Status Badges
function getContractStatusBadge($status) {
    switch (strtoupper(trim($status))) {
        case 'ACTIVE':
            return 'bg-emerald-100 text-emerald-600';
        case 'PENDING':
        case 'PENDING APPROVAL':
            return 'bg-amber-100 text-amber-600';
        case 'DRAFT':
            return 'bg-blue-100 text-blue-600';
        default:
            return 'bg-slate-100 text-slate-600';
    }
}
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php include_once 'components/top_header.php'; ?>

  <!-- ROW 1: TOP KPI METRICS -->
  <?php include_once 'components/kpi_cards.php'; ?>

  <!-- ROW 2: MAIN DASHBOARD GRID -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- LEFT COLUMN: PIPELINE SNAPSHOT -->
    <?php include_once 'components/pipeline_snapshot.php'; ?>

    <!-- RIGHT COLUMN WIDGETS -->
    <div class="lg:col-span-4 space-y-6">
      <?php include_once 'components/escalation_queue.php'; ?>
      <?php include_once 'components/my_contracts.php'; ?>
    </div>

  </div>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>