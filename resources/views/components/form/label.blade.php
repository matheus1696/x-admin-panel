@props(['value', 'icon' => null])

<label {{ $attributes->merge(['class' => 'flex items-center gap-1.5 pb-1.5 pl-1.5 font-medium text-xs text-gray-700 dark:text-gray-300']) }}>
    @if ($icon) <i class="{{ $icon }} text-blue-600 text-xs"></i> @endif
    {{ $value ?? $slot }}
</label>
