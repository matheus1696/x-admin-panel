@props([
    'icon' => 'fa-solid fa-icons',
    'color' => 'green',
    'title' => 'Título da Página',
    'subtitle' => 'Subtitulo da Página',
    'button' => null,
])

<div class="flex items-center justify-between gap-0.5 px-3">
    <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="{{ $icon }} mr-1 text-{{ $color }}-600"></i>     
            <span class="text-gl text-gray-600 mt-1">{{ $title }}</span>
        </h2>
        <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
    </div>
    
    <div class="flex items-center justify-center">
        {{ $button }}
    </div>
</div>