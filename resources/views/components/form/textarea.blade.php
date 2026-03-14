@props([
    'disabled' => false,
    'name' => null,
    'rows' => 6,
    'variant' => 'default',
    'size' => 'xs',
    'withIcon' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'borderColor' => 'green',
    'rounded' => 'lg',
    'shadow' => true,
    'loading' => false,
])

@php
    $normalizeBoolean = static fn (mixed $value): bool => filter_var($value, FILTER_VALIDATE_BOOLEAN) || $value === true || $value === 1 || $value === '1';

    $disabled = $normalizeBoolean($disabled);
    $withIcon = $normalizeBoolean($withIcon);
    $shadow = $normalizeBoolean($shadow);
    $loading = $normalizeBoolean($loading);

    $sizeConfig = [
        'xs' => [
            'input' => 'text-[12px] px-3 py-2',
            'icon' => 'text-[12px]',
            'iconPadding' => 'pl-8',
            'iconRightPadding' => 'pr-8',
        ],
        'sm' => [
            'input' => 'text-sm px-3 py-2.5',
            'icon' => 'text-sm',
            'iconPadding' => 'pl-9',
            'iconRightPadding' => 'pr-9',
        ],
        'md' => [
            'input' => 'text-sm px-4 py-3',
            'icon' => 'text-sm',
            'iconPadding' => 'pl-10',
            'iconRightPadding' => 'pr-10',
        ],
        'lg' => [
            'input' => 'text-base px-4 py-3.5',
            'icon' => 'text-base',
            'iconPadding' => 'pl-11',
            'iconRightPadding' => 'pr-11',
        ],
    ];

    $currentSize = $sizeConfig[$size] ?? $sizeConfig['md'];

    $roundedConfig = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        'full' => 'rounded-full',
    ];

    $roundedClass = $roundedConfig[$rounded] ?? $roundedConfig['md'];

    if ($variant === 'pills') {
        $roundedClass = 'rounded-full';
    }

    $colorConfig = [
        'green' => [
            'base' => 'border-gray-300 focus:border-emerald-700 focus:ring-emerald-700/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-emerald-50 border-emerald-200 focus:bg-white',
            'icon' => 'text-emerald-600',
        ],
        'blue' => [
            'base' => 'border-gray-300 focus:border-blue-500 focus:ring-blue-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-blue-50 border-blue-200 focus:bg-white',
            'icon' => 'text-blue-600',
        ],
        'purple' => [
            'base' => 'border-gray-300 focus:border-purple-500 focus:ring-purple-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-purple-50 border-purple-200 focus:bg-white',
            'icon' => 'text-purple-600',
        ],
        'red' => [
            'base' => 'border-gray-300 focus:border-red-500 focus:ring-red-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-red-50 border-red-200 focus:bg-white',
            'icon' => 'text-red-600',
        ],
        'yellow' => [
            'base' => 'border-gray-300 focus:border-yellow-500 focus:ring-yellow-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-yellow-50 border-yellow-200 focus:bg-white',
            'icon' => 'text-yellow-600',
        ],
        'gray' => [
            'base' => 'border-gray-300 focus:border-gray-500 focus:ring-gray-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-gray-50 border-gray-200 focus:bg-white',
            'icon' => 'text-gray-600',
        ],
        'sky' => [
            'base' => 'border-gray-300 focus:border-sky-500 focus:ring-sky-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-sky-50 border-sky-200 focus:bg-white',
            'icon' => 'text-sky-600',
        ],
        'indigo' => [
            'base' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-indigo-50 border-indigo-200 focus:bg-white',
            'icon' => 'text-indigo-600',
        ],
    ];

    $currentColor = $colorConfig[$borderColor] ?? $colorConfig['green'];

    $variantConfig = [
        'default' => [
            'base' => "bg-white backdrop-blur-sm {$currentColor['base']} shadow-sm hover:shadow focus:shadow-md transition-all duration-200",
            'error' => "bg-white backdrop-blur-sm {$currentColor['error']} text-red-700 placeholder-red-400 shadow-sm",
        ],
        'outline' => [
            'base' => "bg-transparent border {$currentColor['base']} hover:bg-white/50 hover:shadow-sm transition-all duration-200",
            'error' => "bg-transparent border {$currentColor['error']} text-red-700 placeholder-red-400",
        ],
        'filled' => [
            'base' => "{$currentColor['filled']} shadow-sm hover:bg-white/90 focus:shadow-md transition-all duration-200",
            'error' => 'bg-red-50 border border-red-300 text-red-700 placeholder-red-400',
        ],
        'minimal' => [
            'base' => "bg-transparent border-0 {$currentColor['base']} focus:ring-0 px-0 focus:bg-white transition-colors duration-200",
            'error' => 'bg-transparent border-0 text-red-600 placeholder-red-400 focus:ring-0 px-0',
        ],
        'glass' => [
            'base' => "bg-white/20 backdrop-blur-lg border border-white/30 {$currentColor['base']} hover:bg-white/30 hover:backdrop-blur-xl transition-all duration-300",
            'error' => 'bg-red-50/20 backdrop-blur-lg border border-red-300/30 text-red-700 placeholder-red-400',
        ],
        'pills' => [
            'base' => "bg-gray-50 border border-gray-200 {$currentColor['base']} hover:bg-white transition-all duration-200",
            'error' => 'bg-red-50 border border-red-300 text-red-700 placeholder-red-400',
        ],
    ];

    $currentVariant = $variantConfig[$variant] ?? $variantConfig['default'];

    $baseClasses = 'w-full resize-y focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500 disabled:opacity-50';
    $sizeClass = $currentSize['input'];
    $variantClass = $errors->has($name) && ! $disabled ? $currentVariant['error'] : $currentVariant['base'];
    $shadowClass = ! $shadow ? 'shadow-none hover:shadow-none focus:shadow-none' : '';
    $iconClass = '';

    if (($withIcon && $icon) || $loading) {
        $iconClass = $iconPosition === 'right' || $loading
            ? $currentSize['iconRightPadding']
            : $currentSize['iconPadding'];
    }

    $classes = trim(implode(' ', [
        $baseClasses,
        $sizeClass,
        $roundedClass,
        $variantClass,
        $shadowClass,
        $iconClass,
    ]));
@endphp

<div class="relative w-full">
    @if ($withIcon && $icon && $iconPosition === 'left')
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-start pl-3 pt-3">
            <i class="{{ $icon }} {{ $currentSize['icon'] }} {{ $currentColor['icon'] }}"></i>
        </div>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        @disabled($disabled || $loading)
        {{ $attributes->merge(['class' => $classes]) }}
    ></textarea>

    @if ($withIcon && $icon && $iconPosition === 'right' && ! $loading)
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-start pr-3 pt-3">
            <i class="{{ $icon }} {{ $currentSize['icon'] }} {{ $currentColor['icon'] }}"></i>
        </div>
    @endif

    @if ($loading)
        <div class="absolute inset-y-0 right-0 flex items-start pr-3 pt-3">
            <i class="fas fa-spinner fa-spin {{ $currentSize['icon'] }} {{ $currentColor['icon'] }}"></i>
        </div>
    @endif
</div>
