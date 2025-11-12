@props(['type' => 'button', 'blue' => false])

@php
    $base = 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm focus:outline-none';
    $color = $blue ? 'text-white bg-indigo-600 hover:bg-indigo-700' : 'text-gray-700 bg-gray-100 hover:bg-gray-200';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "$base $color"]) }}>
    {{ $slot }}
</button>

