<div class="p-6 bg-gray-100 overflow-x-hidden">

    <h1 class="text-3xl font-bold mb-8 text-center">Organograma</h1>

    @if($organizationCharts->isNotEmpty())
        <ul class="flex justify-center">
            @foreach($organizationCharts as $node)
                @php $level = 1; @endphp
                <li class="flex flex-col items-center">
                    {{-- Raiz --}}
                    <div class="px-6 py-3 rounded-lg shadow font-semibold text-white bg-blue-600 text-center">
                        {{ $node->name }}
                    </div>

                    {{-- Filhos --}}
                    @if($node->children->isNotEmpty())
                        <ul class="flex justify-center gap-6 mt-6">
                            @foreach($node->children as $child)
                                <li class="flex flex-col items-center">
                                    <div class="px-4 py-2 rounded-lg shadow font-semibold text-white bg-green-500 text-center">
                                        {{ $child->name }}
                                    </div>

                                    {{-- Filhos do filho --}}
                                    @if($child->children->isNotEmpty())
                                        <ul class="flex justify-center gap-6 mt-6">
                                            @foreach($child->children as $grandchild)
                                                <li class="flex flex-col items-center">
                                                    <div class="px-4 py-2 rounded-lg shadow font-semibold text-white bg-yellow-500 text-center">
                                                        {{ $grandchild->name }}
                                                    </div>

                                                    {{-- Filhos do filho --}}
                                                    @if($grandchild->children->isNotEmpty())
                                                        <ul class="flex justify-center gap-6 mt-6">
                                                            @foreach($grandchild->children as $greatGrandchild)
                                                                <li class="flex flex-col items-center">
                                                                    <div class="px-4 py-2 rounded-lg shadow font-semibold text-white bg-yellow-500 text-center">
                                                                        {{ $greatGrandchild->name }}
                                                                    </div>

                                                                    {{-- Continue recursivamente se precisar --}}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-center text-gray-500">Nenhum cargo cadastrado.</p>
    @endif

</div>
