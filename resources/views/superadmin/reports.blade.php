<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report Incassi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- 1. Usiamo una griglia per affiancare i grafici su schermi grandi --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                <div class="lg:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800">Incassi degli ultimi 30 giorni</h3>
                    <div class="mt-6 h-96">
                        {{-- 2. Abbiamo rinominato l'ID per chiarezza --}}
                        <canvas id="revenueLineChart"></canvas>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800">Incassi per Servizio (Top 7)</h3>
                    <div class="mt-6 h-96 flex items-center justify-center">
                        <canvas id="revenuePieChart"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Dati passati dal controller
            const lineLabels = @json($lineChartLabels);
            const lineData = @json($lineChartData);
            const pieLabels = @json($pieChartLabels);
            const pieData = @json($pieChartData);

            document.addEventListener('DOMContentLoaded', function() {
                // --- 1. CONFIGURAZIONE COMPLETA GRAFICO A LINEE ---
                const lineCtx = document.getElementById('revenueLineChart').getContext('2d');
                const lineChartColor = 'rgb(79, 70, 229)';
                const lineGradient = lineCtx.createLinearGradient(0, 0, 0, 400);
                lineGradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
                lineGradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: lineLabels,
                        datasets: [{
                            label: 'Incasso Giornaliero (€)',
                            data: lineData,
                            fill: true,
                            backgroundColor: lineGradient,
                            borderColor: lineChartColor,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 7,
                            pointHoverBackgroundColor: lineChartColor,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: { // Stile del tooltip per tema chiaro
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                backgroundColor: '#fff',
                                titleColor: 'rgb(31, 41, 55)', // Grigio scuro
                                bodyColor: 'rgb(75, 85, 99)', // Grigio medio
                                borderColor: 'rgb(229, 231, 235)', // Bordo grigio chiaro
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    title: (tooltipItems) => tooltipItems[0].label,
                                    label: (context) => `Incasso: ${context.parsed.y.toFixed(2)} €`,
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: true,
                                    color: 'rgb(243, 244, 246)'
                                },
                                ticks: {
                                    color: 'rgb(107, 114, 128)',
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    drawBorder: false,
                                    color: 'rgb(229, 231, 235)'
                                },
                                ticks: {
                                    color: 'rgb(107, 114, 128)',
                                    callback: (value) => value + ' €'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });

                // --- 2. CONFIGURAZIONE COMPLETA GRAFICO A TORTA ---
                const pieCtx = document.getElementById('revenuePieChart').getContext('2d');
                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: pieLabels,
                        datasets: [{
                            label: 'Incasso per Servizio',
                            data: pieData,
                            backgroundColor: [
                                'rgba(79, 70, 229, 0.8)', 'rgba(59, 130, 246, 0.8)',
                                'rgba(34, 197, 94, 0.8)', 'rgba(249, 115, 22, 0.8)',
                                'rgba(139, 92, 246, 0.8)', 'rgba(239, 68, 68, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                            ],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: 'rgb(107, 114, 128)',
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const value = context.raw;
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${context.label}: ${value.toFixed(2)} € (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
