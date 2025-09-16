<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Le Mie Prenotazioni') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                @forelse ($reservations as $reservation)
                    <div x-data="{ showConfirmModal: false }">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col sm:flex-row justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Prenotazione per il {{ \Carbon\Carbon::parse($reservation->start_time)->isoFormat('dddd D MMMM YYYY') }}
                                </p>
                                <h3 class="text-lg font-bold text-gray-900 mt-1">
                                    {{ $reservation->service->name }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    con {{ $reservation->service->worker->name }}
                                </p>
                                <p class="mt-2 font-semibold text-gray-800">
                                    Orario: {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}
                                </p>
                            </div>

                            {{-- in resources/views/customer/dashboard.blade.php --}}

                            <div class="mt-4 sm:mt-0 sm:text-right w-full sm:w-auto">

                                {{-- 1. Controlliamo prima di tutto se la data della prenotazione è passata --}}
                                @if (\Carbon\Carbon::parse($reservation->start_time)->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Effettuata
                                    </span>

                                    {{-- 2. Se non è passata, controlliamo lo stato come prima --}}
                                @elseif ($reservation->status === 'confirmed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Confermata
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Annullata
                                    </span>
                                @endif

                                <div class="mt-2">
                                    {{-- 3. BONUS: Mostriamo il pulsante "Annulla" solo per le prenotazioni future e confermate --}}
                                    @if ($reservation->status === 'confirmed' && \Carbon\Carbon::parse($reservation->start_time)->isFuture())
                                        <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" x-ref="deleteForm">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="showConfirmModal = true" class="text-xs text-red-600 hover:text-red-900 font-semibold">
                                                Annulla Prenotazione
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div x-show="showConfirmModal" @keydown.escape.window="showConfirmModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75" style="display: none;" x-cloak x-transition>
                            <div @click.away="showConfirmModal = false" class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-md mx-4">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>

                                <div class="mt-3 text-center sm:mt-5">
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900">Annullare Prenotazione?</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Sei sicuro di voler annullare il tuo appuntamento per <br> <span class="font-bold">"{{ $reservation->service->name }}"</span>? <br>L'azione è irreversibile.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-center sm:space-x-4">
                                    <button type="button" @click="showConfirmModal = false" class="mt-3 sm:mt-0 w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        No, mantieni
                                    </button>
                                    {{-- 5. Questo pulsante ora invia il form specifico usando x-ref --}}
                                    <button type="button" @click="$refs.deleteForm.submit()" class="w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                        Sì, annulla
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- ... (codice per quando non ci sono prenotazioni) ... --}}
                @endforelse

                {{-- Assicurati di avere questo stile da qualche parte (es. in app.blade.php o qui) --}}
                @push('styles')
                    <style>
                        [x-cloak] {
                            display: none !important;
                        }
                    </style>
                @endpush
            </div>
        </div>
    </div>
</x-app-layout>
