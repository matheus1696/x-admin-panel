@props(['title' => null, 'color' => 'blue', 'disabled' => false])

<div class="rounded-lg text-xs font-medium text-{{ $color }}-700 bg-{{ $color }}-50 hover:bg-{{ $color }}-100 border border-{{ $color }}-200 transition-all duration-200 {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}" title="{{ $title ?? ''}}">
    <div class="flex items-center justify-center size-6">
        {{ $slot }}
    </div>
</div>
