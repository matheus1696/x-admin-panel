@props([
    'value' => null,
    'icon' => null,
    'required' => false,
    'size' => 'sm',
])

@php
    $sizeClasses = [
        'xs' => 'text-[11px] gap-1 pb-1 pl-1',
        'sm' => 'text-xs gap-1.5 pb-1.5 pl-1.5',
        'md' => 'text-sm gap-1.5 pb-2 pl-1.5',
    ];

    $classes = $sizeClasses[$size] ?? $sizeClasses['sm'];
@endphp

<label {{ $attributes->merge(['class' => "flex items-center font-medium text-gray-700 {$classes}"]) }}>
    @if ($icon)
        <i class="{{ $icon }} text-[0.95em] text-emerald-700"></i>
    @endif

    <span>{{ $value ?? $slot }}</span>

    @if ($required)
        <span class="text-red-600" aria-hidden="true">*</span>
    @endif
</label>
