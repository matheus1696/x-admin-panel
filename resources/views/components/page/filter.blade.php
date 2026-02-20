@props([
    'accordionOpen' => false,
    'title' => 'Filtros',
    'icon' => 'fas fa-sliders',
    'showClear' => null,
    'clearAction' => null,
])

<div x-data="{ openAccordion: {{ $accordionOpen ? 'true' : 'false' }} }" class="mb-4">
    
    <!-- Cabeçalho com título e botões -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-1 h-5 bg-gradient-to-b from-emerald-700 to-emerald-800 rounded-full"></div>
            <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ $title }}</h3>
        </div>
        
        <div class="flex items-center gap-2">
            @if($showClear && $clearAction)
                <button 
                    type="button"
                    wire:click="{{ $clearAction }}"
                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-medium
                           bg-gray-100 text-gray-600 hover:bg-red-100 hover:text-red-600
                           transition-all duration-200"
                >
                    <i class="fas fa-times text-[8px]"></i>
                    <span>Limpar</span>
                </button>
            @endif

            <button 
                type="button" 
                @click="openAccordion = !openAccordion" 
                class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-medium 
                       bg-gray-100 text-gray-700 hover:bg-emerald-100 hover:text-emerald-700 
                       transition-all duration-200 group"
            >
                <i class="{{ $icon }} text-xs transition-all duration-300 group-hover:text-emerald-700" 
                   :class="{ 'rotate-90': openAccordion }"></i>
                <span x-text="openAccordion ? 'Ocultar' : 'Abrir'"></span>
            </button>
        </div>
    </div>

    <!-- Painel de Filtros -->
    <div x-show="openAccordion" 
         x-collapse 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mt-3">
        
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>