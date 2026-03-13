@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'icon' => null,
    'text' => null,
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
    'size' => 'xs',
    'pill' => false,
    'shadow' => true,
    'withIconRight' => false,
    'loadingText' => 'Enviando...',
    'spinner' => 'ri-loader-4-line animate-spin',
    'preventSubmit' => false,
])

@php
    $normalizeBoolean = static fn (mixed $value): bool => filter_var($value, FILTER_VALIDATE_BOOLEAN) || $value === true || $value === 1 || $value === '1';

    $loading = $normalizeBoolean($loading);
    $disabled = $normalizeBoolean($disabled);
    $fullWidth = $normalizeBoolean($fullWidth);
    $pill = $normalizeBoolean($pill);
    $shadow = $normalizeBoolean($shadow);
    $preventSubmit = $normalizeBoolean($preventSubmit);

    $baseClasses = 'inline-flex items-center justify-center gap-2 font-medium transition-all duration-200 focus:outline-none focus:ring-transparent disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none';

    $sizes = [
        'xs' => 'min-h-8 px-3 py-1.5 text-[12px]',
        'sm' => 'min-h-9 px-3.5 py-2 text-[13px]',
        'md' => 'min-h-10 px-4 py-2 text-sm',
        'lg' => 'min-h-11 px-5 py-2.5 text-base',
    ];

    $textSizes = [
        'xs' => 'px-1 py-0.5 text-xs',
        'sm' => 'px-1 py-0.5 text-sm',
        'md' => 'px-1 py-1 text-sm',
        'lg' => 'px-1.5 py-1 text-base',
    ];

    $variantAliases = [
        'primary' => 'green_solid',
        'secondary' => 'gray_outline',
        'destructive' => 'red_solid',
        'ghost' => 'gray_text',
        'link' => 'gray_text',
        'success' => 'green_solid',
        'warning' => 'yellow_solid',
        'info' => 'blue_solid',
        'green' => 'green_solid',
        'gray' => 'gray_solid',
        'blue' => 'blue_solid',
        'red' => 'red_solid',
        'yellow' => 'yellow_solid',
        'sky' => 'sky_solid',
        'default' => 'green_solid',
        'filled' => 'green_solid',
        'inline' => 'gray_text',
        'minimal' => 'gray_text',
        'pills' => 'gray_outline',
    ];

    $resolvedVariant = $variantAliases[$variant] ?? $variant;

    $variants = [
        'green_solid' => 'rounded-lg border border-emerald-900/10 bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 text-white hover:from-emerald-800 hover:via-emerald-900 hover:to-teal-900 active:scale-[0.99]',
        'gray_solid' => 'rounded-lg border border-slate-900/10 bg-gradient-to-r from-slate-500 to-slate-600 text-white hover:from-slate-600 hover:to-slate-700 active:scale-[0.99]',
        'yellow_solid' => 'rounded-lg border border-amber-900/10 bg-gradient-to-r from-amber-400 to-amber-500 text-white hover:from-amber-500 hover:to-amber-600 active:scale-[0.99]',
        'red_solid' => 'rounded-lg border border-red-900/10 bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white hover:from-red-700 hover:via-rose-700 hover:to-red-800 active:scale-[0.99]',
        'blue_solid' => 'rounded-lg border border-blue-900/10 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 text-white hover:from-blue-700 hover:via-indigo-700 hover:to-blue-800 active:scale-[0.99]',
        'sky_solid' => 'rounded-lg border border-sky-900/10 bg-gradient-to-r from-sky-600 via-cyan-600 to-sky-700 text-white hover:from-sky-700 hover:via-cyan-700 hover:to-sky-800 active:scale-[0.99]',
        'indigo_solid' => 'rounded-lg border border-indigo-900/10 bg-gradient-to-r from-indigo-600 via-violet-600 to-indigo-700 text-white hover:from-indigo-700 hover:via-violet-700 hover:to-indigo-800 active:scale-[0.99]',
        'purple_solid' => 'rounded-lg border border-purple-900/10 bg-gradient-to-r from-purple-600 via-fuchsia-600 to-purple-700 text-white hover:from-purple-700 hover:via-fuchsia-700 hover:to-purple-800 active:scale-[0.99]',
        'gray_outline' => 'rounded-lg border border-slate-300 bg-gradient-to-r from-slate-50 to-white text-slate-700 hover:from-slate-100 hover:to-slate-50 active:scale-[0.99]',
        'green_outline' => 'rounded-lg border border-emerald-300 bg-gradient-to-r from-emerald-50 to-white text-emerald-700 hover:from-emerald-100 hover:to-emerald-50 active:scale-[0.99]',
        'blue_outline' => 'rounded-lg border border-blue-300 bg-gradient-to-r from-blue-50 to-white text-blue-700 hover:from-blue-100 hover:to-blue-50 active:scale-[0.99]',
        'red_outline' => 'rounded-lg border border-red-300 bg-gradient-to-r from-red-50 to-white text-red-700 hover:from-red-100 hover:to-red-50 active:scale-[0.99]',
        'yellow_outline' => 'rounded-lg border border-amber-300 bg-gradient-to-r from-amber-50 to-white text-amber-700 hover:from-amber-100 hover:to-amber-50 active:scale-[0.99]',
        'purple_outline' => 'rounded-lg border border-purple-300 bg-gradient-to-r from-purple-50 to-white text-purple-700 hover:from-purple-100 hover:to-purple-50 active:scale-[0.99]',
        'white_text' => 'text-white hover:text-slate-200',
        'gray_text' => 'text-slate-700 hover:text-slate-900',
        'green_text' => 'text-emerald-700 hover:text-emerald-900',
        'blue_text' => 'text-blue-700 hover:text-blue-900',
        'red_text' => 'text-red-700 hover:text-red-900',
        'yellow_text' => 'text-amber-700 hover:text-amber-900',
        'purple_text' => 'text-purple-700 hover:text-purple-900',
        'sky_text' => 'text-sky-700 hover:text-sky-900',
        'indigo_text' => 'text-indigo-700 hover:text-indigo-900',
        'gray_light' => 'rounded-lg border border-slate-200 bg-slate-100/80 text-slate-700 hover:bg-slate-200/90',
        'green_light' => 'rounded-lg border border-emerald-200 bg-emerald-100/80 text-emerald-700 hover:bg-emerald-200/90',
        'blue_light' => 'rounded-lg border border-blue-200 bg-blue-100/80 text-blue-700 hover:bg-blue-200/90',
        'purple_premium' => 'rounded-lg border border-purple-900/10 bg-gradient-to-r from-purple-700 via-pink-700 to-purple-700 text-white hover:from-purple-800 hover:via-pink-800 hover:to-purple-800 active:scale-[0.99]',
        'blue_gradient' => 'rounded-lg border border-blue-900/10 bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-700 text-white hover:from-blue-700 hover:via-cyan-700 hover:to-blue-800 active:scale-[0.99]',
        'green_gradient' => 'rounded-lg border border-emerald-900/10 bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 text-white hover:from-emerald-700 hover:via-green-700 hover:to-emerald-800 active:scale-[0.99]',
        'gray_dark' => 'rounded-lg border border-slate-800/30 bg-gradient-to-r from-slate-800 via-slate-900 to-black text-white hover:from-slate-900 hover:via-black hover:to-slate-900 active:scale-[0.99]',
        'white_dark' => 'rounded-lg border border-slate-300 bg-white/95 text-slate-800 hover:border-slate-400 hover:bg-white active:scale-[0.99]',
    ];

    $isTextVariant = str_ends_with($resolvedVariant, '_text');
    $sizeClass = $isTextVariant ? ($textSizes[$size] ?? $textSizes['md']) : ($sizes[$size] ?? $sizes['md']);
    $variantClass = $variants[$resolvedVariant] ?? $variants['green_solid'];
    $widthClass = $fullWidth ? 'w-full' : '';
    $shapeClass = $pill || $variant === 'pills' ? 'rounded-full' : '';
    $shadowClass = $shadow && ! $isTextVariant ? 'shadow-sm hover:shadow-md' : 'shadow-none hover:shadow-none';
    $classes = trim(implode(' ', [$baseClasses, $sizeClass, $variantClass, $widthClass, $shapeClass, $shadowClass]));

    $isLink = filled($href);
    $hasTextProp = filled($text);
    $hasSlotContent = $slot->isNotEmpty();
