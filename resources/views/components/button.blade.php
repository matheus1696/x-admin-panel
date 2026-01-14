@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'green',
    'icon' => null,
    'text' => null,
])

@php
    $baseClasses = 'w-full text-xs px-4 py-2.5 rounded-lg font-medium transition-all duration-200
    flex items-center justify-center gap-1 shadow transform';

    $variants = [
        'green' => 'bg-green-600 hover:bg-green-700 text-white',
        'gray' => 'bg-gray-600 hover:bg-gray-700 text-white',
        'yellow' => 'bg-yellow-600 hover:bg-yellow-700 text-white',
        'red' => 'bg-red-600 hover:bg-red-700 text-white',
        'sky' => 'bg-sky-600 hover:bg-sky-700 text-white',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['green']);
@endphp

@if ($href != null)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon) <i class="{{ $icon }}"></i> @endif
        <span>{{ $text ?? $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} >
        @if ($icon) <i class="{{ $icon }}"></i> @endif
        <span>{{ $text ?? $slot }}</span>
    </button>
@endif

