@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'green_solid',
    'icon' => null,
    'text' => null,
    'loading' => false, // Agora controlado pelo próprio componente
    'disabled' => false,
    'fullWidth' => false,
    'size' => 'xs',
    'pill' => false,
    'shadow' => true,
    'withIconRight' => false,
    'loadingText' => 'Enviando ...',
    'spinner' => 'ri-loader-4-line animate-spin',
    'preventSubmit' => false, // Nova prop para controlar o comportamento
])

@php
    // Classes base com efeitos modernos
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-300 transform focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none';
    
    // Tamanhos
    $sizes = [
        'xs' => 'text-[13px] px-3 py-1 gap-1.5',
        'sm' => 'text-[13px] px-3.5 py-1.5 gap-2',
        'md' => 'text-sm px-4 py-2 gap-2',
        'lg' => 'text-base px-5 py-2.5 gap-2.5',
    ];
    
    // Tamanhos para text variants (menor padding)
    $textSizes = [
        'xs' => 'text-xs px-0.5 py-1',
        'sm' => 'text-sm px-0.5 py-0.5',
        'md' => 'text-sm px-0.5 py-1',
        'lg' => 'text-base px-1.5 py-1',
    ];
    
    // Variantes seguindo padrão [cor]_[tipo]
    $variants = [
        // SOLID VARIANTS (com gradiente)
        'green_solid' => 'text-white bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 shadow-md hover:shadow-lg transition-all duration-300 -translate-y-0.5 border border-emerald-600/20 rounded-lg active:scale-[0.98]',
        
        'gray_solid' => 'text-white bg-gradient-to-r from-gray-700 via-gray-700 to-gray-800 hover:from-gray-700 hover:via-gray-800 hover:to-gray-900 focus:ring-gray-600/30 shadow-md hover:shadow-lg border border-gray-700/20 rounded-lg active:scale-[0.98]',
        
        'yellow_solid' => 'text-white bg-gradient-to-r from-amber-400 via-amber-400 to-amber-500 hover:from-amber-500 hover:via-amber-500 hover:to-amber-600 focus:ring-yellow-600/30 shadow-md hover:shadow-lg border border-yellow-600/20 rounded-lg active:scale-[0.98]',
        
        'red_solid' => 'text-white bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-700 hover:via-rose-700 hover:to-red-700 focus:ring-red-600/30 shadow-md hover:shadow-lg border border-red-700/20 rounded-lg active:scale-[0.98]',
        
        'blue_solid' => 'text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:via-indigo-700 hover:to-blue-700 focus:ring-blue-600/30 shadow-md hover:shadow-lg border border-blue-700/20 rounded-lg active:scale-[0.98]',
        
        'sky_solid' => 'text-white bg-gradient-to-r from-sky-600 via-cyan-600 to-sky-700 hover:from-sky-700 hover:via-cyan-700 hover:to-sky-700 focus:ring-sky-600/30 shadow-md hover:shadow-lg border border-sky-700/20 rounded-lg active:scale-[0.98]',
        
        'indigo_solid' => 'text-white bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 hover:from-indigo-700 hover:via-purple-700 hover:to-indigo-700 focus:ring-indigo-600/30 shadow-md hover:shadow-lg border border-indigo-700/20 rounded-lg active:scale-[0.98]',
        
        'purple_solid' => 'text-white bg-gradient-to-r from-purple-600 via-fuchsia-600 to-purple-700 hover:from-purple-700 hover:via-fuchsia-700 hover:to-purple-700 focus:ring-purple-600/30 shadow-md hover:shadow-lg border border-purple-700/20 rounded-lg active:scale-[0.98]',
        
        // OUTLINE VARIANTS (bordas)
        'gray_outline' => 'bg-gradient-to-r from-gray-50 to-gray-100/80 text-gray-700 border-gray-200/50 hover:from-gray-100 hover:to-gray-200/80 shadow-md hover:shadow-lg transition-all duration-300 -translate-y-0.5 border border-gray-600/20 rounded-lg active:scale-[0.98]',
        
        'green_outline' => 'bg-gradient-to-r from-green-50 to-green-100/80 text-green-700 border-green-200/50 hover:from-green-100 hover:to-green-200/80 shadow-md hover:shadow-lg transition-all duration-300 -translate-y-0.5 border border-green-600/20 rounded-lg active:scale-[0.98]',
        
        'blue_outline' => 'bg-gradient-to-r from-blue-50 to-blue-100/80 text-blue-700 border-blue-200/50 hover:from-blue-100 hover:to-blue-200/80 shadow-md hover:shadow-lg transition-all duration-300 -translate-y-0.5 border border-blue-600/20 rounded-lg active:scale-[0.98]',
        
        'red_outline' => 'bg-gradient-to-r from-red-50 to-red-100/80 text-red-700 border-red-200/50 hover:from-red-100 hover:to-red-200/80 shadow-md hover:shadow-lg transition-all duration-300 -translate-y-0.5 border border-red-600/20 rounded-lg active:scale-[0.98]',
        
        'yellow_outline' => 'bg-gradient-to-r from-amber-50 to-amber-100/80 text-amber-700 border-amber-200/50 hover:from-amber-100 hover:to-amber-200/80 shadow-md hover:shadow-lg transition-all duration-300 -translate-y-0.5 border border-amber-600/20 rounded-lg active:scale-[0.98]',
        
        'purple_outline' => 'bg-gradient-to-r from-purple-50 to-purple-100/80 text-purple-700 border-purple-200/50 hover:from-purple-100 hover:to-purple-200/80 shadow-md hover:shadow-lg transition-all duration-300 -translate-y-0.5 border border-purple-600/20 rounded-lg active:scale-[0.98]',
        
        // TEXT VARIANTS (dentro do texto)
        'gray_text' => 'text-gray-700 hover:text-gray-900 bg-transparent hover:bg-gray-100/50 border-0 shadow-none px-1 py-0.5 rounded-md',
        
        'green_text' => 'text-green-700 hover:text-green-800 bg-transparent hover:bg-green-50/50 border-0 shadow-none px-1 py-0.5 rounded-md',
        
        'blue_text' => 'text-blue-700 hover:text-blue-800 bg-transparent hover:bg-blue-50/50 border-0 shadow-none px-1 py-0.5 rounded-md',
        
        'red_text' => 'text-red-700 hover:text-red-800 bg-transparent hover:bg-red-50/50 border-0 shadow-none px-1 py-0.5 rounded-md',
        
        'yellow_text' => 'text-yellow-700 hover:text-yellow-800 bg-transparent hover:bg-yellow-50/50 border-0 shadow-none px-1 py-0.5 rounded-md',
        
        'purple_text' => 'text-purple-700 hover:text-purple-800 bg-transparent hover:bg-purple-50/50 border-0 shadow-none px-1 py-0.5 rounded-md',
        
        'sky_text' => 'text-sky-700 hover:text-sky-800 bg-transparent hover:bg-sky-50/50 border-0 shadow-none px-1 py-0.5 rounded-md',
        
        'indigo_text' => 'text-indigo-700 hover:text-indigo-800 bg-transparent hover:bg-indigo-50/50 border-0 shadow-none px-1 py-0.5 rounded-md',
        
        // LIGHT VARIANTS (fundos claros)
        'gray_light' => 'text-gray-700 bg-gray-100/80 hover:bg-gray-200/90 border border-gray-200/80 hover:border-gray-300 focus:ring-gray-400/20 shadow-sm hover:shadow backdrop-blur-sm rounded-lg active:scale-[0.98]',
        
        'green_light' => 'text-green-700 bg-green-100/80 hover:bg-green-200/90 border border-green-200/80 hover:border-green-300 focus:ring-green-400/20 shadow-sm hover:shadow backdrop-blur-sm rounded-lg active:scale-[0.98]',
        
        'blue_light' => 'text-blue-700 bg-blue-100/80 hover:bg-blue-200/90 border border-blue-200/80 hover:border-blue-300 focus:ring-blue-400/20 shadow-sm hover:shadow backdrop-blur-sm rounded-lg active:scale-[0.98]',
        
        // PREMIUM VARIANTS (efeitos especiais)
        'purple_premium' => 'text-white bg-gradient-to-r from-purple-700 via-pink-700 to-purple-700 hover:from-purple-700 hover:via-pink-700 hover:to-purple-800 focus:ring-purple-600/30 shadow-lg hover:shadow-xl border border-purple-700/20 rounded-lg active:scale-[0.98]',
        
        'blue_gradient' => 'text-white bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-700 hover:from-blue-700 hover:via-cyan-700 hover:to-blue-700 focus:ring-cyan-600/30 shadow-lg hover:shadow-xl border border-blue-700/20 rounded-lg active:scale-[0.98]',
        
        'green_gradient' => 'text-white bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 hover:from-emerald-700 hover:via-green-700 hover:to-emerald-700 focus:ring-emerald-600/30 shadow-lg hover:shadow-xl border border-emerald-700/20 rounded-lg active:scale-[0.98]',
        
        // DARK VARIANTS
        'gray_dark' => 'text-white bg-gradient-to-r from-gray-800 via-gray-900 to-black hover:from-gray-900 hover:via-black hover:to-gray-900 focus:ring-gray-700/30 shadow-lg hover:shadow-xl border border-gray-800/30 rounded-lg active:scale-[0.98]',
        
        'white_dark' => 'text-gray-800 bg-white/95 backdrop-blur-sm border border-gray-300 hover:border-gray-400 hover:bg-white focus:ring-gray-400/20 shadow-md hover:shadow-lg rounded-lg active:scale-[0.98]',
    ];

    // Classes dinâmicas
    $isTextVariant = str_ends_with($variant, '_text');
    $sizeClass = $isTextVariant ? ($textSizes[$size] ?? $textSizes['md']) : ($sizes[$size] ?? $sizes['md']);
    $variantClass = $variants[$variant] ?? $variants['green_solid'];
    $widthClass = $fullWidth ? 'w-full' : '';
    $pillClass = $pill ? 'rounded-full' : '';
    $shadowClass = !$shadow ? 'shadow-none hover:shadow-none' : '';
    
    // Classes finais
    $classes = $baseClasses . ' ' . $sizeClass . ' ' . $variantClass . ' ' . $widthClass . ' ' . $pillClass . ' ' . $shadowClass;

    $isLink = !is_null($href);