@endphp

@if ($isLink)
    <a
        href="{{ $disabled ? '#' : $href }}"
        {{ $attributes->class([$classes])->merge($disabled ? ['aria-disabled' => 'true', 'tabindex' => '-1'] : []) }}
    >
        @if ($icon && ! $withIconRight)
            <i class="{{ $icon }}"></i>
        @endif

        @if ($hasTextProp)
            <span>{{ $text }}</span>
        @elseif ($hasSlotContent)
            {{ $slot }}
        @endif

        @if ($icon && $withIconRight)
            <i class="{{ $icon }}"></i>
        @endif
    </a>
@elseif ($preventSubmit)
    <div
        x-data="{
            loading: {{ $loading ? 'true' : 'false' }},
            submit() {
                if (this.loading) return;
                this.loading = true;
                const form = this.$el.closest('form');
                if (form) {
                    form.submit();
                } else {
                    this.loading = false;
                }
            }
        }"
    >
        <button
            type="{{ $type }}"
            x-bind:disabled="loading || {{ $disabled ? 'true' : 'false' }}"
            x-on:click="submit()"
            {{ $attributes->class([$classes]) }}
            @disabled($disabled && ! $loading)
        >
            <template x-if="!loading">
                <span class="inline-flex items-center gap-2">
                    @if ($icon && ! $withIconRight)
                        <i class="{{ $icon }}"></i>
                    @endif

                    @if ($hasTextProp)
                        <span>{{ $text }}</span>
                    @elseif ($hasSlotContent)
                        {{ $slot }}
                    @endif

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
    <button
        {{ $attributes->class([$classes])->merge(['type' => $type]) }}
        @disabled($disabled || $loading)
    >
        @if ($icon && ! $withIconRight)
            <i class="{{ $icon }}"></i>
        @endif

        @if ($hasTextProp)
            <span>{{ $text }}</span>
        @elseif ($hasSlotContent)
            {{ $slot }}
        @endif

        @if ($icon && $withIconRight)
            <i class="{{ $icon }}"></i>
        @endif
    </button>
@endif
