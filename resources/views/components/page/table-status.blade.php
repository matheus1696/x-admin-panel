@props([ 
    'condition' => false,
    'trueText' => 'Ativo',
    'falseText' => 'Inativo',
])

@php
    $statusConfig = $condition ? [
        'bg' => 'bg-emerald-100',
        'text' => 'text-emerald-700',
        'icon' => 'fas fa-check-circle',
        'dot' => 'bg-emerald-500',
    ] : [
        'bg' => 'bg-red-100',
        'text' => 'text-red-700',
        'icon' => 'fas fa-times-circle',
        'dot' => 'bg-red-500',
    ];
@endphp

<div class="flex justify-center">
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
        <i class="{{ $statusConfig['icon'] }} text-xs"></i>
        {{ $condition ? $trueText : $falseText }}
    </span>
</div>