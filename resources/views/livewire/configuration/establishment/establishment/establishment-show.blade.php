<div>
    
    <!-- Flash Message -->
    <x-alert.flash />

    <x-page.header icon="fa-solid fa-layer-group" title="Estabelecimento" subtitle="Dados da Unidade">
        <x-slot name="button">
            <x-button href="{{ route('configuration.manage.establishments.view') }}" text="Voltar página" icon="fa-solid fa-reply" variant="gray" />
        </x-slot>
    </x-page.header>

    <div class="border rounded-2xl shadow-sm my-6 {{ $establishment->is_active ? 'bg-white border-gray-200' : 'bg-red-50 border-red-200'}}">
        <!-- Header do Card -->
        <div class="px-6 py-4 border-b {{ $establishment->is_active ? 'border-gray-200' : 'border-red-200'}} flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide space-x-2">
                <span>Dados Principais</span>
                <span class="text-[11px] px-2 rounded-full {{ $establishment->is_active ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700'}}">{{ $establishment->is_active ? 'Ativada' : 'Inativada'}}</span>
            </h2>

            <div class="flex items-center justify-center gap-2">
                <x-button wire:click="edit({{ $establishment->id }})" value="Voltar" icon="fa-solid fa-pen-to-square" variant="gray_outline"/>
                <x-button wire:click="status({{ $establishment->id }})" value="Voltar" icon="fa-solid fa-toggle-on" variant="gray_outline" />
            </div>
        </div>

        <!-- Conteúdo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 p-6 text-sm">

            <!-- Código -->
            <div>
                <p class="text-xs text-gray-500">Código</p>
                <p class="font-semibold text-gray-900">{{ $establishment->code ?? '-' }}</p>
            </div>

            <!-- Nome -->
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-500">Nome do Estabelecimento</p>
                <p class="font-semibold text-gray-900">{{ $establishment->title }}</p>
            </div>

            <!-- Nome Fantasia -->
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-500">Nome Fantasia</p>
                <p class="font-medium text-gray-900">{{ $establishment->surname ?? '-' }}</p>
            </div>

            <!-- Tipo -->
            <div>
                <p class="text-xs text-gray-500">Tipo de Estabelecimento</p>
                <p class="font-medium text-gray-900">{{ $establishment->typeEstablishment->title ?? '-' }}</p>
            </div>

            <!-- Bloco Financeiro -->
            <div>
                <p class="text-xs text-gray-500">Bloco de Financiamento</p>
                <p class="font-medium text-gray-900">{{ $establishment->financialBlock->title ?? '-' }}</p>
            </div>

            <!-- Endereço -->
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-500">Endereço</p>
                <p class="font-medium text-gray-900">{{ $establishment->address }},{{ $establishment->number ?? 's/n' }}, {{ $establishment->district ?? '-' }}, {{ $establishment->RegionCity->title ?? '-' }} / {{ $establishment->RegionCity->RegionState->acronym ?? '-' }}</p>
            </div>

            <!-- Coordenadas -->
            <div>
                <p class="text-xs text-gray-500">Coordenadas</p>
                <p class="font-medium text-gray-900">
                    {{ $establishment->latitude ?? '-' }},
                    {{ $establishment->longitude ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    <div class="py-4 w-full border-t border-gray-200">
        <div>
            <!-- Header -->
            <div class="flex items-center justify-between px-2 pb-2 gap-3">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Departamentos</h3>
                    <div class="bg-green-50 hover:bg-green-100 border border-green-200 px-2 py-1 rounded-lg">
                        <x-button wire:click="createDepartment" text="Adicionar Departamento" icon="fa-solid fa-plus" variant="green_outline" />
                    </div>
                </div>                
            </div>

            <!-- Conteúdo -->
            <x-page.table>
                <x-slot name="thead">
                    <tr>
                        <x-page.table-th value="Setores" />
                        <x-page.table-th class="w-28" value="Contato" />
                        <x-page.table-th class="w-28" value="Ramal" />
                        <x-page.table-th class="w-28" value="Tipo" />
                        <x-page.table-th class="w-24" value="Ações" />
                    </tr>
                </x-slot>

                <x-slot name="tbody">
                    @foreach ($departments as $department)
                        <tr>
                            <x-page.table-td class="truncate" :value="$department->title" />
                            <x-page.table-td class="truncate" :value="$department->contact" />
                            <x-page.table-td class="truncate" :value="$department->extension" />
                            <x-page.table-td>
                                @if ($department->type_contact === "Without") Sem definição @endif
                                @if ($department->type_contact === "Internal") Interno @endif
                                @if ($department->type_contact === "Main") Principal @endif
                            </x-page.table-td>
                            <x-page.table-td>
                                <div class="flex items-center justify-center gap-2">
                                    <x-button.btn-table wire:click="editDepartment({{ $department->id }})" title="Editar Tipo de Tarefa">
                                        <i class="fa-solid fa-pen"></i>
                                    </x-button.btn-table>
                                </div>
                            </x-page.table-td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-page.table>

        </div>
    </div>

    <!-- Modal -->
    <x-modal :show="$showModal" wire:key="establishment-modal">
        @if ($modalKey === 'modal-form-edit-establishment')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Estabelecimento</h2>
            </x-slot>

            <form wire:submit.prevent="update" class="space-y-4">
                @include('livewire.configuration.establishment.establishment._partials.establishment-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Alterar" variant="sky" />
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-create-departament')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Departamento</h2>
            </x-slot>

            <form wire:submit.prevent="storeDepartment" class="space-y-4">
                @include('livewire.configuration.establishment.establishment._partials.department-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-edit-departament')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Departamento</h2>
            </x-slot>

            <form wire:submit.prevent="updateDepartment" class="space-y-4">
                @include('livewire.configuration.establishment.establishment._partials.department-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Alterar" variant="sky" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
