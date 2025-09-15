<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Scopri i Nostri Servizi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($services as $service)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900">{{ $service->name }}</h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Offerto da: <span class="font-semibold">{{ $service->worker->name }}</span>
                            </p>
                            <p class="mt-2 text-gray-800 font-bold text-xl">{{ number_format($service->price, 2, ',', '.') }} €</p>
                            <p class="mt-1 text-sm text-gray-500">Durata: {{ $service->duration }} minuti</p>
                            
                            <div class="mt-4">
                                <a href="{{ route('services.show', $service) }}" class="w-full text-center inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Visualizza e Prenota
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-700">Al momento non ci sono servizi disponibili.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>