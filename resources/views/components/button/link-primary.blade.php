@props(['value' => null])

<a {{ $attributes->merge([ 'class' => 'bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl transform']) }}>
    {{ $slot ?? $value }}
</a>