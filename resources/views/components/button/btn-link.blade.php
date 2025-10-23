@props(['href' => '#', 'icon' => null, 'value' => null, 'color' => 'blue'])

<a href="{{ $href }}" class="bg-{{ $color }}-500 hover:bg-{{ $color }}-700 text-white text-xs px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center justify-center gap-1 shadow-lg hover:shadow-xl transform">
    @if ($icon) <i class="{{ $icon }} text-white text-xs pr-2"></i> @endif
    <span class="line-clamp-1">{{ $value ?? $slot}}</span>
</a>