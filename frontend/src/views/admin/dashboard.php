<?php
$page_title = "Admin Control Center · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Fetch lahat ng Closed Won Tickets mula sa FastAPI (/api/v1/admin/close-won-tickets)
$tickets_res = make_api_request('/api/v1/admin/close-won-tickets', 'GET');
$all_tickets = $tickets_res['data'] ?? [];

// FILTER LOGIC 
$pending_tickets = array_filter($all_tickets, function($ticket) {
    return !isset($ticket['customer_id']) || is_null($ticket['customer_id']) || trim((string)$ticket['customer_id']) === '';
});

$ticket_count = count($pending_tickets);

// 2. Fetch Active Customers count 
$customers_res    = make_api_request('/api/v1/admin/customers', 'GET');
$customers_list   = $customers_res['data'] ?? [];
$active_customers = count($customers_list);
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER -->
  <?php include_once 'components/top_header.php'; ?>

  <!-- ROW 1: TOP 2 KPI METRICS -->
  <?php include_once 'components/kpi_cards.php'; ?>

  <!-- ROW 2: MAIN DASHBOARD GRID -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Main content widgets -->
  </div>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>