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
        'blue'   => 'bg-blue-600 hover:bg-blue-700 text-white pl-4 pr-2.5 py-2.5 rounded-lg shadow',
        'indigo' => 'bg-indigo-600 hover:bg-indigo-700 text-white pl-4 pr-2.5 py-2.5 rounded-lg shadow',
        'purple' => 'bg-purple-600 hover:bg-purple-700 text-white pl-4 pr-2.5 py-2.5 rounded-lg shadow',
        
        'gray_outline'   => 'text-gray-500 hover:text-gray-400',
        'sky_outline'    => 'text-sky-600 hover:text-sky-500',
        'green_outline'  => 'text-green-700 hover:text-green-800',
        'yellow_outline' => 'text-yellow-600 hover:text-yellow-500',
        'red_outline'    => 'text-red-600 hover:text-red-500',
        'blue_outline'   => 'text-blue-600 hover:text-blue-500',

        'light'    => 'bg-gray-100 hover:bg-gray-200 text-gray-700 pl-4 pr-2.5 py-2.5 rounded-lg',
        'dark'     => 'bg-gray-900 hover:bg-gray-800 text-white pl-4 pr-2.5 py-2.5 rounded-lg shadow',
        'white'    => 'bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 pl-4 pr-2.5 py-2.5 rounded-lg shadow-sm',
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

