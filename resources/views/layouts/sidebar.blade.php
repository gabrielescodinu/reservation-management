<div class="flex flex-shrink-0">
    {{-- Inizializziamo Alpine.js per gestire lo stato dei sottomenu --}}
    <div x-data="{ activeSection: '' }" class="flex flex-col w-64 bg-slate-900 text-slate-300">

        <div class="flex items-center h-16 flex-shrink-0 px-4 border-b border-slate-700">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <x-application-logo class="block h-9 w-auto fill-current text-white" />
                <span class="ml-3 font-semibold text-white">My App</span>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto">
            <div class="px-4 py-4 space-y-2">

                {{-- Dashboard Link --}}
                <x-side-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                    </x-slot>
                    {{ __('Dashboard') }}
                </x-side-nav-link>

                {{-- Menu Condizionale per Worker --}}
                @if(auth()->user()->role === 'worker')
                    <div class="space-y-2">
                        <button @click="activeSection = (activeSection === 'worker' ? '' : 'worker')" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-slate-300 rounded-lg hover:bg-slate-700 hover:text-white">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                <span>Workspace</span>
                            </div>
                            <svg class="h-5 w-5 transform transition-transform" :class="{'rotate-90': activeSection === 'worker'}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="activeSection === 'worker'" x-collapse class="pl-7 space-y-1">
                            <a href="{{ route('worker.dashboard') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('worker.dashboard') ? 'text-white' : 'text-slate-400 hover:text-white' }}">La Mia Agenda</a>
                            <a href="{{ route('services.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('services.index') ? 'text-white' : 'text-slate-400 hover:text-white' }}">Gestisci Servizi</a>
                        </div>
                    </div>
                @endif
                
                {{-- Menu Condizionale per Customer --}}
                @if(auth()->user()->role === 'customer')
                     <x-side-nav-link :href="route('services.index')" :active="request()->routeIs('services.index')">
                        <x-slot name="icon">
                           <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        </x-slot>
                        {{ __('Trova Servizi') }}
                    </x-side-nav-link>
                     <x-side-nav-link :href="route('customer.dashboard')" :active="request()->routeIs('customer.dashboard')">
                        <x-slot name="icon">
                           <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-1.5h5.25m-5.25 0h3m-3 0h-3m-1.5 0h5.25m-5.25 0h3m-3 0h-3m1.5-3h5.25m-5.25 0h3m-3 0h-3" /></svg>
                        </x-slot>
                        {{ __('Le Mie Prenotazioni') }}
                    </x-side-nav-link>
                @endif

                 <div class="pt-4 border-t border-slate-700 space-y-2">
                     <x-side-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                        <x-slot name="icon">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </x-slot>
                        {{ __('Account') }}
                    </x-side-nav-link>
                </div>
            </div>
        </nav>
    </div>
</div>