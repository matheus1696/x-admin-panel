@props([
    'icon' => 'fa-solid fa-circle-question',
    'title' => 'Título',
    'active' => false,
    'color' => 'green',
])

<div x-data="{ open: false }" class="relative mx-2">
    <!-- Botão principal -->
    <button 
        @click="open = !open" 
        class="w-full flex items-center justify-between px-4 py-2 rounded-lg font-medium text-xs transition-all duration-200 
            {{ $active 
                ? 'bg-green-600 text-white shadow-md' 
                : 'text-gray-700 hover:bg-green-50 hover:text-green-700 hover:translate-x-1 border border-transparent hover:border-green-700' }}"
    >
        <!-- Ícone e título -->
        <div class="flex items-center gap-3">
            <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-white' : 'text-green-500' }}"></i>
            <span 
                class="transition-all duration-200"
                :class="sidebarExpanded ? 'opacity-100 whitespace-nowrap' : 'hidden opacity-0'"
            >
                {{ $title }}
            </span>
        </div>

        <!-- Seta -->
        <div 
            :class="sidebarExpanded ? 'opacity-100' : 'hidden opacity-0'"
            class="transition-all duration-200"
        >
            <svg 
                :class="open ? 'rotate-90' : 'rotate-0'" 
                class="w-4 h-4 transform transition-transform duration-200 {{ $active ? 'text-white' : 'text-gray-400' }}" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </div>
    </button>

    <!-- Submenu -->
    <div 
        x-show="open && sidebarExpanded" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="ml-6 mt-2 space-y-1 border-l-2 border-green-100 pl-3"
    >
        {{ $slot }}
    </div>
</div>
