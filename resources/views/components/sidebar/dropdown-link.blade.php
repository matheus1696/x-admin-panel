@props([
    'title' => 'Item',
    'href' => '#',
    'active' => false,
    'icon' => 'fa-solid fa-circle',
])

<a
    href="{{ $href }}"
    class="block px-3 py-2 rounded-xl text-[11px] transition-all duration-200 ease-out
    {{ $active
        ? 'bg-green-700 text-white'
        : 'text-gray-600 hover:bg-green-100 hover:text-green-700 hover:translate-x-1'
    }}"
>
    <div class="flex gap-2 items-center font-semibold">
        @if ($icon)
            <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-white' : 'text-green-700' }}"></i>
        @endif

        {{ $title }}
    </div>
</a>
