@props(['active'])

@php
    $classes = $active ?? false ? 'flex items-center px-3 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg' : 'flex items-center px-3 py-2 text-sm font-medium text-slate-300 rounded-lg hover:bg-gray-700 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{-- Icona --}}
    <span class="mr-3">
        {{ $icon }}
    </span>
    {{-- Testo --}}
    <span>
        {{ $slot }}
    </span>
</a>
