@props([
    'icon' => 'fas fa-folder',
    'title' => 'Subgrupo',
    'active' => false,
])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="select-none">
    <button
        @click="open = !open"
        class="w-full flex items-center justify-between px-4 py-2 rounded-lg text-xs font-medium
               transition-all duration-200 ease-out group
               {{ $active
                   ? 'text-emerald-800 bg-emerald-50 border border-emerald-200'
                   : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 hover:translate-x-1'
               }}"
        {{ $attributes }}
    >
        <div class="flex items-center gap-3">
            <i class="{{ $icon }} text-sm {{ $active ? 'text-emerald-700' : 'text-emerald-800' }} w-5 text-center"></i>
            <span>{{ $title }}</span>
        </div>

        <i class="fas fa-chevron-right text-xs transition-transform duration-200 {{ $active ? 'text-emerald-700' : 'text-gray-400' }}"
           :class="{ 'rotate-90': open }"></i>
    </button>

    <div x-show="open" 
         x-collapse 
         class="ml-4 mt-1 space-y-0.5 border-l-2 border-emerald-200/50 pl-2">
        {{ $slot }}
    </div>
</div>