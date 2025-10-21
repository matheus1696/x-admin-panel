@props(['value', 'icon'])

<button type="button"
        {{ $attributes->merge([
            'class' => 'w-full mb-0.5 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform transition-all duration-300 ease-in-out hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-blue-500/30 border-0 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none text-xs'
        ]) }}
    >
        @if ($icon) <i class="{{ $icon }} text-white text-xs pr-1"></i> @endif
        <span>{{ $value }}</span>
    </button>