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
                    {{-- in resources/views/services/index.blade.php --}}

                    {{-- Sostituisci la vecchia card con questa --}}
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex justify-between items-start">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                                        {{-- Icona placeholder per il worker --}}
                                        <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $service->worker->name }}</p>
                                        <p class="text-xs text-gray-500">Professionista</p>
                                    </div>
                                </div>
                                {{-- Qui in futuro potresti mettere un'icona "Salva" --}}
                            </div>

                            <div class="mt-5">
                                <h3 class="text-lg font-bold text-gray-900">{{ $service->name }}</h3>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="bg-indigo-100 text-indigo-700 text-xs font-medium px-2.5 py-1 rounded-full">{{ $service->duration }} min</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <p class="text-lg font-bold text-gray-900">{{ number_format($service->price, 2, ',', '.') }} €</p>

                            <div class="flex items-center space-x-3">
                                {{-- Mostra il pulsante Modifica solo se l'utente ha il permesso --}}
                                @can('update', $service)
                                    <a href="{{ route('services.edit', $service) }}" class="text-sm font-medium text-gray-500 hover:text-gray-800">
                                        Modifica
                                    </a>
                                @endcan

                                <a href="{{ route('services.show', $service) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Prenota
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
