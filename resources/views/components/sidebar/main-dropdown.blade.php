@props([
    'icon' => 'fa-solid fa-circle-question',
    'title' => 'Título',
    'active' => false,
])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="relative mx-2">
    <button
        @click="open = !open"
        class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-xs font-semibold
        transition-all duration-200 ease-out
        {{ $active
            ? 'bg-green-700 text-white shadow-md'
            : 'text-gray-700 hover:bg-green-50 hover:text-green-700 hover:translate-x-1'
        }}"
    >
        <div class="flex items-center gap-2">
            <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-white' : 'text-green-600' }}"></i>

            <span
                :class="sidebarExpanded ? 'inline opacity-100' : 'hidden opacity-0'"
                class="transition-all duration-200 whitespace-nowrap opacity-100 lg:opacity-0"
            >
                {{ $title }}
            </span>
        </div>

        <svg
            :class="open ? 'rotate-90' : ''"
            class="w-4 h-4 transition-transform duration-200 {{ $active ? 'text-white' : 'text-gray-500' }}"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    <div
        x-show="open && sidebarExpanded"
        x-transition
        class="ml-4 mt-2 space-y-1 border-l border-green-400 pl-3"
    >
        {{ $slot }}
    </div>
</div>
