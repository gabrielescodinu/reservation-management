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
                    {{-- Ogni card ha il suo stato Alpine.js per gestire la modale di cancellazione --}}
                    <div x-data="{ showConfirmModal: false }">
                        <div class="bg-white p-6 rounded-2xl border border-gray-200 flex flex-col justify-between h-full">

                            {{-- Sezione Superiore: Dettagli del servizio e stato --}}
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-indigo-600">{{ \Carbon\Carbon::parse($reservation->start_time)->isoFormat('dddd D MMMM YYYY') }}</p>
                                    <h3 class="text-lg font-bold text-gray-900 mt-1">
                                        {{ $reservation->service->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        con {{ $reservation->service->worker->name }}
                                    </p>
                                </div>
                                <div>
                                    @if (\Carbon\Carbon::parse($reservation->start_time)->isPast())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Effettuata
                                        </span>
                                    @elseif ($reservation->status === 'confirmed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Confermata
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Annullata
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Sezione Inferiore: Orario e Azioni --}}
                            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="font-semibold text-gray-800">
                                        Ore: {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}
                                    </p>
                                </div>

                                <div>
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

                        {{-- Modale di Conferma Cancellazione (assicurati che sia qui) --}}
                        <div x-show="showConfirmModal" @keydown.escape.window="showConfirmModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75" style="display: none;" x-cloak x-transition>
                            <div @click.away="showConfirmModal = false" class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-md mx-4">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-5">
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900">Annullare Prenotazione?</h3>
                                    <p class="mt-2 text-sm text-gray-500">Sei sicuro di voler annullare questo appuntamento? L'azione è irreversibile.</p>
                                </div>
                                <div class="mt-6 flex justify-center space-x-4">
                                    <button type="button" @click="showConfirmModal = false" class="px-4 py-2 bg-white text-gray-900 rounded-md ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Mantieni</button>
                                    <button type="button" @click="$refs.deleteForm.submit()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500">Sì, annulla</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <p class="text-gray-600 text-center">Non hai ancora nessuna prenotazione.</p>
                        <div class="mt-4 text-center">
                            <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Scopri i nostri servizi
                            </a>
                        </div>
                    </div>
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
