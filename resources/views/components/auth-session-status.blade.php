@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'hidden']) }}>
        {{ $status }}
    </div>
@endif
