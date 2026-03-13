@props([
    'accordionOpen' => false,
    'title' => 'Filtros',
    'icon' => 'fas fa-sliders',
    'description' => null,
    'showClear' => null,
    'clearAction' => null,
    'gridClass' => 'grid grid-cols-1 gap-4 md:grid-cols-12',
    'panelClass' => '',
    'headerClass' => '',
])

@php
    $normalizeBoolean = static fn (mixed $value): bool => filter_var($value, FILTER_VALIDATE_BOOLEAN) || $value === true || $value === 1 || $value === '1';

    $accordionOpen = $normalizeBoolean($accordionOpen);
    $showClear = $normalizeBoolean($showClear);
    $filterContent = isset($showBasic) ? $showBasic : $slot;
@endphp

<section x-data="{ open: {{ $accordionOpen ? 'true' : 'false' }} }" class="mb-4">
    <div class="flex flex-col gap-3 px-1 border-b border-gray-200 pb-2 {{ $headerClass }}">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <div class="h-5 w-1 rounded-full bg-gradient-to-b from-emerald-700 to-emerald-800"></div>
                    <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">{{ $title }}</h3>
                </div>

                @if ($description)
                    <p class="mt-2 text-sm text-slate-500">{{ $description }}</p>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @isset($actions)
                    {{ $actions }}
                @endisset

                @if ($showClear && $clearAction)
                    <x-button
                        type="button"
                        variant="ghost"
                        icon="fas fa-times"
                        text="Limpar"
                        wire:click="{{ $clearAction }}"
                    />
                @endif

                <x-button
                    type="button"
                    variant="ghost"
                    :icon="$icon"
                    x-on:click="open = !open"
                >
                    <span x-text="open ? 'Ocultar' : 'Abrir'"></span>
                </x-button>
            </div>
        </div>
    </div>

    <div
        x-show="open"
        x-collapse
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="mt-3"
    >
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm {{ $panelClass }}">
            <div class="{{ $gridClass }}">
                {{ $filterContent }}
            </div>
        </div>
    </div>
</section>
