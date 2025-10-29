@props([
    'title' => 'Filtros',
    'showBasic' => null,
    'showAdvanced' => null,
    'accordionOpen' => false,
])

<!-- 🎯 Filter Panel Component -->
<div x-data="{ openAccordion: {{ $accordionOpen ? 'true' : 'false' }} }" class="mb-8 bg-white {{ config('xadminpanel.class_filter') }}">
    <!-- Cabeçalho -->
    <div class="flex flex-wrap items-center justify-between gap-3 {{ config('xadminpanel.class_filter_header') }}">
        <div class="flex items-center gap-2">
            <div class="flex items-center justify-center shadow-inner {{ config('xadminpanel.class_filter_icon') }}">
                <i class="fa-solid fa-filter"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 leading-none text-sm">{{ $title }}</h3>
            </div>
        </div>

        @if($showAdvanced)
            <button @click="openAccordion = !openAccordion" class="flex items-center gap-2 text-xs font-medium transition-all duration-200">
                <i class="fa-solid fa-sliders text-xs transition-transform duration-300"
                   :class="{ 'rotate-90': openAccordion }"></i>
                <span x-text="openAccordion ? 'Ocultar Filtros' : 'Filtros Avançados'"></span>
            </button>
        @endif
    </div>

    <!-- Corpo -->
    <div class="px-6 py-4 space-y-4">
        @isset($showBasic)
            <!-- Filtros Básicos -->
            <div class="grid grid-cols-2 md:grid-cols-12 gap-5">
                {{ $showBasic }}
            </div>
        @endisset

        @if($showAdvanced)
            <!-- Filtros Avançados -->
            <div x-show="openAccordion" x-collapse x-transition>
                <div class="grid grid-cols-2 md:grid-cols-12 gap-6">
                    {{ $showAdvanced }}
                </div>
            </div>
        @endif
    </div>
</div>
