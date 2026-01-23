<div>

    {{-- Título --}}
    <x-page.header title="Contatos Internos" subtitle="Contatos da Secretaria de Saúde de Caruaru" icon="fa-solid fa-address-book">
        <x-slot name="button">
            <div class="w-52 md:w-96 flex items-center rounded-lg shadow bg-green-700 border border-green-700">
                <div class="px-3"> <i class="fa-solid fa-magnifying-glass text-white text-xs"></i> </div>
                <x-form.input wire:model.live.debounce.500ms="searchEstablishment" placeholder="Pesquise pela unidade, setor ou contato"/>
            </div>
        </x-slot>
    </x-page.header>

    {{-- Grid de Unidades --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-6">

        @foreach ($viewDepartments as $viewDepartment)
            @if ($viewDepartment->contact != null)
                <div class="group relative overflow-hidden rounded-2xl border border-green-500/20 bg-gradient-to-br from-green-700 via-green-800 to-green-900 shadow-md hover:shadow-xl transition-all duration-300">

                    {{-- Glow --}}
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute -inset-1 bg-gradient-to-r from-green-400/20 to-green-400/20 blur-xl"></div>
                    </div>

                    {{-- Conteúdo --}}
                    <div class="relative p-5 flex flex-col justify-between h-full">

                        {{-- Unidade --}}
                        <div>
                            <h2 class="text-sm font-semibold text-white leading-tight line-clamp-1 truncate">
                                {{ $viewDepartment->establishment->title }} 
                            </h2>

                            <p class="mt-1 text-xs text-green-100 line-clamp-1 truncate">
                                {{ $viewDepartment->establishment->address }},
                                {{ $viewDepartment->establishment->number }} – {{ $viewDepartment->establishment->district }}
                            </p>

                            <p class="mt-1 text-xs text-green-100 line-clamp-1 truncate">                            
                                @if ($viewDepartment->establishment->mainDepartment)
                                    {{ $viewDepartment->establishment->mainDepartment?->contact }}
                                @else
                                    <span class="text-xs italic text-green-100">
                                        Contato principal não informado
                                    </span>
                                @endif                            
                            </p>

                            <div class="mt-4">
                                <div class="flex items-center gap-2 text-sm text-white">
                                    <span class="inline-flex items-center justify-center size-10 rounded-lg bg-white/10">
                                        <i class="fa-solid fa-phone"></i>
                                    </span>
                                    <span class="flex flex-col font-medium">
                                        <span>{{ $viewDepartment?->title }}</span>
                                        <span>{{ $viewDepartment?->contact }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Ação --}}
                        <div class="mt-4">
                            <button wire:click="openDepartments({{ $viewDepartment->establishment->id }})" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 hover:bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200">
                                <i class="fa-solid fa-layer-group text-xs"></i> Ver setores
                            </button>
                        </div>

                    </div>
                </div>
            @endif
        @endforeach

    </div>

    {{-- Modal --}}
    <x-modal :show="$showModal" wire:key="contacts-modal">
        @if ($modalKey === 'modal-info-contact')
            <x-slot name="header">
                <div class="space-y-1">
                    <h2 class="text-sm font-semibold text-gray-800 uppercase">
                        {{ $establishmentTitle }}
                    </h2>
                    <p class="text-xs text-gray-500">
                        Contatos Internos
                    </p>
                </div>
            </x-slot>

            <div class="divide-y divide-gray-200">
                <div class="pb-4">
                    <x-form.input type="text" wire:model.live.debounce.500ms="searchDepartment" id="searchDepartment" placeholder="Buscar por setor ou contato"/>
                </div>
                @forelse ($departments as $department)
                    <div class="py-3 flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700">
                                {{ $department->title }}
                            </p>
                        </div>

                        <div class="text-right text-sm text-gray-600">
                            @if ($department->contact)
                                <div>
                                    <i class="fa-solid fa-phone text-gray-400"></i>
                                    {{ $department->contact }}
                                </div>
                            @endif
                            @if ($department->extension)
                                <div class="text-xs text-gray-500">
                                    Ramal {{ $department->extension }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-sm text-gray-500 text-center">
                        Nenhum setor cadastrado.
                    </p>
                @endforelse
            </div>
        @endif
    </x-modal>

</div>
