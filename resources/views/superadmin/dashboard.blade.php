<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tutte le Prenotazioni') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servizio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data e Ora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($reservations as $reservation)
                                <tr x-data="{ showConfirmModal: false }">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reservation->customer->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reservation->service->worker->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $reservation->service->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($reservation->start_time)->isoFormat('D MMM YYYY, HH:mm') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if (\Carbon\Carbon::parse($reservation->start_time)->isPast())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Effettuata</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Confermata</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        @if (\Carbon\Carbon::parse($reservation->start_time)->isFuture())
                                            <button @click="showConfirmModal = true" class="text-red-600 hover:text-red-900 font-semibold">Annulla</button>
                                        @endif

                                        <div x-show="showConfirmModal" @keydown.escape.window="showConfirmModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75" x-cloak x-transition>
                                            <div @click.away="showConfirmModal = false" class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-md mx-4">
                                                <h3 class="text-lg text-center font-semibold text-gray-900">Annullare Prenotazione?</h3>
                                                <p class="mt-2 text-center text-sm text-gray-500">Sei sicuro di voler annullare questo appuntamento? L'azione è irreversibile.</p>
                                                <div class="mt-6 flex justify-center space-x-4">
                                                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" @click="showConfirmModal = false" class="px-4 py-2 bg-white text-gray-900 rounded-md ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Mantieni</button>
                                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500">Sì, annulla</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $reservations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
