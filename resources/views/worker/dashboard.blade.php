<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('La Mia Agenda') }}
        </h2>
    </x-slot>

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
                // La rotta da cui caricare gli eventi
                // ATTENZIONE: Assicurati di usare la rotta corretta (web o api)
                // Se usi la rotta api, l'autenticazione è diversa.
                // Per un approccio più semplice, sposta la logica della rotta api in una rotta web.
                events: '{{ route("worker.reservations.data") }}'
                // Per usare una rotta web, avrai bisogno di un token API o di usare cookie-based auth
            });
            calendar.render();
        });
    </script>
    @endpush
</x-app-layout>