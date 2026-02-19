@props([
    'for',
])

@php
    $message = null;

    if ($errors && $errors->has($for)) {
        $message = $errors->first($for);
    }
@endphp

@if ($message)
    <p {{ $attributes->merge([ 'class' => 'text-xs text-red-600 pl-1' ]) }}>
        {{ $message }}
    </p>
@endif
