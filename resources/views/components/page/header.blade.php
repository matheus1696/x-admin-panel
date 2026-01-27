@props([
    'icon' => 'fa-solid fa-icons',
    'color' => 'green',
    'title' => 'Título da Página',
    'subtitle' => 'Subtítulo da Página',
    'button' => null,
])

<!-- Page Header -->
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-5">
    <!-- Título -->
    <div class="flex items-start gap-4">
        <div class="flex items-center justify-center size-12 rounded-2xl bg-{{ $color }}-100 text-{{ $color }}-600 border border-{{ $color }}-500 shadow-sm">
            <i class="{{ $icon }} text-xl"></i>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-900 leading-tight">
                {{ $title }}
            </h2>
            <p class="text-sm text-gray-400 mt-0.5">
                {{ $subtitle }}
            </p>
        </div>
    </div>

    <!-- Ação -->
    @if($button)
        <div class="flex items-center justify-end">
            {{ $button }}
        </div>
    @endif
</div>
