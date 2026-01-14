@props(['value', 'icon' => null, 'type'=>'button' ])

<button type="{{ $type }}" {{ $attributes->merge(['class' => "text-white text-xs px-4 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center justify-center gap-1 shadow transform"]) }} >
    @if ($icon) <i class="{{ $icon }} text-white text-xs pr-1"></i> @endif
    <span>{{ $value ?? $slot}}</span>
</button>