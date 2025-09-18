<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('La Mia Agenda') }}
        </h2>
    </x-slot>

    {{-- 1. Inizializziamo Alpine.js per gestire la modale --}}
    <div x-data="{
        showEventModal: false,
        selectedEvent: {},
        handleEventClick(event) {
            this.selectedEvent = {
                id: event.id,
                title: event.title,
                start: event.start.toLocaleString('it-IT', { dateStyle: 'long', timeStyle: 'short' }),
                // Aggiungiamo una proprietà 'isPast' per controllare se l'evento è nel passato.
                isPast: event.start < new Date(),
                ...event.extendedProps
            };
            this.showEventModal = true;
        }
    }" @open-event-modal.window="handleEventClick($event.detail)" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>

        {{-- 2. HTML della Modale di Dettaglio/Cancellazione --}}
        <div x-show="showEventModal" @keydown.escape.window="showEventModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75" x-cloak x-transition>
            <div @click.away="showEventModal = false" class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-lg mx-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Dettaglio Prenotazione</h3>
                    <button @click="showEventModal = false">
                        <svg class="h-6 w-6 text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4 border-t pt-4 space-y-1">
                    <p class="text-sm text-gray-500">Servizio: <strong class="text-gray-800" x-text="selectedEvent.serviceName"></strong></p>
                    <p class="text-sm text-gray-500">Cliente: <strong class="text-gray-800" x-text="selectedEvent.customerName"></strong></p>
                    <p class="text-sm text-gray-500">Quando: <strong class="text-gray-800" x-text="selectedEvent.start"></strong></p>
                    <p class="text-sm text-gray-500">Durata: <strong class="text-gray-800" x-text="selectedEvent.duration"></strong></p>
                    <p class="text-sm text-gray-500">Prezzo: <strong class="text-gray-800" x-text="selectedEvent.price"></strong></p>
                </div>
                <div class="mt-6 flex justify-end">
                    <template x-if="!selectedEvent.isPast">
                        <form :action="'/reservations/' + selectedEvent.id" method="POST" onsubmit="return confirm('Sei sicuro di voler annullare questa prenotazione?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                                Annulla Appuntamento
                            </button>
                        </form>
                    </template>

                    {{-- Mostra questo blocco se l'appuntamento È passato --}}
                    <template x-if="selectedEvent.isPast">
                        <span class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-500 uppercase tracking-widest">
                            Appuntamento Effettuato
                        </span>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
        {{-- Alpine.js viene già caricato da app.js, ma includerlo non crea problemi --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek', // Vista settimanale, più utile per un'agenda
                    locale: 'it',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: '{{ route('worker.reservations.data') }}',
                    hiddenDays: [0],
                    slotMinTime: '08:00:00',
                    slotMaxTime: '19:00:00',
                    businessHours: {
                        daysOfWeek: [1, 2, 3, 4, 5, 6],
                        startTime: '08:00',
                        endTime: '19:00',
                    },
                    allDaySlot: false,

                    // 3. Funzione che si attiva al click su un evento esistente
                    eventClick: function(info) {
                        info.jsEvent.preventDefault(); // Evita comportamenti di default

                        // Lancia l'evento personalizzato per Alpine.js
                        const event = new CustomEvent('open-event-modal', {
                            bubbles: true,
                            detail: info.event
                        });
                        calendarEl.dispatchEvent(event);
                    }
                });
                calendar.render();
            });
        </script>
    @endpush
</x-app-layout>
