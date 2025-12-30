@props([
    'icon' => 'fa-solid fa-angle-right',
    'title' => 'Subgrupo',
    'active' => false,
])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }">
    <button
        @click="open = !open"
        class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-xs font-semibold
        transition-all duration-200 ease-out
        {{ $active
            ? 'text-green-800 bg-green-50 border border-green-600'
            : 'text-gray-600 hover:bg-green-100 hover:text-green-700 hover:translate-x-1'
        }}"
    >
        <div class="flex items-center gap-2">
            <i class="{{ $icon }} text-xs text-green-600"></i>
            <span>{{ $title }}</span>
        </div>

        <svg
            :class="open ? 'rotate-90' : ''"
            class="w-3 h-3 transition-transform text-gray-400"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    <div x-show="open" x-transition class="ml-4 mt-1 space-y-1">
        {{ $slot }}
    </div>
</div>
