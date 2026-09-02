document.addEventListener('DOMContentLoaded', () => {
    fetchPipelineDashboard();
});

async function fetchPipelineDashboard() {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/v1/analytics/sales-dashboard');
        const json = await response.json();

        if (json.status !== 'success' || !json.data) {
            console.error('API Error:', json.message || 'Failed to fetch dashboard data');
            return;
        }

        const data = json.data;

        // 1. Update KPI Counters
        if (data.kpis) {
            document.getElementById('chart-total-leads').innerText = data.kpis.active_leads || 0;
            document.getElementById('chart-won-mtd').innerText = data.kpis.customers_closed_mtd || 0;

            if (data.kpis.stages) {
                const s = data.kpis.stages;
                document.getElementById('cnt-new').innerText = s.new || 0;
                document.getElementById('cnt-qualifying').innerText = s.qualifying || 0;
                document.getElementById('cnt-quote').innerText = s.quote_sent || 0;
                document.getElementById('cnt-negotiation').innerText = s.negotiation || 0;
                document.getElementById('cnt-won').innerText = s.won || 0;
            }
        }

        // 2. Render Pipeline Area Chart
        if (data.pipeline_activity) {
            renderPipelineChart(data.pipeline_activity.dates || [], data.pipeline_activity.counts || []);
        }

        // 3. Render Top Customers
        if (data.top_customers) {
            renderTopCustomers(data.top_customers);
        }

    } catch (error) {
        console.error('Fetch Error:', error);
    }
}

let pipelineChartInstance = null;

function renderPipelineChart(dates, counts) {
    const chartContainer = document.getElementById('pipelineActivityChart');
    if (!chartContainer) return;

    const options = {
        series: [{
            name: 'Inquiries',
            data: counts
        }],
        chart: {
            type: 'area',
            height: 240,
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif'
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#4F46E5'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: dates,
            tickAmount: 6, 
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: '#9CA3AF', fontSize: '11px' } }
        },
        yaxis: {
            min: 0, 
            forceNiceScale: true, 
            labels: { style: { colors: '#9CA3AF', fontSize: '11px' } }
        },
        grid: {
            borderColor: '#F3F4F6',
            strokeDashArray: 4
        }
    };

    if (pipelineChartInstance) {
        pipelineChartInstance.destroy();
    }

    pipelineChartInstance = new ApexCharts(chartContainer, options);
    pipelineChartInstance.render();
}

function getTierColor(tier) {
    const cleanTier = (tier || 'BRONZE').toUpperCase().trim();
    switch (cleanTier) {
        case 'PLATINUM':
            return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'GOLD':
            return 'bg-yellow-100 text-yellow-800 border-yellow-300';
        case 'SILVER':
            return 'bg-slate-100 text-slate-700 border-slate-200';
        case 'BRONZE':
        default:
            return 'bg-amber-100 text-amber-800 border-amber-200';
    }
}

function renderTopCustomers(customers) {
    const container = document.getElementById('top-customers-list');
    if (!container) return;

    container.innerHTML = '';

    if (!customers || customers.length === 0) {
        container.innerHTML = '<p class="text-xs text-gray-400 text-center py-4">No customer records found.</p>';
        return;
    }

    customers.forEach(cust => {
        const item = `
            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                <div>
                    <h5 class="font-bold text-gray-900 text-xs">${cust.name}</h5>
                    <p class="text-[11px] text-gray-500">${cust.contact_person}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-md border ${getTierColor(cust.tier)}">${cust.tier}</span>
                    <p class="text-[11px] font-semibold text-gray-700 mt-0.5">${cust.bookings_count} Bookings</p>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', item);
    });
}