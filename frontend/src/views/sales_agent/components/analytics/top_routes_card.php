<?php
$routes_res  = make_api_request('/api/v1/analytics/top-routes', 'GET');
$routes_list = $routes_res['data']['data'] ?? [];

$categories  = [];
$full_routes = [];
$values      = [];

foreach ($routes_list as $r) {
    $categories[]  = $r['short_name'];   
    $full_routes[] = $r['route_name'];   
    $values[]      = $r['inquiries_count']; 
}

// Fallback visual data
if (empty($categories)) {
    $categories  = ['Manila', 'Cebu', 'Davao', 'Subic', 'Iloilo'];
    $full_routes = ['Manila → China', 'Cebu → Singapore', 'Davao → Japan', 'Subic → Korea', 'Iloilo → USA'];
    $values      = [0, 0, 0, 0, 0];
}
?>

<!-- CARD 3: TOP ROUTES -->
<div class="bg-gradient-to-br from-[#689dff] via-[#4b82f6] to-[#3b70e6] p-5 rounded-[2.2rem] text-white shadow-sm w-[320px] h-[210px] flex flex-col justify-between relative overflow-hidden">
  
  <div class="flex items-center justify-between z-10">
    <span class="text-white/90 font-medium text-lg tracking-tight">Top Routes</span>
    <button type="button" class="text-white/60 hover:text-white transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    </button>
  </div>

  <div id="topRoutesChart" class="w-full -mb-3 -ml-1"></div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const categories = <?= json_encode($categories) ?>;
    const fullRoutes = <?= json_encode($full_routes) ?>;
    const values = <?= json_encode($values) ?>;

    const options = {
        series: [{
            name: 'Inquiries',
            data: values
        }],
        chart: {
            type: 'bar',
            height: 145,
            toolbar: { show: false },
            sparkline: { enabled: false }
        },
        plotOptions: {
            bar: {
                columnWidth: '42%',
                borderRadius: 8,
                borderRadiusApplication: 'end'
            }
        },
        colors: ['#FFFFFF'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: "vertical",
                shadeIntensity: 0.2,
                opacityFrom: 0.95,
                opacityTo: 0.40,
                stops: [0, 100]
            }
        },
        dataLabels: { enabled: false },
        grid: { show: false },
        xaxis: {
            categories: categories,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: '#FFFFFF',
                    fontSize: '10px',
                    fontWeight: 500
                }
            }
        },
        yaxis: { show: false },
        tooltip: {
            theme: 'dark',
            x: {
                formatter: function (val, opts) {
                    // Gamitin ang full route name kapag nag-hover
                    return fullRoutes[opts.dataPointIndex] || val;
                }
            },
            y: {
                formatter: function (val) {
                    return val + " Inquiries";
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#topRoutesChart"), options);
    chart.render();
});
</script>