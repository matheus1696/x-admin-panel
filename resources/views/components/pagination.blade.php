@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center py-4">
        <div class="flex items-center gap-1">

            {{-- Botão Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="flex items-center justify-center px-3 py-2 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-md cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left mr-1 text-[10px]"></i>
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="flex items-center justify-center px-3 py-2 text-xs font-medium text-green-700 bg-white border border-gray-200 rounded-md hover:bg-green-600 hover:text-white hover:border-green-600 transition-all duration-150">
                    <i class="fa-solid fa-chevron-left mr-1 text-[10px]"></i>
                    Anterior
                </a>
            @endif

            {{-- Números das Páginas --}}
            @foreach ($elements as $element)
                {{-- Separador "..." --}}
                @if (is_string($element))
                    <span class="px-3 py-2 text-xs font-medium text-gray-500 select-none">{{ $element }}</span>
                @endif

                {{-- Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-2 text-xs font-semibold border rounded-md cursor-default 
                                bg-green-700 hover:bg-green-600 text-white shadow-sm border-green-600">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" 
                               class="px-3 py-2 text-xs font-medium text-gray-700 border border-gray-200 rounded-md hover:bg-green-50 hover:text-green-700 hover:border-green-600 transition-all duration-150">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Botão Próximo --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="flex items-center justify-center px-3 py-2 text-xs font-medium text-green-700 bg-white border border-gray-200 rounded-md hover:bg-green-600 hover:text-white hover:border-green-600 transition-all duration-150">
                    Próximo
                    <i class="fa-solid fa-chevron-right ml-1 text-[10px]"></i>
                </a>
            @else
                <span class="flex items-center justify-center px-3 py-2 text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-md cursor-not-allowed">
                    Próximo
                    <i class="fa-solid fa-chevron-right ml-1 text-[10px]"></i>
                </span>
            @endif

        </div>
    </nav>
@endif
