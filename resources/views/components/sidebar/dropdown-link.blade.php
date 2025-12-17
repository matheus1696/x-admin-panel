@props([
    'title' => 'Item',
    'href' => '#',
    'active' => false,
    'icon' => null,
])

<a
    href="{{ $href }}"
    class="block px-3 py-2 rounded-md text-[11px] transition-all duration-200 ease-out
    {{ $active
        ? 'bg-green-600 text-white'
        : 'text-gray-600 hover:bg-green-100 hover:text-green-700 hover:translate-x-1'
    }}"
>
    <div class="flex gap-2 items-center">
        @if ($icon)
            <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-white' : 'text-green-600' }}"></i>
        @endif

        {{ $title }}
    </div>
</a>
