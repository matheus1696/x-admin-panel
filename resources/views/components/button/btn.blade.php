@props(['value', 'icon'])

<button type="button"
    {{ $attributes->merge([
        'class' => 'w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-xs px-2.5 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl transform transition-all duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-500/30 border-0 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none text-xs'
    ]) }}
>
    @if ($icon) <i class="{{ $icon }} text-white text-xs pr-1"></i> @endif
    <span>{{ $value }}</span>
</button>