<div class="space-y-6">
    
    <!-- Flash Message -->
    <x-alert.flash />

    <x-page.header icon="fa-solid fa-briefcase" title="Configuração das Ocupações" subtitle="Lista de ocupações no sistema" />

    <x-page.filter title="Filtros de Ocupações">
        {{-- Filtros Básicos --}}
        <x-slot name="showBasic">
            <div class="md:col-span-2">
                <x-form.label value="Código" />
                <x-form.input type="text" placeholder="Buscar por código..." wire:model.live.debounce.500ms="code"/>
            </div>

            <div class="md:col-span-4">
                <x-form.label value="Nome" />
                <x-form.input type="text" placeholder="Buscar por nome..." wire:model.live.debounce.500ms="name"/>
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

    <x-page.table :pagination="$occupations">
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="w-20 text-center" value="Código" />
                <x-page.table-th value="Descrição" />
                <x-page.table-th class="w-28 text-center" value="Status" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($occupations as $occupation)
                <tr>
                    <x-page.table-td class="text-center" :value="$occupation->code"/>
                    <x-page.table-td class="truncate" :value="$occupation->title" title="{{ $occupation->title }}"/>
                    <x-page.table-td class="text-center">
                        <div class="text-xs font-medium rounded-full py-0.5 px-1 {{ $occupation->status ? 'bg-green-300 text-green-700' : 'bg-red-300 text-red-700' }}">
                            {{ $occupation->status ? 'Ativo' : 'Desativado' }}
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
