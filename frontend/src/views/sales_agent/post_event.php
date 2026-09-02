<?php
$page_title = "Sales Agent Post Event · PRIORITY HANDLING";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

?>

<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php include_once '../../components/top_header.php'; ?>

  <!-- CAMPAIGN & EVENT INLINE FORM & FEED COMPONENT -->
  <div class="mt-6">
    <?php include_once 'components/campaign/campaign_post.php'; ?>
  </div>

</main>

<!-- FRONTEND SCRIPT FOR CAMPAIGN MANAGEMENT -->
<script src="../../../assets/js/sales_agent/campaign_management.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>