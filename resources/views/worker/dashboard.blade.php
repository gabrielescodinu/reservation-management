<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('La Mia Agenda') }}
        </h2>
    </x-slot>

    <style>
        /* Stile base per tutti gli eventi */
        .fc-event {
            border-width: 2px !important;
            /* Forza il bordo più spesso */
            padding: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .fc-event:hover {
            opacity: 0.8;
        }

        /* Palette di colori */
        .event-blue {
            background-color: rgba(59, 130, 246, 0.1) !important;
            border-color: rgba(59, 130, 246, 1) !important;
            color: #333 !important;
            /* Colore del testo */
        }

        .event-green {
            background-color: rgba(16, 185, 129, 0.1) !important;
            border-color: rgba(16, 185, 129, 1) !important;
            color: #333 !important;
        }

        .event-purple {
            background-color: rgba(139, 92, 246, 0.1) !important;
            border-color: rgba(139, 92, 246, 1) !important;
            color: #333 !important;
        }

        .event-orange {
            background-color: rgba(249, 115, 22, 0.1) !important;
            border-color: rgba(249, 115, 22, 1) !important;
            color: #333 !important;
        }
    </style>

    <div class="py-12">
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
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth', // Vista mensile di default
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay' // Viste disponibili
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
                    
                    // La rotta da cui caricare gli eventi
                    // ATTENZIONE: Assicurati di usare la rotta corretta (web o api)
                    // Se usi la rotta api, l'autenticazione è diversa.
                    // Per un approccio più semplice, sposta la logica della rotta api in una rotta web.
                    events: '{{ route('worker.reservations.data') }}'
                    // Per usare una rotta web, avrai bisogno di un token API o di usare cookie-based auth
                });
                calendar.render();
            });
        </script>
    @endpush
</x-app-layout>
