@props(['disabled' => false, 'name' => null])

@php
    $baseClasses = 'w-full rounded-md border px-2.5 py-1.5 text-[13px] shadow-sm transition-all duration-200 disabled:bg-gray-300 disabled:text-gray-700 disabled:cursor-not-allowed';
    $errorClasses = 'border-red-500 bg-red-50 text-red-700 placeholder-red-400 focus:border-red-500 focus:ring-red-500';
    $normalClasses = "border-gray-300 bg-gray-50 text-gray-700 placeholder-gray-400 focus:border-green-700 focus:ring-green-700";
@endphp

<input
    name="{{ $name }}"
    id="{{ $name }}"
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => $baseClasses . ' ' . ($errors->has($name) && !$disabled ? $errorClasses : $normalClasses)
    ]) }}
>
