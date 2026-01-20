<div class="space-y-6">
    
    <!-- Flash Message -->
    <x-alert.flash />

    <x-page.header icon="fa-solid fa-layer-group" title="Estabelecimento" subtitle="Gerencie os estabelecimento">
        <x-slot name="button">
            <x-button wire:click="create" text="Novo Estabelecimento" icon="fa-solid fa-plus" />
        </x-slot>
    </x-page.header>
    
    <x-page.filter title="Filtros de Estabelecimentos">
        {{-- Filtros Básicos --}}
        <x-slot name="showBasic">
            <div class="md:col-span-2">
                <x-form.label value="Código" />
                <x-form.input type="text" placeholder="Buscar por código..." wire:model.live.debounce.500ms="code"/>
            </div>

            <div class="md:col-span-6">
                <x-form.label value="Estabelecimento" />
                <x-form.input type="text" placeholder="Buscar por título do estabelecimento..." wire:model.live.debounce.500ms="name"/>
            </div>

            <div class="md:col-span-2">
                <x-form.label value="Status" />
                <x-form.select-livewire wire:model.live="status" name="status" default="Selecione o status"
                    :options="[
                        ['value' => 'all', 'label' => 'Todos'],
                        ['value' => true, 'label' => 'Ativo'],
                        ['value' => false, 'label' => 'Inativo'],
                    ]"
                />
            </div>

            <div class="md:col-span-2">
                <x-form.label value="Itens por página" />
                <x-form.select-livewire 
                    wire:model.live="perPage"
                    name="perPage"
                    :options="[
                        ['value' => 10, 'label' => '10'],
                        ['value' => 25, 'label' => '25'],
                        ['value' => 50, 'label' => '50'],
                        ['value' => 100, 'label' => '100']
                    ]"
                    default="Selecione a quantidade de itens"
                />
            </div>
        </x-slot>

        {{-- Filtros Avançados --}}
        <x-slot name="showAdvanced">
            <div class="md:col-span-2">
                <x-form.label value="Ordenar por" />                
                <x-form.select-livewire 
                    wire:model.live="sort"
                    name="sort"
                    :options="[
                        ['value' => 'name_asc', 'label' => 'Nome (A–Z)'],
                        ['value' => 'name_desc', 'label' => 'Nome (Z–A)'],
                    ]"
                />
            </div>
        </x-slot>
    </x-page.filter>

    <x-page.table :pagination="$establishments">
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="w-20" value="Código" />
                <x-page.table-th value="Estabelecimentos" />
                <x-page.table-th class="w-28" value="Status" />
                <x-page.table-th class="w-24" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($establishments as $establishment)
                <tr>
                    <x-page.table-td :value="$establishment->code" />
                    <x-page.table-td class="truncate" :value="$establishment->title" title="{{ $establishment->title }}"/>
                    <x-page.table-status :condition="$establishment->status" />
                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                                <x-button.btn-table title="Detalhe do Estabelecimento">
                                    <a href="{{ route('admin.establishments.show', $establishment->code) }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </x-button.btn-table>
                                <x-button.btn-table wire:click="status({{ $establishment->id }})" title="Alterar Status">
                                    <i class="fa-solid fa-toggle-on"></i>
                                </x-button.btn-table>
                            </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>

    <!-- Modal -->
    <x-modal :show="$showModal" wire:key="establishment-modal">
        @if ($modalKey === 'modal-form-create-establishment')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Setor</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.configuration.establishment.establishment._partials.establishment-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
