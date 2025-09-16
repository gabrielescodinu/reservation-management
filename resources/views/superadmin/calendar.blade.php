<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agenda Globale') }}
        </h2>
    </x-slot>

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            /* Stile base per gli eventi cliccabili */
            .fc-event {
                border-width: 2px !important;
                padding: 5px;
                cursor: pointer;
                transition: opacity 0.2s ease;
            }

            .fc-event:hover {
                opacity: 0.8;
            }

            /* Palette di colori per gli eventi */
            .fc-event.event-blue {
                background-color: rgba(59, 130, 246, 0.1) !important;
                border-color: rgba(59, 130, 246, 1) !important;
            }

            .fc-event.event-green {
                background-color: rgba(16, 185, 129, 0.1) !important;
                border-color: rgba(16, 185, 129, 1) !important;
            }

            .fc-event.event-purple {
                background-color: rgba(139, 92, 246, 0.1) !important;
                border-color: rgba(139, 92, 246, 1) !important;
            }

            .fc-event.event-orange {
                background-color: rgba(249, 115, 22, 0.1) !important;
                border-color: rgba(249, 115, 22, 1) !important;
            }

            /* Regola per rendere il testo scuro e leggibile nelle viste week/day */
            .fc-v-event .fc-event-title,
            .fc-v-event .fc-event-time {
                color: #1f2937 !important;
            }
        </style>
    @endpush

    <div x-data="{ ... }" @open-event-modal.window="..." class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div id='calendar'></div>
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
                    initialView: 'timeGridWeek', // Vista settimanale, più utile per un'agenda
                    locale: 'it',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    // ... (Configurazione del calendario: initialView, locale, header, etc.)
                    // IMPORTANTE: cambia la fonte degli eventi
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
                        // (Logica per aprire la modale, identica a quella del worker)
                    }
                });
                calendar.render();
            });
        </script>
    @endpush
</x-app-layout>
