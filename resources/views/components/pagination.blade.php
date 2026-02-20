@props(['paginator'])

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center pb-4">
        <div class="flex items-center gap-1 px-2 py-1">

            {{-- Botão Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="flex items-center justify-center px-3 py-2 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-md cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left mr-1 text-[10px]"></i>
                    Anterior
                </span>
            @else
                <button 
                    wire:click="previousPage"
                    class="flex items-center justify-center px-3 py-2 text-xs font-medium text-emerald-700 bg-white border border-gray-200 rounded-md hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all duration-200 active:scale-95">
                    <i class="fa-solid fa-chevron-left mr-1 text-[10px]"></i>
                    Anterior
                </button>
            @endif

            {{-- Números das Páginas --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-3 py-2 text-xs font-medium text-gray-500 select-none">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-2 text-xs font-semibold border rounded-md cursor-default 
                                    bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md border-emerald-600">
                                    {{ $page }}
                                </span>
                            @else
                                <button 
                                    wire:click="gotoPage({{ $page }})"
                                    class="px-3 py-2 text-xs font-medium text-gray-700 border border-gray-200 rounded-md hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-600 transition-all duration-200 active:scale-95">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Botão Próximo --}}
            @if ($paginator->hasMorePages())
                <button 
                    wire:click="nextPage"
                    class="flex items-center justify-center px-3 py-2 text-xs font-medium text-emerald-700 bg-white border border-gray-200 rounded-md hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all duration-200 active:scale-95">
                    Próximo
                    <i class="fa-solid fa-chevron-right ml-1 text-[10px]"></i>
                </button>
            @else
                <span class="flex items-center justify-center px-3 py-2 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-md cursor-not-allowed">
                    Próximo
                    <i class="fa-solid fa-chevron-right ml-1 text-[10px]"></i>
                </span>
            @endif

        </div>
    </nav>
@endif