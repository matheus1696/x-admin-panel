@props(['value'])

<label {{ $attributes->merge(['class' => 'block pb-1 pl-1 font-medium text-xs text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
</label>
