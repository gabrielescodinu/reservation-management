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

                        <div class="mt-4 sm:mt-0 text-right">
                            @if ($reservation->status === 'confirmed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Confermata
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Annullata
                                </span>
                            @endif
                            
                            {{-- Qui in futuro potremmo aggiungere un pulsante per annullare --}}
                            {{-- <div class="mt-2">
                                <form action="#" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-900">Annulla</button>
                                </form>
                            </div> --}}
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
            </div>
        </div>
    </div>
</x-app-layout>