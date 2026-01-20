@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'green',
    'icon' => null,
    'text' => null,
])

@php
    $baseClasses = 'w-full text-xs font-medium transition-all duration-200
    flex items-center justify-center gap-1  transform';

    $variants = [
        'green' => 'bg-green-700 hover:bg-green-800 text-white pl-4 pr-2.5 py-2.5 rounded-lg shadow',
        'gray' => 'bg-gray-600 hover:bg-gray-700 text-white pl-4 pr-2.5 py-2.5 rounded-lg shadow',
        'yellow' => 'bg-yellow-500 hover:bg-yellow-600 text-white pl-4 pr-2.5 py-2.5 rounded-lg shadow',
        'red' => 'bg-red-600 hover:bg-red-700 text-white pl-4 pr-2.5 py-2.5 rounded-lg shadow',
        'sky' => 'bg-sky-600 hover:bg-sky-700 text-white pl-4 pr-2.5 py-2.5 rounded-lg shadow',
        
        'gray_outline' => 'text-gray-500 hover:text-gray-400',
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
        @if ($icon)<i class="{{ $icon }}"></i>@endif
        <span>{{ $text ?? $slot }}</span>
    </button>
@endif

