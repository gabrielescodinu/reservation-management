<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agenda Globale') }}
        </h2>
    </x-slot>

    @push('styles')
        <style>

        </style>
    @endpush

    <div x-data="{
        showEventModal: false,
        selectedEvent: {},
        handleEventClick(event) {
            this.selectedEvent = {
                id: event.id,
                start: event.start,
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

        <div x-show="showEventModal" @keydown.escape.window="showEventModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75" x-cloak x-transition>
            <div @click.away="showEventModal = false" class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-lg mx-4">
                <h3 class="text-lg font-semibold text-gray-900">Dettaglio Prenotazione</h3>
                <div class="mt-4 border-t pt-4 space-y-1">
                    <p class="text-sm text-gray-500">Servizio: <strong class="text-gray-800" x-text="selectedEvent.serviceName"></strong></p>
                    <p class="text-sm text-gray-500">Cliente: <strong class="text-gray-800" x-text="selectedEvent.customerName"></strong></p>
                    <p class="text-sm text-gray-500">Worker: <strong class="text-gray-800" x-text="selectedEvent.workerName"></strong></p>
                </div>
                <div class="mt-6 flex justify-end">
                    <template x-if="!selectedEvent.isPast">
                        <form :action="'/reservations/' + selectedEvent.id" method="POST" x-ref="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="$refs.deleteForm.submit()" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">Annulla Appuntamento</button>
                        </form>
                    </template>
                    <template x-if="selectedEvent.isPast">
                        <span class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-500 uppercase tracking-widest">Appuntamento Effettuato</span>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'it',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: '{{ route('superadmin.reservations.data') }}',
                    hiddenDays: [0],
                    slotMinTime: '08:00:00',
                    slotMaxTime: '19:00:00',
                    businessHours: {
                        daysOfWeek: [1, 2, 3, 4, 5, 6],
                        startTime: '08:00',
                        endTime: '19:00',
                    },
                    allDaySlot: false,
                    eventClick: function(info) {
                        info.jsEvent.preventDefault();
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
