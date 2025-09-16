<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report Incassi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Contenitore con sfondo bianco --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800">Incassi degli ultimi {{ count($labels) }} giorni</h3>
                
                {{-- Contenitore per dare un'altezza al grafico --}}
                <div class="mt-6 h-96">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('revenueChart').getContext('2d');
                const labels = @json($labels);
                const data = @json($data);

                // Colori per il grafico (stessi colori, funzionano bene su sfondo chiaro)
                const chartColor = 'rgb(79, 70, 229)'; // Indigo-600 di Tailwind
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)'); // Opacità ridotta per sfondo chiaro
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Incasso Giornaliero (€)',
                            data: data,
                            fill: true,
                            backgroundColor: gradient,
                            borderColor: chartColor,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 7,
                            pointHoverBackgroundColor: chartColor,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { // Stile del tooltip per tema chiaro
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                backgroundColor: '#fff',
                                titleColor: 'rgb(31, 41, 55)',    // Grigio scuro
                                bodyColor: 'rgb(75, 85, 99)',     // Grigio medio
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
                                    display: true, // Manteniamo la griglia verticale nascosta
                                },
                                ticks: {
                                    color: 'rgb(107, 114, 128)', // Grigio medio per le etichette
                                    font: { size: 12 }
                                }
                            },
                            y: {
                                grid: {
                                    drawBorder: false,
                                    color: 'rgb(229, 231, 235)', // Griglia orizzontale grigio chiaro
                                },
                                ticks: {
                                    color: 'rgb(107, 114, 128)', // Grigio medio per le etichette
                                    callback: (value) => value + ' €',
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>