<?php 

$page_title = "Sales Agent Analytics · PRIORITY HANDLING";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php include_once '../../components/top_header.php'; ?>

  <!-- PAGE HEADER -->
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Business Intelligence & Sales Analytics</h1>
    <p class="text-xs text-slate-400">Real-time performance metrics and predictive analytics</p>
  </div>

  <!-- MAIN WRAPPER (2-COLUMN LAYOUT) -->
  <div class="flex flex-col xl:flex-row gap-6 items-start mb-8 w-full">
    
    <!-- LEFT SIDE: 2x2 CARDS GRID (FIXED 660px MAX WIDTH) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 shrink-0 w-full xl:w-[660px]">
      <?php include_once 'components/analytics/gross_revenue_card.php'; ?>
      <?php include_once 'components/analytics/service_types_card.php'; ?>
      <?php include_once 'components/analytics/top_routes_card.php'; ?>
      <?php include_once 'components/analytics/shipments_closed_card.php'; ?>
    </div>

    <!-- RIGHT SIDE: PIPELINE SNAPSHOT CONTAINER -->
    <div class="w-full xl:flex-1 min-w-0">
      <?php include_once 'components/pipeline_snapshot.php'; ?>
    </div>

  </div>

 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 w-full mb-8">
  
  <!-- LEFT: WIN / LOSS BY SERVICE TYPE -->
  <div class="w-full">
    <?php include_once 'components/analytics/win_loss_card.php'; ?>
  </div>

  <!-- MIDDLE: SERVICE WON DONUT CHART -->
  <div class="w-full">
    <?php include_once 'components/analytics/service_distribution_card.php'; ?>
  </div>

  <!-- RIGHT: CARGO WEIGHT CLASS BAR CHART -->
  <div class="w-full">
    <?php include_once 'components/analytics/weight_class_card.php'; ?>
  </div>

</div>

  <?php include_once 'components/lead_modal.php'; ?>

</main>

<?php include_once 'components/alert.php'; ?>

<script src="../../../assets/js/sales_agent/pipiline.js"></script>
<script src="../../../assets/js/sales_agent/new_leads.js"></script>
<script src="../../../assets/js/sales_agent/win_or_lost.js"></script>
<script src="../../../assets/js/sales_agent/service_won_donut.js"></script>
<script src="../../../assets/js/sales_agent/weight_class_chart.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>