document.addEventListener("DOMContentLoaded", function () {
    async function fetchServiceWonDistribution() {
        try {
            const response = await fetch('http://127.0.0.1:8000/api/v1/analytics/service-won-distribution');
            const data = await response.json();

            if (data.status === 'success') {
                // Update Gemini AI Suggestion Box
                const suggestionEl = document.getElementById('ai-service-donut-suggestion');
                if (suggestionEl && data.ai_suggestion) {
                    suggestionEl.innerText = data.ai_suggestion;
                }

                // Render Donut Chart
                renderDonutChart(data.labels, data.series);
            }
        } catch (error) {
            console.error("Error fetching Service Distribution:", error);
            const suggestionEl = document.getElementById('ai-service-donut-suggestion');
            if (suggestionEl) {
                suggestionEl.innerText = "Unable to fetch service distribution data.";
            }
        }
    }

    let donutChartInstance = null;

    function renderDonutChart(labels, series) {
        const chartElement = document.querySelector("#serviceWonDonutChart");
        if (!chartElement) return;

        if (donutChartInstance) {
            donutChartInstance.destroy();
        }

        const options = {
            series: series.length > 0 ? series : [1],
            labels: labels.length > 0 ? labels : ['No Data'],
            chart: {
                type: 'donut',
                height: 210,
                fontFamily: 'Inter, sans-serif'
            },
            // Monochromatic Blue Palette katulad ng sa reference UI
            colors: ['#2563EB', '#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE'],
            stroke: {
                show: true,
                colors: ['#FFFFFF'],
                width: 2
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Won',
                                fontSize: '11px',
                                fontWeight: 600,
                                color: '#94A3B8',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + " deals";
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(1) + "%";
                },
                style: {
                    fontSize: '10px',
                    fontFamily: 'Inter, sans-serif',
                    fontWeight: 'bold'
                },
                dropShadow: { enabled: false }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '11px',
                fontWeight: 600,
                labels: { colors: '#64748B' },
                markers: { width: 8, height: 8, radius: 12 },
                itemMargin: { horizontal: 6, vertical: 2 }
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

        donutChartInstance = new ApexCharts(chartElement, options);
        donutChartInstance.render();
    }

    fetchServiceWonDistribution();
});