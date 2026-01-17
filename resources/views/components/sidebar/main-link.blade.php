@props([
    'icon' => 'fa-solid fa-circle-question',
    'title' => 'Título',
    'active' => false,
    'href' => '#',
])

<a
    href="{{ $href }}"
    class="flex items-center gap-2 px-4 py-2 mx-2 rounded-xl text-xs font-medium
    transition-all duration-200 ease-out
    {{ $active
        ? 'bg-green-700 text-white shadow-md'
        : 'text-gray-700 hover:bg-green-50 hover:text-green-700 hover:translate-x-1'
    }}"
>
    <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-white' : 'text-green-600' }}"></i>

    <span
        :class="sidebarExpanded ? 'opacity-100' : 'opacity-0'"
        class="transition-all duration-200 whitespace-nowrap"
    >
        {{ $title }}
    </span>

    @if($active)
        <span
            :class="sidebarExpanded ? 'opacity-100 ml-auto w-2 h-2 bg-white rounded-full animate-pulse' : 'hidden opacity-0'"
        ></span>
    @endif
</a>
