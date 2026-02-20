@props([
    'icon' => 'fas fa-circle-question',
    'title' => 'Título',
    'active' => false,
    'href' => '#',
])

<div class="relative mx-2">
    <a
        href="{{ $href }}"
        class="w-full flex items-center justify-between px-4 py-2 rounded-lg text-xs font-medium
               transition-all duration-200 ease-out group
               {{ $active
                   ? 'bg-gradient-to-r from-emerald-700 to-emerald-800 text-white shadow-md'
                   : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 hover:translate-x-1'
               }}"
        {{ $attributes }}
    >
        <div class="flex items-center gap-3">
            <i class="{{ $icon }} text-sm w-5 text-center {{ $active ? 'text-white' : 'text-emerald-700' }}"></i>

            <span x-show="sidebarExpanded || openAside" 
                  x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0 -translate-x-2"
                  x-transition:enter-end="opacity-100 translate-x-0"
                  class="whitespace-nowrap font-medium">
                {{ $title }}
            </span>
        </div>

        @if($active)
            <span x-show="sidebarExpanded"
                  class="w-1.5 h-1.5 bg-white rounded-full animate-pulse shadow-lg"></span>
        @endif
    </a>
</div>