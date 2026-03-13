@props([
    'show' => false,
    'size' => 'xl',
    'maxWidth' => null,
    'closeable' => true,
    'closeMethod' => 'closeModal',
    'title' => null,
    'description' => null,
    'panelClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
])

@php
    $normalizeBoolean = static fn (mixed $value): bool => filter_var($value, FILTER_VALIDATE_BOOLEAN) || $value === true || $value === 1 || $value === '1';

    $show = $normalizeBoolean($show);
    $closeable = $normalizeBoolean($closeable);

    $sizeMap = [
        'sm' => 'max-w-md',
        'md' => 'max-w-2xl',
        'lg' => 'max-w-4xl',
        'xl' => 'max-w-5xl',
        '2xl' => 'max-w-7xl',
        'full' => 'max-w-[calc(100vw-2rem)]',
    ];

    $resolvedMaxWidth = $maxWidth ?: ($sizeMap[$size] ?? $sizeMap['xl']);
    $titleId = 'modal-title-'.md5((string) ($attributes->get('wire:key') ?? $title ?? $size));
    $descriptionId = 'modal-description-'.md5((string) ($attributes->get('wire:key') ?? $description ?? $size));
@endphp

@if ($show)
    <div
        x-data="{
            closeable: {{ $closeable ? 'true' : 'false' }},
            closeMethod: @js($closeMethod),
            close() {
                if (!this.closeable) return;

                if (typeof $wire !== 'undefined') {
                    $wire.call(this.closeMethod);
                }
            }
        }"
        x-on:keydown.escape.window="close()"
        class="fixed inset-0 z-[999] flex items-center justify-center p-4 md:p-6"
        role="presentation"
    >
        <div
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"
            x-on:click="close()"
            aria-hidden="true"
        ></div>

        <div
            {{ $attributes->except(['class']) }}
            role="dialog"
            aria-modal="true"
            aria-labelledby="{{ $titleId }}"
            @if ($description) aria-describedby="{{ $descriptionId }}" @endif
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full {{ $resolvedMaxWidth }} rounded-2xl border border-gray-200 bg-white shadow-2xl {{ $panelClass }}"
            x-on:click.stop
        >
            @if (isset($header) || $title || $closeable)
                <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-4">
                    <div class="min-w-0 flex-1">
                        @if ($title)
                            <div class="flex items-center gap-2">
                                <div class="h-5 w-1 rounded-full bg-emerald-600"></div>
                                <h2 id="{{ $titleId }}" class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                            </div>
                        @elseif (isset($header))
                            <div id="{{ $titleId }}" class="min-w-0">
                                {{ $header }}
                            </div>
                        @else
                            <h2 id="{{ $titleId }}" class="sr-only">Modal</h2>
                        @endif

                        @if ($description)
                            <p id="{{ $descriptionId }}" class="mt-2 text-sm text-gray-500">{{ $description }}</p>
                        @endif
                    </div>

                    @if ($closeable)
                        <button
                            type="button"
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none"
                            aria-label="Fechar modal"
                            x-on:click="close()"
                        >
                            <i class="fas fa-times text-base"></i>
                        </button>
                    @endif
                </div>
            @endif

            <div class="max-h-[calc(100vh-200px)] overflow-y-auto px-6 py-5 {{ $bodyClass }}">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="border-t border-gray-200 bg-gray-50/60 px-6 py-4 {{ $footerClass }}">
                    <div class="flex items-center justify-end gap-3">
                        {{ $footer }}
                    </div>
                </div>
            @endisset
        </div>
    </div>
@endif
