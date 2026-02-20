<div>
    
    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header  title="Tipos de Estabelecimento" subtitle="Tipos de Estabelecimento" icon="fa-solid fa-sitemap" />
    
    <x-page.filter title="Filtros dos Tipos de Estabelecimento">
        {{-- Filtros Básicos --}}
        <x-slot name="showBasic">
            <div class="md:col-span-6">
                <x-form.label value="Tipo" />
                <x-form.input type="text" placeholder="Buscar por tipo..." wire:model.live.debounce.500ms="name"/>
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

    <x-page.table :pagination="$establishmentTypes">
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Tipos de Estabelecimentos" />
                <x-page.table-th class="w-28 text-center" value="Status" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($establishmentTypes as $establishmentType)
                <tr>
                    <x-page.table-td class="truncate" :value="$establishmentType->title" />
                    <x-page.table-td class="text-center">
                        <x-page.table-status :condition="$establishmentType->is_active" />
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>

</div>
