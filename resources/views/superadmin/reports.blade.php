<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report Incassi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">Incassi degli ultimi 30 giorni</h3>
                    {{-- Il grafico verrà disegnato qui --}}
                    <canvas id="revenueChart" class="mt-4"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- 1. Includiamo la libreria Chart.js --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('revenueChart').getContext('2d');

                // 2. Passiamo i dati da PHP a JavaScript in modo sicuro
                const labels = @json($labels);
                const data = @json($data);

                // 3. Configuriamo e creiamo il grafico
                new Chart(ctx, {
                    type: 'line', // Tipo di grafico (puoi usare 'bar')
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Incasso Giornaliero (€)',
                            data: data,
                            fill: true,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            tension: 0.2
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    // Aggiunge il simbolo € sull'asse Y
                                    callback: function(value, index, values) {
                                        return value + ' €';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Incasso: ${context.raw.toFixed(2)} €`;
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