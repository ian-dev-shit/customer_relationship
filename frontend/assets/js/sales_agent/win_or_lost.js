document.addEventListener("DOMContentLoaded", function () {
    async function fetchWinLossAnalytics() {
        try {
            // Palitan ang URL kung pangkalahatang API route ng FastAPI gamit mo
            const response = await fetch('http://127.0.0.1:8000/api/v1/analytics/win-loss-service');
            const data = await response.json();

            if (data.status === 'success') {
                // Update Gemini AI Suggestion Box
                const suggestionEl = document.getElementById('ai-win-loss-suggestion');
                if (suggestionEl && data.ai_suggestion) {
                    suggestionEl.innerText = data.ai_suggestion;
                }

                // Render Chart
                renderWinLossChart(data.categories, data.series);
            }
        } catch (error) {
            console.error("Error fetching Win/Loss Analytics:", error);
            const suggestionEl = document.getElementById('ai-win-loss-suggestion');
            if (suggestionEl) {
                suggestionEl.innerText = "Unable to fetch AI analytics at this time.";
            }
        }
    }

    let winLossChartInstance = null;

   function renderWinLossChart(categories, series) {
    const chartElement = document.querySelector("#winLossStackedBarChart");
    if (!chartElement) return;

    if (winLossChartInstance) {
        winLossChartInstance.destroy();
    }

    const options = {
        series: series,
        chart: {
            type: 'bar',
            height: 220,
            stacked: true,
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'Inter, sans-serif'
        },
        // Shades of Blue para sa Won Deals at Shades of Red para sa Lost Deals
        colors: ['#3B82F6', '#EF4444', '#60A5FA', '#F87171'],
        fill: {
            opacity: 0.95
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '30%', // Sakto ang kapal sa kalahating card width
                borderRadius: 8,
                borderRadiusApplication: 'end',
                borderRadiusWhenStacked: 'last'
            }
        },
        dataLabels: { enabled: false },
        grid: {
            borderColor: '#F1F5F9',
            strokeDashArray: 3,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } }
        },
        xaxis: {
            categories: categories,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: '#94A3B8',
                    fontSize: '11px',
                    fontWeight: 600
                }
            }
        },
        yaxis: {
            forceNiceScale: true,
            min: 0,
            labels: {
                style: {
                    colors: '#94A3B8',
                    fontSize: '10px',
                    fontWeight: 500
                },
                formatter: (val) => Math.floor(val)
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '11px',
            fontWeight: 600,
            offsetY: -5,
            labels: { colors: '#64748B' },
            markers: { width: 8, height: 8, radius: 12 }
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function (val) {
                    return val + " deals";
                }
            }
        }
    };

    winLossChartInstance = new ApexCharts(chartElement, options);
    winLossChartInstance.render();
}

    fetchWinLossAnalytics();
});