@props([
    'title' => 'Filtros',
    'subtitle' => null,
    'color' => 'blue',
    'showBasic' => null,
    'showAdvanced' => null,
    'defaultOpen' => false,
])

<!-- 🎯 Filter Panel Component -->
<div x-data="{ openAccordion: {{ $defaultOpen ? 'true' : 'false' }} }" class="mb-8 bg-white rounded-2xl shadow-md border border-{{ $color }}-200/60 transition-all duration-300 hover:shadow-lg">
    <!-- Cabeçalho -->
    <div class="px-6 py-2 border-b border-{{ $color }}-100 bg-gradient-to-r from-{{ $color }}-50/70 to-white backdrop-blur-sm flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <div class="size-7 rounded-xl bg-{{ $color }}-100 flex items-center justify-center shadow-inner">
                <i class="fa-solid fa-filter text-{{ $color }}-600 text-xs"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 leading-none text-sm">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-xs text-gray-600 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if($showAdvanced)
            <button @click="openAccordion = !openAccordion" class="flex items-center gap-2 text-xs font-medium text-{{ $color }}-700 transition-all duration-200">
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
