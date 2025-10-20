@props([
    'icon' => 'fa-solid fa-circle-question',
    'title' => 'Title',
    'active' => false,
])

<!-- Grupo com submenu -->
<div x-data="{ groupOpen: false }" class="relative mx-2">
    <button @click="groupOpen = !groupOpen"
            class="w-full flex items-center justify-between px-4 py-1.5 rounded-xl font-medium transition-all duration-200 
                   {{ $active || $active ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-700' }} hover:translate-x-1 border border-transparent hover:border-blue-700">
        <div class="flex items-center gap-3">
            <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-white' : 'text-blue-500' }}"></i>
            <span :class="sidebarExpanded ? 'opacity-100 whitespace-nowrap transition-all duration-200' : 'hidden opacity-0'"> {{ $title }} </span>
        </div>
        <div :class="sidebarExpanded ? 'opacity-100' : 'hidden opacity-0'">
            <svg :class="groupOpen ? 'rotate-90' : ''" 
                class="w-4 h-4 transition-transform duration-200 {{ $active ? 'text-white' : 'text-gray-400' }}" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </button>
    
    <div x-show="groupOpen && sidebarExpanded" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="ml-6 mt-2 space-y-1 border-l-2 border-blue-100 pl-3">
        {{ $slot }}
    </div>
</div>