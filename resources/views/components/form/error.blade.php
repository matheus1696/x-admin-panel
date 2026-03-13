@props([
    'for',
    'icon' => 'fas fa-circle-exclamation',
])

@php
    $message = null;

    if ($errors && $errors->has($for)) {
        $message = $errors->first($for);
    }
@endphp

@if ($message)
    <p {{ $attributes->merge([ 'class' => 'mt-1 inline-flex items-start gap-1.5 pl-1 text-xs font-medium text-red-600' ]) }}>
        <i class="{{ $icon }} mt-0.5 text-[10px]" aria-hidden="true"></i>
        <span>{{ $message }}</span>
    </p>
@endif
