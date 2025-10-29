@props([
    'icon' => 'fa-solid fa-icons',
    'title' => 'Título da Página',
    'subtitle' => 'Subtitulo da Página',
    'button' => null,
])

<div class="flex items-center justify-between gap-0.5 px-1.5">
    <div>
        <h2 class="{{ config('xadminpanel.class_page_header') }}">
            <i class="{{ $icon }} {{ config('xadminpanel.class_page_header_icon') }}"></i>     
            <span class="{{ config('xadminpanel.class_page_header_title') }}">{{ $title }}</span>
        </h2>
        <p class="{{ config('xadminpanel.class_page_header_subtitle') }}">{{ $subtitle }}</p>
    </div>
    
    <div class="flex items-center justify-center">
        {{ $button }}
    </div>
</div>