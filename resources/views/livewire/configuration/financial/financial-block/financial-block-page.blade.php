<div>
    
    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header icon="fa-solid fa-coins" title="Blocos de Financiamento" subtitle="Gerencie os blocos de financiamento" />    
    
    <x-page.filter title="Filtros de Blocos de Financiamento">
        {{-- Filtros Básicos --}}
        <x-slot name="showBasic">
            <div class="md:col-span-2">
                <x-form.label value="Sigla" />
                <x-form.input type="text" placeholder="Buscar por sigla..." wire:model.live.debounce.500ms="acronym"/>
            </div>

            <div class="md:col-span-4">
                <x-form.label value="Bloco de Financiamento" />
                <x-form.input type="text" placeholder="Buscar por bloco..." wire:model.live.debounce.500ms="name"/>
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
                        ['value' => 'email_asc', 'label' => 'Email (A–Z)'],
                        ['value' => 'email_desc', 'label' => 'Email (Z–A)'],
                    ]"
                />
            </div>
        </x-slot>
    </x-page.filter>

    <x-page.table :pagination="$financialBlocks">
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="w-24 text-center" value="Siglas" />
                <x-page.table-th class="truncate" value="Blocos de Financiamentos" />
                <x-page.table-th class="w-28 text-center" value="Status" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($financialBlocks as $financialBlock)
                <tr>
                    <x-page.table-td class="text-center" :value="$financialBlock->acronym" />
                    <x-page.table-td :value="$financialBlock->title" />
                    <x-page.table-td class="text-center">
                        <x-page.table-status :condition="$financialBlock->is_active" />
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>

</div>
