<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $service->name }}
        </h2>
    </x-slot>

    @push('styles')
        <style>
            .fc-daygrid-day.fc-day-future {
                cursor: pointer;
            }

            /* Cursore a puntatore sui giorni futuri */
        </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-1 bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-bold">{{ $service->name }}</h3>
                    <p class="mt-2 text-sm text-gray-600">Con {{ $service->worker->name }}</p>
                    <p class="mt-4 text-gray-800 font-bold text-2xl">{{ number_format($service->price, 2, ',', '.') }} €</p>
                    <p class="mt-1 text-sm text-gray-500">Durata: {{ $service->duration }} minuti</p>
                    <p class="mt-4">{{ $service->description }}</p>

                    <hr class="my-6">

                    <form action="{{ route('reservations.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">

                        <div>
                            <x-input-label for="start_time" value="Data e Ora Selezionate" />
                            <x-text-input id="start_time" name="start_time" type="text" class="mt-1 block w-full bg-gray-100" readonly required />
                            <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
                        </div>

                        <div class="mt-4">
                            <x-primary-button class="w-full justify-center" disabled id="submit-button">
                                {{ __('Seleziona uno slot dal calendario') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                <div class="md:col-span-2 bg-white p-6 shadow-sm sm:rounded-lg">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const startTimeInput = document.getElementById('start_time');
                const submitButton = document.getElementById('submit-button');

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    events: '{{ route('services.reservations', $service) }}',
                    // Imposta la data di oggi come primo giorno visibile e disabilita il passato
                    validRange: {
                        start: new Date().toISOString().split('T')[0],
                    },
                    // Funzione eseguita quando si clicca su una data/ora
                    dateClick: function(info) {
                        // Formatta la data cliccata in un formato leggibile e per l'input
                        const selectedDateTime = new Date(info.dateStr).toLocaleString('it-IT', {
                            dateStyle: 'long',
                            timeStyle: 'short'
                        });
                        // Il valore per il backend deve essere yyyy-mm-dd hh:mm:ss
                        const backendDateTime = new Date(info.dateStr).toISOString().slice(0, 19).replace('T', ' ');

                        startTimeInput.value = backendDateTime;
                        submitButton.disabled = false;
                        submitButton.innerText = `Conferma per le ${selectedDateTime}`;
                    }
                });
                calendar.render();
            });
        </script>
    @endpush
</x-app-layout>
