<?php
$shipments_res    = make_api_request('/api/v1/analytics/shipments-closed', 'GET');
$shipments_data   = $shipments_res['data'] ?? [];
$total_closed     = $shipments_data['total_closed'] ?? 0;
$monthly_series   = $shipments_data['monthly_series'] ?? array_fill(0, 12, 0);
?>

<!-- CARD 4: SHIPMENTS CLOSED (WHITE CARD STYLE) -->
<div class="bg-white p-5 rounded-[2.2rem] shadow-sm w-[320px] h-[210px] flex flex-col justify-between border border-gray-100 relative overflow-hidden">
  
  <!-- HEADER -->
  <div class="flex flex-col">
    <div class="flex items-center justify-between">
      <span class="text-gray-500 font-semibold text-sm tracking-tight">Shipments Closed</span>
      <button type="button" class="text-gray-400 hover:text-gray-600 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </button>
    </div>
    
    <!-- BIG TOTAL NUMBER -->
    <div class="text-2xl font-bold text-gray-900 tracking-tight mt-0.5">
      <?= number_format($total_closed) ?>
    </div>
  </div>

  <!-- APEXCHARTS AREA CHART CONTAINER -->
  <div id="shipmentsClosedChart" class="w-full -mb-2 -ml-2"></div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const monthlySeries = <?= json_encode($monthly_series) ?>;
    const months = ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];

    const options = {
        series: [{
            name: 'Closed Shipments',
            data: monthlySeries
        }],
        chart: {
            type: 'area',
            height: 120,
            toolbar: { show: false },
            sparkline: { enabled: false }
        },
        colors: ['#3b82f6'],
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.25,
                opacityTo: 0.0,
                stops: [0, 90, 100]
            }
        },
        markers: {
            size: 3,
            colors: ['#3b82f6'],
            strokeColors: '#FFFFFF',
            strokeWidth: 1.5,
            hover: { size: 5 }
        },
        dataLabels: { enabled: false },
        grid: {
            show: true,
            borderColor: '#f3f4f6',
            strokeDashArray: 3,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } }
        },
        xaxis: {
            categories: months,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: '#9ca3af',
                    fontSize: '10px',
                    fontWeight: 600
                }
            }
        },
        yaxis: {
            show: false
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function (val) {
                    return val + " Deals";
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#shipmentsClosedChart"), options);
    chart.render();
});
</script>