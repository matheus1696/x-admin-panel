<div class="flex items-center justify-between gap-0.5 px-1.5">
    <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="{{ $icon ?? 'fa-solid fa-icons'}} mr-2 text-blue-600"></i>
            {{ $title ?? 'Título da Página' }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">{{ $subtitle ?? 'Subtitulo da Página'}}</p>
    </div>
    
    <div class="flex items-center justify-center">
        {{ $button ?? '' }}
    </div>
</div>