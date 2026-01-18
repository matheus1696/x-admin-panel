<div class="space-y-6">
    
    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header  title="Organograma" subtitle="Organograma da Secretária de Saúde de Caruaru" icon="fa-solid fa-sitemap">
        <x-slot name="button">
            <x-button text="Novo Setor" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>

    <!-- Filter -->
    <x-page.filter title="Filtros">
        <x-slot name="showBasic">

            {{-- Sigla do Setor --}}
            <div class="col-span-12 md:col-span-2">
                <x-form.label value="Sigla" />
                <x-form.input wire:model.live.debounce.500ms="filters.acronym" placeholder="Buscar por sigla..." />
            </div>

            {{-- Nome do Setor --}}
            <div class="col-span-12 md:col-span-6">
                <x-form.label value="Nome do Setor" />
                <x-form.input wire:model.live.debounce.500ms="filters.filter" placeholder="Buscar por setor..." />
            </div>

            {{-- Status --}}
            <div class="col-span-6 md:col-span-2">
                <x-form.label value="Status" />
                <x-form.select-livewire wire:model.live="filters.status" name="filters.status"
                    :options="[
                        ['value' => 'all', 'label' => 'Todos'],
                        ['value' => 'true', 'label' => 'Ativo'],
                        ['value' => 'false', 'label' => 'Desativado'],
                    ]"
                />
            </div>

            {{-- Itens por página --}}
            <div class="col-span-6 md:col-span-2">
                <x-form.label value="Itens por página" />
                <x-form.select-livewire wire:model.live="filters.perPage" name="filters.perPage"
                    :options="[
                        ['value' => 10, 'label' => '10'],
                        ['value' => 25, 'label' => '25'],
                        ['value' => 50, 'label' => '50'],
                        ['value' => 100, 'label' => '100']
                    ]"
                />
            </div>

        </x-slot>
    </x-page.filter>

    <!-- Table -->
    <x-page.table :pagination="$organizationCharts">
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Título" />
                <x-page.table-th class="text-center w-20" value="Status" />
                <x-page.table-th class="text-center w-20" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($organizationCharts as $organizationChart)
                <tr>
                    <x-page.table-td>
                        <div class="w-48 md:w-full truncate" title="{{ $organizationChart->acronym }} - {{ $organizationChart->title }}">
                            @for ($i = 0; $i < $organizationChart->number_hierarchy; $i++)
                                <span><i class="fa-solid fa-angle-right"></i></span>
                            @endfor                                       
                            <span class="pl-1">{{ $organizationChart->acronym }} - {{ $organizationChart->title }}</span>
                        </div>
                    </x-page.table-td>

                    <x-page.table-td class="text-center">
                        <div class="text-xs font-medium rounded-full py-0.5 px-1 {{ $organizationChart->status ? 'bg-green-300 text-green-700' : 'bg-red-300 text-red-700' }}">
                            {{ $organizationChart->status ? 'Ativo' : 'Desativado' }}
                        </div>
                    </x-page.table-td>

                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            <x-button.btn-table wire:click="status({{ $organizationChart->id }})" title="Alterar Status">
                                <i class="fa-solid fa-toggle-on"></i>
                            </x-button.btn-table>
                            <x-button.btn-table wire:click="edit({{ $organizationChart->id }})" title="Editar Tipo de Tarefa">
                                <i class="fa-solid fa-pen"></i>
                            </x-button.btn-table>
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>

    <!-- Modal -->
    <x-modal :show="$showModal" wire:key="organitation-chart-modal">
        @if ($modalKey === 'modal-form-create-organitation-chart')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Setor</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <x-form.label value="Sigla" />
                    <x-form.input wire:model.defer="acronym" placeholder="Sigla" required/>
                    <x-form.error :messages="$errors->get('acronym')" />
                </div>
                <div>
                    <x-form.label value="Setor" />
                    <x-form.input wire:model.defer="title" placeholder="Nome do Setor" required/>
                    <x-form.error :messages="$errors->get('title')" />
                </div>
                <div>
                    <x-form.label value="Setor Pai" />
                    <x-form.select-livewire wire:model.defer="hierarchy" name="hierarchy" :collection="$organizationCharts" value-field="id" label-acronym="acronym" label-field="title" />
                    <x-form.error :messages="$errors->get('hierarchy')" />
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-edit-organitation-chart')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Setor</h2>
            </x-slot>

            <form wire:submit.prevent="update" class="space-y-4">
                <div>
                    <x-form.label value="Sigla" />
                    <x-form.input wire:model.defer="acronym" placeholder="Sigla" required/>
                    <x-form.error :messages="$errors->get('acronym')" />
                </div>
                <div>
                    <x-form.label value="Setor" />
                    <x-form.input wire:model.defer="title" placeholder="Nome do Setor" required/>
                    <x-form.error :messages="$errors->get('title')" />
                </div>
                <div>
                    <x-form.label value="Setor Pai" />
                    <x-form.select-livewire wire:model.defer="hierarchy" name="hierarchy" :collection="$organizationCharts" label-acronym="acronym" label-field="title" valueField="id" default="Selecione o setor" :selected="old('hierarchy', $hierarchy ?? '')"/>
                    <x-form.error :messages="$errors->get('hierarchy')" />
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky"/>
                </div>
            </form>
        @endif
    </x-modal>

</div>
