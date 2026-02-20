@props([
    'icon' => 'fas fa-circle-question',
    'title' => 'Título',
    'active' => false,
])

<div x-data="{ openDropdown: {{ $active ? 'true' : 'false' }} }" class="relative mx-2">
    <button
        @click="openDropdown = !openDropdown"
        class="w-full flex items-center justify-between px-4 py-2 rounded-lg text-xs font-medium
               transition-all duration-200 ease-out group
               {{ $active
                   ? 'bg-gradient-to-r from-emerald-700 to-emerald-800 text-white shadow-md'
                   : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 hover:translate-x-1'
               }}"
    >
        <div class="flex items-center gap-3">
            <i class="{{ $icon }} text-sm w-5 text-center {{ $active ? 'text-white' : 'text-emerald-700' }}"></i>

            <span x-show="sidebarExpanded || openAside" 
                  x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0 -translate-x-2"
                  x-transition:enter-end="opacity-100 translate-x-0"
                  class="whitespace-nowrap">
                {{ $title }}
            </span>
        </div>
        
        <i x-show="sidebarExpanded || openAside"
           :class="{ 'rotate-90': openDropdown }" 
           class="fas fa-chevron-right text-xs transition-transform duration-200 {{ $active ? 'text-white' : 'text-gray-400' }}"></i>
    </button>

    <!-- Dropdown items - visível apenas quando sidebar expandida E dropdown aberto -->
    <div x-show="openDropdown && sidebarExpanded" 
         x-collapse
         class="ml-6 mt-2 space-y-1 border-l-2 border-emerald-200/50 pl-3">
        {{ $slot }}
    </div>
    
    <!-- Para mobile (sidebar sempre expandida virtualmente) -->
    <div x-show="openDropdown && !sidebarExpanded" 
         x-collapse
         class="ml-6 mt-2 space-y-1 border-l-2 border-emerald-200/50 pl-3 md:hidden">
        {{ $slot }}
    </div>
</div>