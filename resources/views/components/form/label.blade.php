@props(['value', 'icon' => null])

<label {{ $attributes->merge(['class' => 'flex items-center gap-1.5 pb-1.5 pl-1.5 font-medium text-sm text-gray-700']) }}>
    @if ($icon) <i class="{{ $icon }}"></i> @endif
    {{ $value ?? $slot }}
</label>
