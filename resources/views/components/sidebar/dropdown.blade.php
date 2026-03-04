@props([
    'icon' => 'fas fa-folder',
    'title' => 'Subgrupo',
    'active' => false,
])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="select-none">
    <button
        @click="open = !open"
        class="group flex w-full items-center justify-between rounded-xl border border-transparent px-4 py-2.5 text-xs font-medium
               transition-all duration-200 ease-out
               {{ $active
                   ? 'border-emerald-200 bg-emerald-50 text-emerald-800 shadow-sm'
                   : 'text-gray-600 hover:bg-emerald-50/80 hover:border-emerald-100 hover:text-emerald-700 hover:translate-x-1'
               }}"
        {{ $attributes }}
    >
        <div class="flex items-center gap-3">
            <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-emerald-700' : 'text-emerald-800' }}"></i>
            <span>{{ $title }}</span>
        </div>

        <i class="fas fa-chevron-right text-[10px] transition-transform duration-200 {{ $active ? 'text-emerald-700' : 'text-gray-400' }}"
           :class="{ 'rotate-90': open }"></i>
    </button>

    <div x-show="open"
         x-collapse
         class="ml-4 mt-1.5 space-y-1 border-l-2 border-emerald-200/50 pl-2.5">
        {{ $slot }}
    </div>
</div>