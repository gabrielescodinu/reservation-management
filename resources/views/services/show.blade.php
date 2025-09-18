<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $service->name }}
        </h2>
    </x-slot>

    <style>
        .fc-timegrid-slot-lane:not(.fc-slot-past) {
            cursor: pointer;
        }

        .fc-timegrid-slot-lane:not(.fc-slot-past):hover {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .fc-daygrid-day.fc-day-future {
            cursor: pointer;
        }
    </style>

    {{-- Inizializziamo Alpine.js per gestire lo stato della modale --}}
    <div x-data="{
        showModal: false,
        selectedDateTime: '',
        backendDateTime: '',
        handleBookingClick(event) {
            this.backendDateTime = event.detail.backend;
            this.selectedDateTime = event.detail.friendly;
            this.showModal = true;
        }
    }" @open-booking-modal.window="handleBookingClick($event)" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Sostituisci la vecchia card con questa in services/show.blade.php --}}
                <div class="md:col-span-1 bg-white p-6 rounded-2xl border border-gray-200 h-fit">

                    {{-- Sezione Header: Icona e Nome Worker --}}
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Offerto da</p>
                            <p class="font-semibold text-gray-800">{{ $service->worker->name }}</p>
                        </div>
                    </div>

                    {{-- Dettagli Principali del Servizio --}}
                    <div class="mt-5">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $service->name }}</h3>
                        <p class="mt-2 text-2xl font-bold text-gray-800">{{ number_format($service->price, 2, ',', '.') }} €</p>
                        <div class="mt-3">
                            <span class="bg-indigo-100 text-indigo-700 text-xs font-medium px-2.5 py-1 rounded-full">{{ $service->duration }} min</span>
                        </div>
                        <p class="mt-4 text-sm text-gray-600">{{ $service->description }}</p>
                    </div>

                    {{-- Istruzioni per la Prenotazione --}}
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <p class="text-sm text-blue-600 font-semibold">Clicca su uno slot orario disponibile nel calendario per prenotare.</p>
                    </div>
                </div>

                <div class="md:col-span-2 bg-white p-6 shadow-sm sm:rounded-lg">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <div x-show="showModal" @keydown.escape.window="showModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75" style="display: none;" x-cloak {{-- Transizione per l'effetto di fade dello sfondo --}} x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="showModal = false" class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-md mx-4" {{-- Transizione per l'effetto "pop" della modale --}} x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>

                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg font-semibold leading-6 text-gray-900">Conferma Prenotazione</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Stai per prenotare il servizio <br>
                            <span class="font-bold text-gray-700">"{{ $service->name }}"</span>
                        </p>
                        <p class="mt-4 text-sm text-gray-500">
                            Data e ora: <br>
                            <strong class="font-bold text-gray-700" x-text="selectedDateTime"></strong>
                        </p>
                    </div>
                </div>

                <form action="{{ route('reservations.store') }}" method="POST" class="mt-6">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                    <input type="hidden" name="start_time" :value="backendDateTime">

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-center sm:space-x-4">
                        <button type="button" @click="showModal = false" class="mt-3 sm:mt-0 w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annulla</button>
                        <x-primary-button class="w-full justify-center">Conferma Prenotazione</x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Aggiungi questo al @push('styles') per evitare un flash della modale al caricamento della pagina --}}
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
        {{-- Includiamo Alpine.js (anche se è già in app.js, lo forziamo per sicurezza) --}}
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script>
            // La funzione di Alpine.js è globale, quindi il nostro script la può usare
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    locale: 'it',
                    headerToolbar: {
                        /* ... */
                    },
                    events: '{{ route('services.reservations', $service) }}',
                    validRange: {
                        start: new Date().toISOString().split('T')[0]
                    },

                    // --- NUOVE OPZIONI PER GLI ORARI DI LAVORO ---

                    // 1. Nasconde la domenica
                    hiddenDays: [0], // 0 = Domenica, 1 = Lunedì, ..., 6 = Sabato

                    // 2. Imposta l'inizio e la fine della griglia oraria visibile
                    slotMinTime: '08:00:00',
                    slotMaxTime: '19:00:00',

                    // 3. Evidenzia le ore lavorative (opzionale ma consigliato)
                    businessHours: {
                        daysOfWeek: [1, 2, 3, 4, 5, 6], // Da Lunedì a Sabato
                        startTime: '08:00',
                        endTime: '19:00',
                    },

                    // 4. Rimuove la riga "tutto il giorno" in cima al calendario
                    allDaySlot: false,

                    // --- MODIFICA CHIAVE QUI ---
                    dateClick: function(info) {
                        const clickedDate = new Date(info.dateStr);

                        // Prepara i dati come prima
                        const backendDateTime = `${clickedDate.getFullYear()}-${String(clickedDate.getMonth() + 1).padStart(2, '0')}-${String(clickedDate.getDate()).padStart(2, '0')} ${String(clickedDate.getHours()).padStart(2, '0')}:${String(clickedDate.getMinutes()).padStart(2, '0')}:${String(clickedDate.getSeconds()).padStart(2, '0')}`;
                        const userFriendlyDateTime = clickedDate.toLocaleString('it-IT', {
                            dateStyle: 'long',
                            timeStyle: 'short'
                        });

                        // --- NUOVA LOGICA: LANCIA UN EVENTO PERSONALIZZATO ---
                        const event = new CustomEvent('open-booking-modal', {
                            bubbles: true, // Permette all'evento di "risalire" nel DOM
                            detail: { // I dati che vogliamo inviare
                                backend: backendDateTime,
                                friendly: userFriendlyDateTime
                            }
                        });
                        // Lancia l'evento dall'elemento del calendario
                        calendarEl.dispatchEvent(event);
                    }
                });
                calendar.render();
            });
        </script>
    @endpush
</x-app-layout>
