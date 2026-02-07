@props([
    'icon' => 'fas fa-tasks',
    'color' => 'green',
    'title' => 'Título da Página',
    'subtitle' => 'Subtítulo da Página',
    'button' => null,
    'compact' => true,
])

@php
    $colorConfig = [
        'green' => [
            'iconBg' => 'bg-gradient-to-br from-green-600 to-emerald-700',
            'iconText' => 'text-white',
            'accent' => 'from-green-600/10 to-emerald-700/5',
        ],
        'blue' => [
            'iconBg' => 'bg-gradient-to-br from-blue-500 to-indigo-600',
            'iconText' => 'text-white',
            'accent' => 'from-blue-500/10 to-indigo-500/5',
        ],
        // ... outras cores (mesmo mapeamento)
    ];
    
    $config = $colorConfig[$color] ?? $colorConfig['green'];
@endphp

@if($compact)
<!-- Compact Header -->
<div class="flex items-center justify-between gap-4 mt-6 md:mt-0 mb-6 p-4 rounded-xl bg-gradient-to-r {{ $config['accent'] }} border border-gray-200/50">
    <div class="flex items-center gap-3">
        <div class="{{ $config['iconBg'] }} {{ $config['iconText'] }} size-10 rounded-lg flex items-center justify-center shadow-sm">
            <i class="{{ $icon }} text-lg"></i>
        </div>
        <div>
            <h1 class="text-lg font-semibold text-gray-900">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-sm text-gray-600">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    
    @if($button)
        <div>
            {{ $button }}
        </div>
    @endif
</div>
@endif