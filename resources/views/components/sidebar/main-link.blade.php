@props([
    'icon' => 'fas fa-circle-question',
    'title' => 'Título',
    'active' => false,
    'href' => '#',
])

<div class="relative mx-2">
    <a
        href="{{ $href }}"
        class="group flex w-full items-center justify-between rounded-xl border border-transparent px-4 py-2.5 text-xs font-medium
               transition-all duration-200 ease-out
               {{ $active
                   ? 'bg-gradient-to-r from-emerald-700 to-emerald-800 text-white shadow-sm ring-1 ring-emerald-700/10'
                   : 'text-gray-600 hover:bg-emerald-50/80 hover:border-emerald-100 hover:text-emerald-800 hover:translate-x-1'
               }}"
        {{ $attributes }}
    >
        <div class="flex items-center gap-3">
            <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-white' : 'text-emerald-700' }}"></i>

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
                  class="h-1.5 w-1.5 rounded-full bg-white shadow-sm"></span>
        @endif
    </a>
</div>