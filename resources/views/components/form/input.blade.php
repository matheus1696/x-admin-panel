@props([
    'disabled' => false,
    'name' => null,
    'variant' => 'default', // default, outline, filled, minimal, glass, pills
    'size' => 'sm', // xs, sm, md, lg
    'withIcon' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'borderColor' => 'green', // green, blue, purple, red, yellow, gray, sky, indigo
    'rounded' => 'md', // none, sm, md, lg, xl, 2xl, full
    'shadow' => true,
    'loading' => false,
])

@php
    // Sistema de tamanhos
    $sizeConfig = [
        'xs' => [
            'input' => 'text-xs px-2.5 py-1.5',
            'icon' => 'text-xs',
            'iconPadding' => 'pl-8',
            'iconRightPadding' => 'pr-8',
        ],
        'sm' => [
            'input' => 'text-sm px-3 py-2',
            'icon' => 'text-sm',
            'iconPadding' => 'pl-9',
            'iconRightPadding' => 'pr-9',
        ],
        'md' => [
            'input' => 'text-sm px-3.5 py-2.5',
            'icon' => 'text-sm',
            'iconPadding' => 'pl-10',
            'iconRightPadding' => 'pr-10',
        ],
        'lg' => [
            'input' => 'text-base px-4 py-3',
            'icon' => 'text-base',
            'iconPadding' => 'pl-11',
            'iconRightPadding' => 'pr-11',
        ],
    ];
    
    $currentSize = $sizeConfig[$size] ?? $sizeConfig['md'];
    
    // Sistema de rounded
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
    
    // Se variant for 'pills', força rounded-full
    if ($variant === 'pills') {
        $roundedClass = 'rounded-full';
    }
    
    // Sistema de cores
    $colorConfig = [
        'green' => [
            'base' => 'border-gray-300 focus:border-green-700 focus:ring-green-700/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-green-50 border-green-200 focus:bg-white',
            'text' => 'text-green-700',
            'icon' => 'text-green-600',
        ],
        'blue' => [
            'base' => 'border-gray-300 focus:border-blue-500 focus:ring-blue-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-blue-50 border-blue-200 focus:bg-white',
            'text' => 'text-blue-700',
            'icon' => 'text-blue-600',
        ],
        'purple' => [
            'base' => 'border-gray-300 focus:border-purple-500 focus:ring-purple-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-purple-50 border-purple-200 focus:bg-white',
            'text' => 'text-purple-700',
            'icon' => 'text-purple-600',
        ],
        'red' => [
            'base' => 'border-gray-300 focus:border-red-500 focus:ring-red-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-red-50 border-red-200 focus:bg-white',
            'text' => 'text-red-700',
            'icon' => 'text-red-600',
        ],
        'yellow' => [
            'base' => 'border-gray-300 focus:border-yellow-500 focus:ring-yellow-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-yellow-50 border-yellow-200 focus:bg-white',
            'text' => 'text-yellow-700',
            'icon' => 'text-yellow-600',
        ],
        'gray' => [
            'base' => 'border-gray-300 focus:border-gray-500 focus:ring-gray-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-gray-50 border-gray-200 focus:bg-white',
            'text' => 'text-gray-700',
            'icon' => 'text-gray-600',
        ],
        'sky' => [
            'base' => 'border-gray-300 focus:border-sky-500 focus:ring-sky-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-sky-50 border-sky-200 focus:bg-white',
            'text' => 'text-sky-700',
            'icon' => 'text-sky-600',
        ],
        'indigo' => [
            'base' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500/30',
            'error' => 'border-red-400 focus:border-red-500 focus:ring-red-500/30',
            'filled' => 'bg-indigo-50 border-indigo-200 focus:bg-white',
            'text' => 'text-indigo-700',
            'icon' => 'text-indigo-600',
        ],
    ];
    
    $currentColor = $colorConfig[$borderColor] ?? $colorConfig['green'];
    
    // Sistema de variantes
    $variantConfig = [
        'default' => [
            'base' => "bg-white/80 backdrop-blur-sm {$currentColor['base']} shadow-sm hover:shadow focus:shadow-md transition-all duration-200",
            'error' => "bg-white/80 backdrop-blur-sm {$currentColor['error']} shadow-sm",
        ],
        'outline' => [
            'base' => "bg-transparent border {$currentColor['base']} hover:bg-white/50 hover:shadow-sm transition-all duration-200",
            'error' => "bg-transparent border {$currentColor['error']}",
        ],
        'filled' => [
            'base' => "{$currentColor['filled']} shadow-sm hover:bg-white/90 focus:shadow-md transition-all duration-200",
            'error' => "bg-red-50 border border-red-300",
        ],
        'minimal' => [
            'base' => "bg-transparent border-0 border-b-2 border-gray-300 focus:border-{$borderColor}-500 focus:ring-0 px-0 focus:bg-transparent transition-colors duration-200",
            'error' => "bg-transparent border-0 border-b-2 border-red-500 text-red-600 focus:ring-0 px-0",
        ],
        'glass' => [
            'base' => "bg-white/20 backdrop-blur-lg border border-white/30 {$currentColor['base']} hover:bg-white/30 hover:backdrop-blur-xl transition-all duration-300",
            'error' => "bg-red-50/20 backdrop-blur-lg border border-red-300/30",
        ],
        'pills' => [
            'base' => "bg-gray-50 border border-gray-200 {$currentColor['base']} hover:bg-white transition-all duration-200",
            'error' => "bg-red-50 border border-red-300",
        ],
    ];
    
    $currentVariant = $variantConfig[$variant] ?? $variantConfig['default'];
    
    // Classes base
    $baseClasses = 'w-full focus:outline-none focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-600 disabled:text-gray-800';
    
    // Classes dinâmicas
    $sizeClass = $currentSize['input'];
    $variantClass = $errors->has($name) && !$disabled ? $currentVariant['error'] : $currentVariant['base'];
    $shadowClass = !$shadow ? 'shadow-none hover:shadow-none focus:shadow-none' : '';
    
    // Classes de ícone
    $iconClass = $withIcon ? ($iconPosition === 'left' ? $currentSize['iconPadding'] : $currentSize['iconRightPadding']) : '';
    
    // Classes finais
    $classes = $baseClasses . ' ' . $sizeClass . ' ' . $roundedClass . ' ' . $variantClass . ' ' . $shadowClass . ' ' . $iconClass;
@endphp

<div class="relative w-full">
    @if($withIcon && $icon && $iconPosition === 'left')
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="{{ $icon }} {{ $currentSize['icon'] }} {{ $currentColor['icon'] }}"></i>
        </div>
    @endif
    
    <input
        name="{{ $name }}"
        id="{{ $name }}"
        @disabled($disabled || $loading)
        {{ $attributes->merge(['class' => $classes]) }}
    >
    
    @if($withIcon && $icon && $iconPosition === 'right')
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <i class="{{ $icon }} {{ $currentSize['icon'] }} {{ $currentColor['icon'] }}"></i>
        </div>
    @endif
    
    @if($loading)
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <i class="fas fa-spinner fa-spin {{ $currentSize['icon'] }} {{ $currentColor['icon'] }}"></i>
        </div>
    @endif
</div>