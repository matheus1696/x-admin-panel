@props([
    'disabled' => false,
    'color' => 'green',
    'name' => null,
    'default' => 'Selecione uma opção',
    'options' => [],
])

@php
    $baseClasses = "w-full rounded-md border px-2.5 py-2 text-xs shadow-sm transition-all duration-200 disabled:bg-gray-300 disabled:text-gray-700 disabled:cursor-not-allowed";
    $successClasses = "border-gray-300 bg-gray-50 text-gray-700 placeholder-gray-400 focus:border-{$color}-700 focus:ring-{$color}-700";
    $errorClasses = "border-red-500 bg-red-50 text-red-700 placeholder-red-400 focus:border-red-500 focus:ring-red-500";
@endphp

<select
    name="{{ $name }}"
    id="{{ $name }}"
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => $baseClasses . ' ' . ($errors->has($name) && !$disabled ? $errorClasses : $successClasses),
    ]) }}
>
    <option value="" disabled selected>{{ $default }}</option>

    {{-- Se usar via slot --}}
    {{ $slot }}

    {{-- Se usar via :options --}}
    @if(!empty($options))
        @foreach($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    @endif
</select>
