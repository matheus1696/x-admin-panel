@props([
    'showBasic' => null,
    'showAdvanced' => null,
    'accordionOpen' => false,
])

<div x-data="{ openAccordion: {{ $accordionOpen ? 'true' : 'false' }} }" >
    @if($showAdvanced)
        <div class="flex justify-end">
            <button type="button" @click="openAccordion = !openAccordion" class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-xs  font-medium bg-gray-200 hover:bg-gray-300 transition" >
                <i class="fa-solid fa-sliders transition-transform duration-300" :class="{ 'rotate-90': openAccordion }"></i>
                <span x-text="openAccordion ? 'Ocultar filtros' : 'Abrir Filtros'"></span>
            </button>
        </div>
    @endif
    <!-- Filtros Avançados -->
    @if($showAdvanced && $showBasic)
        <div x-show="openAccordion" x-collapse x-transition>
            <div class="rounded-xl border border-dashed border-gray-300 bg-emerald-50 p-4" :class="{ 'mt-4': openAccordion }">
                <div class="grid grid-cols-2 md:grid-cols-12 gap-4">
                    {{ $showBasic }}
                    {{ $showAdvanced }}
                </div>
            </div>
        </div>
    @endif
</div>


