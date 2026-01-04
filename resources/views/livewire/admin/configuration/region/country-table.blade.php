<div>
    <x-page.filter title="Filtros de Países">
        {{-- Filtros Básicos --}}
        <x-slot name="showBasic">
            <div class="md:col-span-6">
                <x-form.label value="País" />
                <x-form.input type="text" placeholder="Buscar por país..." wire:model.live.debounce.500ms="name"/>
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

    <x-page.table :pagination="$countries">
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Países" />
                <x-page.table-th class="w-28 text-center" value="Status" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($countries as $country)
                <tr>
                    <x-page.table-td :value="$country->title" />
                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            @if ($country->status)
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-xs font-medium text-green-700">Ativo</span>
                            @else
                                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                <span class="text-xs font-medium text-red-700">Inativo</span>
                            @endif
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
