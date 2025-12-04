@props(['value', 'icon', 'type'=>'button' ])

<button type="{{ $type }}" {{ $attributes->merge(['class' => "w-full flex items-center justify-center bg-green-700 hover:bg-green-600 text-white text-xs px-2.5 py-2 rounded-lg font-medium flex items-center gap-2 shadow-lg hover:shadow-xl transform transition-all duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-green-500/30 border-0 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none"]) }}
>
    @if ($icon) <i class="{{ $icon }} text-white text-xs pr-1"></i> @endif
    <span>{{ $value ?? $slot}}</span>
</button>