@endphp

@if ($isLink)
    {{-- Link mode (sem comportamento de loading) --}}
    <a 
        href="{{ $href }}" 
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled) aria-disabled="true" tabindex="-1" @endif
    >
        @if ($icon && !$withIconRight)
            <i class="{{ $icon }}"></i>
        @endif
        <span>{{ $text ?? $slot }}</span>
        @if ($icon && $withIconRight)
            <i class="{{ $icon }}"></i>
        @endif
    </a>
@elseif($preventSubmit)
    {{-- Button mode com Alpine autocontido --}}
    <div x-data="{ 
            loading: {{ $loading ? 'true' : 'false' }},
            preventSubmit: {{ $preventSubmit ? 'true' : 'false' }},
            submit() {
                if (this.preventSubmit && this.loading) return;
                
                if (this.preventSubmit) {
                    this.loading = true;
                }
                
                // Encontra o formulário mais próximo
                const form = this.$el.closest('form');
                if (form) {
                    form.submit();
                }
            }
        }"
        x-init="
            // Captura Enter se estiver dentro de um form
            if ($el.closest('form')) {
                $el.closest('form').addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.altKey) {
                        e.preventDefault();
                        submit();
                    }
                });
            }
        ">
        <button type="{{ $type }}" x-bind:disabled="loading || {{ $disabled ? 'true' : 'false' }}" x-on:click="submit()" {{ $attributes->merge(['class' => $classes]) }} @if($disabled && !$loading) disabled @endif >
            {{-- Conteúdo dinâmico com Alpine --}}
            <template x-if="!loading">
                <span class="inline-flex items-center gap-2">
                    @if ($icon && !$withIconRight)
                        <i class="{{ $icon }}"></i>
                    @endif
                    <span>{{ $text ?? $slot }}</span>
                    @if ($icon && $withIconRight)
                        <i class="{{ $icon }}"></i>
                    @endif
                </span>
            </template>
            
            <template x-if="loading">
                <span class="inline-flex items-center gap-2">
                    <i class="{{ $spinner }}"></i>
                    <span>{{ $loadingText }}</span>
                </span>
            </template>
        </button>
    </div>
@else
    {{-- Button mode (sem comportamento de loading) --}}
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @disabled($disabled || $loading)>
        @if ($icon && !$withIconRight)
            <i class="{{ $icon }}"></i>
        @endif
        @if ($text || $slot->isNotEmpty())
            <span @if ($icon) @endif>{{ $text ?? $slot }}</span>
        @endif
        @if ($icon && $withIconRight)
            <i class="{{ $icon }}"></i>
        @endif
    </button>
@endif 