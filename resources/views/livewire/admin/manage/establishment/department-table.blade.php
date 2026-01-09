<div>
    <div class="border-t border-gray-200"></div>

    <div x-data="{ open: false }" :class="open ? 'mt-1' : 'mt-6 rounded-2xl border border-gray-200 bg-white shadow-sm'">

        <!-- Header -->
        <div @click="open = !open" class="flex items-center justify-between cursor-pointer select-none">
            <div class="flex justify-between items-center gap-3 px-6 py-4 w-full">
                <!-- Título -->
                <div class="flex-1 flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        Departamentos
                    </h3>

                    <!-- Botão adicionar (não fecha accordion) -->
                    <div @click.stop>
                        <x-modal title="Adicionar Departamento">
                            <x-slot name="button">
                                <x-button.btn-table title="Adicionar Departamento">
                                    <i class="fa-solid fa-plus"></i>
                                </x-button.btn-table>
                            </x-slot>

                            <x-slot name="body">

                                <form wire:submit.prevent="store" class="space-y-4" @click.stop>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                                        <!-- Matriculation -->
                                        <div class="col-span-2 md:col-span-2">
                                            <x-form.label value="Sigla" for="acronym" />
                                            <x-form.input id="acronym" wire:model.defer="acronym" placeholder="SMS-GAB"/>
                                            <x-form.error :messages="$errors->get('acronym')" />
                                        </div>

                                        <!-- Matriculation -->
                                        <div class="col-span-2 md:col-span-2">
                                            <x-form.label value="Departamento" for="title" />
                                            <x-form.input id="title" wire:model.defer="title" placeholder="Nome do Departamento"/>
                                            <x-form.error :messages="$errors->get('title')" />
                                        </div>

                                    </div>

                                    <div class="flex items-center justify-center gap-3">
                                        <x-button.btn type="submit" class="w-full text-white bg-green-600 hover:bg-green-800" value="Adicionar Departamento"/>
                                    </div>
                                </form>

                                <!-- Mensagem de sucesso -->
                                @if ($successCreate)
                                    <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; isModalOpen = false }, 2000)" x-show="show" x-transition.opacity.scale.95 class="mt-4 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm">
                                        <!-- Ícone -->
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-200 text-green-600">
                                            <i class="fa-solid fa-check"></i>
                                        </div>

                                        <!-- Texto -->
                                        <div class="flex-1">
                                            <p class="font-medium">
                                                {{ $successCreate }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </x-slot>
                        </x-modal>
                    </div>
                </div>

                <!-- Busca -->
                <div class="w-60" :class="open ? 'block' : 'hidden'" @click.stop >
                    <x-form.input type="text" placeholder="Buscar por nome ou código..." wire:model.live.debounce.500ms="search" />
                </div>

                <!-- Ícone Accordion -->
                <span class="h-6 w-6 flex items-center justify-center rounded-full bg-green-100 text-green-700 transition-transform duration-300" :class="{ 'rotate-180': open }" >
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </span>
            </div>
        </div>

        <!-- Conteúdo -->
        <div x-show="open" x-collapse x-cloak>
            <x-page.table :pagination="$departments">
                <x-slot name="thead">
                    <tr>
                        <x-page.table-th class="w-20" value="Código" />
                        <x-page.table-th value="Setores" />
                        <x-page.table-th class="w-28" value="Status" />
                        <x-page.table-th class="w-24" value="Ações" />
                    </tr>
                </x-slot>

                <x-slot name="tbody">
                    @foreach ($departments as $department)
                        <tr>
                            <x-page.table-td :value="$department->acronym" />
                            <x-page.table-td 
                                class="truncate" 
                                :value="$department->title" 
                                title="{{ $department->title }}"
                            />
                            <x-page.table-status :condition="$department->status" />
                            <x-page.table-td>
                                <div class="flex items-center justify-center gap-2">
                                    <x-button.btn-table title="Editar Departamento">
                                        <!-- Botão adicionar (não fecha accordion) -->
                                        <div @click.stop>
                                            <x-modal title="Editar Departamento">
                                                <x-slot name="button">
                                                    <x-button.btn-table title="Editar Departamento">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </x-button.btn-table>
                                                </x-slot>

                                                <x-slot name="body">

                                                    <form wire:submit.prevent="store" class="space-y-4" @click.stop>

                                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                                                            <!-- Matriculation -->
                                                            <div class="col-span-2 md:col-span-2">
                                                                <x-form.label value="Sigla" for="acronym" />
                                                                <x-form.input id="acronym" wire:model.defer="acronym" placeholder="SMS-GAB" value="$department->acronym"/>
                                                                <x-form.error :messages="$errors->get('acronym')" />
                                                            </div>

                                                            <!-- Matriculation -->
                                                            <div class="col-span-2 md:col-span-2">
                                                                <x-form.label value="Departamento" for="title" />
                                                                <x-form.input id="title" wire:model.defer="title" placeholder="Nome do Departamento" value="$department->title"/>
                                                                <x-form.error :messages="$errors->get('title')" />
                                                            </div>

                                                        </div>

                                                        <div class="flex items-center justify-center gap-3">
                                                            <x-button.btn type="submit" class="w-full text-white bg-green-600 hover:bg-green-800" value="Editar Departamento"/>
                                                        </div>
                                                    </form>

                                                    <!-- Mensagem de sucesso -->
                                                    @if ($successCreate)
                                                        <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; isModalOpen = false }, 2000)" x-show="show" x-transition.opacity.scale.95 class="mt-4 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm">
                                                            <!-- Ícone -->
                                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-200 text-green-600">
                                                                <i class="fa-solid fa-check"></i>
                                                            </div>

                                                            <!-- Texto -->
                                                            <div class="flex-1">
                                                                <p class="font-medium">
                                                                    {{ $successCreate }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </x-slot>
                                            </x-modal>
                                        </div>
                                    </x-button.btn-table>
                                </div>
                            </x-page.table-td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-page.table>
        </div>

    </div>

</div>
