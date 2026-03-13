@props([
    'disabled' => false,
    'name' => null,
    'rows' => 6,
    'variant' => 'default',
    'size' => 'xs',
    'borderColor' => 'green',
    'rounded' => 'lg',
    'shadow' => true,
    'loading' => false,
])

@php
    $sizeConfig = [
        'xs' => 'text-[12px] px-3 py-2',
        'sm' => 'text-sm px-3 py-2.5',
        'md' => 'text-sm px-4 py-3',
        'lg' => 'text-base px-4 py-3.5',
    ];

    $roundedConfig = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        'full' => 'rounded-3xl',
    ];

    if ($variant === 'pills') {
        $rounded = 'full';
    }

    $colorConfig = [
        'green' => [
            'base' => 'border-gray-300 focus:border-emerald-700 focus:ring-emerald-700/30',
            'filled' => 'bg-emerald-50 border-emerald-200 focus:bg-white',
        ],
        'blue' => [
            'base' => 'border-gray-300 focus:border-blue-500 focus:ring-blue-500/30',
            'filled' => 'bg-blue-50 border-blue-200 focus:bg-white',
        ],
        'purple' => [
            'base' => 'border-gray-300 focus:border-purple-500 focus:ring-purple-500/30',
            'filled' => 'bg-purple-50 border-purple-200 focus:bg-white',
        ],
        'red' => [
            'base' => 'border-gray-300 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-red-50 border-red-200 focus:bg-white',
        ],
        'yellow' => [
            'base' => 'border-gray-300 focus:border-yellow-500 focus:ring-yellow-500/30',
            'filled' => 'bg-yellow-50 border-yellow-200 focus:bg-white',
        ],
        'gray' => [
            'base' => 'border-gray-300 focus:border-gray-500 focus:ring-gray-500/30',
            'filled' => 'bg-gray-50 border-gray-200 focus:bg-white',
        ],
        'sky' => [
            'base' => 'border-gray-300 focus:border-sky-500 focus:ring-sky-500/30',
            'filled' => 'bg-sky-50 border-sky-200 focus:bg-white',
        ],
        'indigo' => [
            'base' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500/30',
            'filled' => 'bg-indigo-50 border-indigo-200 focus:bg-white',
        ],
    ];

    $currentColor = $colorConfig[$borderColor] ?? $colorConfig['green'];

    $variantConfig = [
        'default' => [
            'base' => "bg-white backdrop-blur-sm {$currentColor['base']} shadow-sm hover:shadow focus:shadow-md transition-all duration-200",
            'error' => 'bg-white backdrop-blur-sm border-red-400 text-red-700 placeholder-red-400 focus:border-red-500 focus:ring-red-500/30 shadow-sm',
        ],
        'outline' => [
            'base' => "bg-transparent border {$currentColor['base']} hover:bg-white/50 hover:shadow-sm transition-all duration-200",
            'error' => 'bg-transparent border border-red-400 text-red-700 placeholder-red-400 focus:border-red-500 focus:ring-red-500/30',
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

    $baseClasses = 'w-full resize-y focus:outline-none focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500';
    $sizeClass = $sizeConfig[$size] ?? $sizeConfig['xs'];
    $roundedClass = $roundedConfig[$rounded] ?? $roundedConfig['lg'];
    $variantClass = $errors->has($name) && !$disabled ? $variantConfig[$variant]['error'] ?? $variantConfig['default']['error'] : $variantConfig[$variant]['base'] ?? $variantConfig['default']['base'];
    $shadowClass = $shadow ? '' : 'shadow-none hover:shadow-none focus:shadow-none';
@endphp

<textarea
    name="{{ $name }}"
    id="{{ $name }}"
    rows="{{ $rows }}"
    @disabled($disabled || $loading)
    {{ $attributes->merge([
        'class' => "{$baseClasses} {$sizeClass} {$roundedClass} {$variantClass} {$shadowClass}"
    ]) }}
></textarea>
