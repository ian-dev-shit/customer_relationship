<?php
$service_res  = make_api_request('/api/v1/analytics/service-types', 'GET');
$service_list = $service_res['data']['data'] ?? [];

$top_two = array_slice($service_list, 0, 2);
?>

<!-- CARD 2: SERVICE TYPES  -->
<div class="bg-gradient-to-br from-[#689dff] via-[#4b82f6] to-[#3b70e6] p-6 rounded-[2.2rem] text-white shadow-sm w-[320px] h-[210px] flex flex-col justify-between relative overflow-hidden">
  
  <!-- HEADER -->
  <div class="flex items-center justify-between">
    <span class="text-white/90 font-medium text-lg tracking-tight">Service Types</span>
    <button type="button" class="text-white/60 hover:text-white transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </div>

  <!-- CONTENT (2 COLUMNS: BRAND/SERVICE STYLE) -->
  <div class="grid grid-cols-2 gap-3 my-auto items-end">
    <?php if (empty($top_two)): ?>
      <div class="col-span-2 text-xs text-white/70">No active services</div>
    <?php else: ?>
      <?php foreach ($top_two as $service): 
        $s_name = strtoupper($service['service_name']);
      ?>
        <div class="flex flex-col space-y-1">
          <!-- DYNAMIC ICON / TITLE -->
          <div class="flex items-center space-x-1.5 text-white/90">
            
            <?php if (str_contains($s_name, 'AIR')): ?>
              <!-- AIR FREIGHT: AIRPLANE ICON -->
              <svg class="w-4 h-4 fill-current text-white/80 shrink-0" viewBox="0 0 24 24">
                <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
              </svg>

            <?php elseif (str_contains($s_name, 'SEA') || str_contains($s_name, 'OCEAN') || str_contains($s_name, 'SHIP')): ?>
              <!-- SEA FREIGHT: BARKO / SHIP ICON -->
              <svg class="w-4 h-4 fill-current text-white/80 shrink-0" viewBox="0 0 24 24">
                <path d="M20 21c-1.39 0-2.78-.47-4-1.32-2.44 1.71-5.56 1.71-8 0C6.78 20.53 5.39 21 4 21H2v2h2c1.38 0 2.74-.35 4-.99 2.52 1.29 5.48 1.29 8 0 1.26.64 2.62.99 4 .99h2v-2h-2zM3.95 19H4c1.6 0 3.02-.88 4-2 .98 1.12 2.4 2 4 2s3.02-.88 4-2c.98 1.12 2.4 2 4 2h.05l1.89-6.62C22.12 11.72 21.6 11 20.9 11H19V6c0-1.1-.9-2-2-2h-3V1h-4v3H7c-1.1 0-2 .9-2 2v5H3.1c-.7 0-1.22.72-1.04 1.38L3.95 19zM11 6h2v5h-2V6z"/>
              </svg>

            <?php else: ?>
              <!-- LAND FREIGHT / TRUCKING: TRUCK ICON -->
              <svg class="w-4 h-4 fill-current text-white/80 shrink-0" viewBox="0 0 24 24">
                <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
              </svg>
            <?php endif; ?>

            <span class="text-[11px] uppercase tracking-wider font-bold text-white/80 truncate">
              <?= htmlspecialchars($service['service_name']) ?>
            </span>
          </div>

          <!-- AMOUNT -->
          <div class="text-lg font-bold text-white tracking-tight leading-none pt-1">
            <?= htmlspecialchars($service['formatted_revenue']) ?>
          </div>

          <!-- SUBTITLE / METRIC -->
          <div class="text-[10px] text-white/70 font-medium">
            <?= $service['deals_count'] ?> Deals Closed
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- FOOTER PAGINATION DOTS -->
  <div class="flex justify-center items-center space-x-1.5 pt-1">
    <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
    <span class="w-2.5 h-2.5 rounded-full bg-white/40 border border-white/20"></span>
    <span class="w-2.5 h-2.5 rounded-full bg-white/40 border border-white/20"></span>
  </div>

</div>