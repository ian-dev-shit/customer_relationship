
const API_URL = window.APP_CONFIG.API_BASE_URL;

document.addEventListener("DOMContentLoaded", function() {
    fetch(`${API_URL}/api/v1/analytics/dashboard`)
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                const kpis = res.data.kpis;
                const growth = kpis.growth || {};

                // 1. Set KPI Numbers
                document.getElementById('kpi-active-leads').innerText = kpis.active_leads;
                document.getElementById('kpi-conversion-rate').innerText = kpis.booking_conversion_rate + '%';
                document.getElementById('kpi-revenue').innerText = '₱' + kpis.total_revenue_mtd.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                document.getElementById('kpi-closed').innerText = kpis.customers_closed_mtd;

                // 2. Set Dynamic Growth Badges 
                updateDynamicBadge('growth-conversion', growth.conversion_growth ?? 0);
                updateDynamicBadge('growth-revenue', growth.revenue_growth ?? 0);
                updateDynamicBadge('growth-closed', growth.closed_growth ?? 0);

                // 3. Render ML Forecast Graph
                if (res.data.revenue_forecast) {
                    renderForecastChart(res.data.revenue_forecast);
                }
            }
        })
        .catch(err => console.error('Error fetching analytics:', err));
});

// Helper function para sa Green/Red badges
function updateDynamicBadge(elementId, value) {
    const el = document.getElementById(elementId);
    if (!el) return;

    const isPositive = value >= 0;
    
    // Up or Down Arrow Icon
    const iconSvg = isPositive 
        ? `<svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>`
        : `<svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>`;

    const bgClass = isPositive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600';

    el.className = `inline-flex items-center text-xs font-bold px-2 py-0.5 rounded-full ${bgClass}`;
    el.innerHTML = `${iconSvg} ${Math.abs(value)}% vs last mo`;
}

let forecastChartInstance = null;

function renderForecastChart(forecast) {
    const chartContainer = document.querySelector("#revenueForecastChart");
    if (!chartContainer) return;

    // Default safety values
    const labels = (forecast && forecast.labels && forecast.labels.length) 
        ? forecast.labels 
        : ['Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Des', 'Jan', 'Feb'];
        
    const actualData = (forecast && forecast.actual && forecast.actual.length) 
        ? forecast.actual 
        : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        
    const predictedData = (forecast && forecast.predicted && forecast.predicted.length) 
        ? forecast.predicted 
        : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    const options = {
        series: [
            {
                name: 'Actual Revenue',
                type: 'column',
                data: actualData
            },
            {
                name: 'Predicted Trend (ML)',
                type: 'line',
                data: predictedData
            }
        ],
        chart: {
            height: 310,
            type: 'line',
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif'
        },
        plotOptions: {
            bar: {
                // ✅ NILIMITAHAN ANG LAPAD NG BAR
                columnWidth: '24px', // Ginawang fixed pixel width para hindi mag-stretch nang sobrang taba
                borderRadius: 6,
                borderRadiusApplication: 'top'
            }
        },
        stroke: {
            width: [0, 2],
            dashArray: [0, 4],
            curve: 'smooth'
        },
        colors: ['#F3F4F6', '#9CA3AF'],
        fill: {
            opacity: [0.85, 1],
            type: ['gradient', 'solid'],
            gradient: {
                inverseColors: false,
                shade: 'light',
                type: "vertical",
                opacityFrom: 0.85,
                opacityTo: 0.25,
                stops: [0, 100],
                colorStops: [
                    [
                        { offset: 0, color: "#6366F1", opacity: 1 },
                        { offset: 15, color: "#F3F4F6", opacity: 0.6 },
                        { offset: 100, color: "#F9FAFB", opacity: 0.1 }
                    ]
                ]
            }
        },
        xaxis: {
            categories: labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: { colors: '#9CA3AF', fontSize: '12px' }
            }
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            labels: {
                style: { colors: '#9CA3AF', fontSize: '12px' },
                formatter: function (val) {
                    if (val >= 1000) {
                        return "₱" + (val / 1000).toFixed(0) + "k";
                    }
                    return "₱" + val;
                }
            }
        },
        grid: {
            borderColor: '#F3F4F6',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } }
        },
        legend: { show: false },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (val) {
                    return val !== null && val !== undefined ? "₱" + Number(val).toLocaleString('en-US') : "N/A";
                }
            }
        }
    };

    if (forecastChartInstance) {
        forecastChartInstance.destroy();
    }

    forecastChartInstance = new ApexCharts(chartContainer, options);
    forecastChartInstance.render();
}



document.addEventListener("DOMContentLoaded", function () {
  renderCalendarStrip();
  fetchPriorityFollowups();
});

