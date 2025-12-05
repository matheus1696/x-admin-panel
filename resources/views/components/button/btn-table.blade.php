@props(['title' => null, 'disabled' => false])

<div 
    {{ $attributes->class([
        'rounded-lg text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 transition-all duration-200',
        'cursor-not-allowed' => $disabled,
        'cursor-pointer' => !$disabled,
    ]) }}
    title="{{ $title ?? ''}}"
>
    <div class="flex items-center justify-center size-6">
        {{ $slot }}
    </div>
</div>