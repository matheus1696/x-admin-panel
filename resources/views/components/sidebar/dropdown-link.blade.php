@props([
    'title' => 'Item',
    'href' => '#',
    'active' => false,
    'icon' => 'fas fa-circle',
])

<a
    href="{{ $href }}"
    class="block px-3 py-2 rounded-lg text-xs transition-all duration-200 ease-out
    {{ $active
        ? 'bg-gradient-to-r from-emerald-700 to-emerald-800 text-white shadow-md'
        : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 hover:translate-x-1'
    }}"
    {{ $attributes }}
>
    <div class="flex items-center gap-3">
        @if ($icon)
            <i class="{{ $icon }} w-5 text-center text-sm {{ $active ? 'text-white' : 'text-emerald-800' }}"></i>
        @endif

        <span class="font-medium">{{ $title }}</span>
    </div>
</a>