// 1. Render Weekly Calendar Strip
function renderCalendarStrip() {
  const now = new Date();
  const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  
  document.getElementById('calendar-month-year').innerText = `${monthNames[now.getMonth()]} ${now.getFullYear()}`;

  const currentDay = now.getDay(); 
  const startOfWeek = new Date(now);
  startOfWeek.setDate(now.getDate() - currentDay); 

  const daysHeader = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
  let stripHTML = '';

  for (let i = 0; i < 7; i++) {
    const dayDate = new Date(startOfWeek);
    dayDate.setDate(startOfWeek.getDate() + i);
    
    const isToday = dayDate.toDateString() === now.toDateString();

    stripHTML += `
      <div class="flex flex-col items-center p-1.5 rounded-xl ${isToday ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-500'}">
        <span class="text-[10px] uppercase font-medium ${isToday ? 'text-indigo-100' : 'text-gray-400'}">${daysHeader[i]}</span>
        <span class="text-xs font-bold ${isToday ? 'text-white' : 'text-gray-800'}">${dayDate.getDate()}</span>
      </div>
    `;
  }
  document.getElementById('calendar-days-strip').innerHTML = stripHTML;
}

// Global State para sa Pagination
let priorityFollowupsData = [];
let currentFollowupPage = 1;
const itemsPerPage = 2;

// 2. Fetch Priority Inquiries from Backend API
async function fetchPriorityFollowups() {
  const container = document.getElementById('priority-list');
  try {
    const response = await fetch('http://127.0.0.1:8000/api/sales/priority-followups'); 
    const result = await response.json();

    priorityFollowupsData = result.data || [];
    currentFollowupPage = 1;

    if (priorityFollowupsData.length === 0) {
      container.innerHTML = `
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl text-xs text-center border border-emerald-100">
          ✨ All inquiries are up to date! No urgent follow-ups needed.
        </div>`;
      updateFollowupPaginationUI(0);
      return;
    }

    renderPriorityFollowupsPage();

  } catch (err) {
    container.innerHTML = `
      <div class="p-3 bg-slate-50 rounded-xl text-xs text-gray-500 text-center">
        Unable to load priority list.
      </div>`;
    updateFollowupPaginationUI(0);
  }
}

// Function para mag-render ng 2 items lang bawat pahina
function renderPriorityFollowupsPage() {
  const container = document.getElementById('priority-list');
  if (!container) return;

  const totalPages = Math.ceil(priorityFollowupsData.length / itemsPerPage);
  const startIndex = (currentFollowupPage - 1) * itemsPerPage;
  const pageItems = priorityFollowupsData.slice(startIndex, startIndex + itemsPerPage);

  let cardsHTML = '';
  pageItems.forEach(item => {
    const isCritical = item.priority_level === 'CRITICAL';
    const badgeStyle = isCritical 
      ? 'bg-rose-50 text-rose-600 border-rose-100' 
      : 'bg-amber-50 text-amber-600 border-amber-100';

    cardsHTML += `
      <div class="p-3.5 bg-slate-50 hover:bg-white rounded-xl border border-slate-100 hover:border-indigo-100 hover:shadow-sm transition-all">
        <div class="flex items-center justify-between mb-1.5">
          <span class="text-xs font-bold text-gray-900">${item.client_name}</span>
          <span class="px-2 py-0.5 text-[10px] font-bold rounded-md border ${badgeStyle}">
            ${item.days_idle}d Idle (${item.priority_level})
          </span>
        </div>
        <div class="flex items-center justify-between text-[11px] text-gray-500 mb-2">
          <span>${item.inquiry_code} • ${item.service_type}</span>
          <span class="font-medium text-gray-700">${item.status}</span>
        </div>
        <div class="flex items-center justify-between pt-2 border-t border-gray-200/60">
          <span class="text-[10px] text-indigo-600 font-medium">⚠️ Needs immediate response</span>
          <a href="/src/views/sales_agent/my_leads.php" class="px-2.5 py-1 text-[11px] font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
            Follow Up →
          </a>
        </div>
      </div>
    `;
  });

  container.innerHTML = cardsHTML;
  updateFollowupPaginationUI(totalPages);
}

// Click Handler para sa Prev/Next Buttons
function changeFollowupPage(direction) {
  const totalPages = Math.ceil(priorityFollowupsData.length / itemsPerPage);
  const newPage = currentFollowupPage + direction;

  if (newPage >= 1 && newPage <= totalPages) {
    currentFollowupPage = newPage;
    renderPriorityFollowupsPage();
  }
}

// UI Controller para sa Pagination Buttons
function updateFollowupPaginationUI(totalPages) {
  const pagWrapper = document.getElementById('followup-pagination');
  const indicator = document.getElementById('followupPageIndicator');
  const prevBtn = document.getElementById('prevFollowupBtn');
  const nextBtn = document.getElementById('nextFollowupBtn');

  if (!pagWrapper) return;

  if (totalPages > 1) {
    pagWrapper.classList.remove('hidden');
    if (indicator) indicator.innerText = `${currentFollowupPage}/${totalPages}`;
    if (prevBtn) prevBtn.disabled = (currentFollowupPage === 1);
    if (nextBtn) nextBtn.disabled = (currentFollowupPage === totalPages);
  } else {
    pagWrapper.classList.add('hidden');
  }
}