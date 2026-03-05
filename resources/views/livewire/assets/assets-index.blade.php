<div>
    <x-alert.flash />

    <x-page.header
        :title="'Lista de Ativos'"
        :subtitle="'Consulte o patrimonio por estado, unidade e setor'"
        icon="fa-solid fa-boxes-stacked"
    />

    <x-page.filter :title="'Filtros de ativos'">
        <div class="md:col-span-4">
            <x-form.label :value="'Busca'" />
            <x-form.input
                type="text"
                :placeholder="'Busque por codigo, descricao ou patrimonio do ativo'"
                wire:model.live.debounce.500ms="filters.search"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="'Estado'" />
            <x-form.select-livewire
                wire:model.live="filters.state"
                name="filters.state"
                :options="[
                    ['value' => 'all', 'label' => 'Todos'],
                    ['value' => 'IN_STOCK', 'label' => 'Em estoque'],
                    ['value' => 'IN_USE', 'label' => 'Em uso'],
                    ['value' => 'MAINTENANCE', 'label' => 'Em manutencao'],
                    ['value' => 'DAMAGED', 'label' => 'Inservivel'],
                ]"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="'Unidade'" />
            <x-form.select-livewire
                wire:model.live="filters.unitId"
                name="filters.unitId"
                :default="'Todas as unidades'"
                :options="collect($units)->map(fn ($unit) => ['value' => $unit->id, 'label' => $unit->title])->prepend(['value' => 'all', 'label' => 'Todos'])->values()->all()"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="'Setor'" />
            <x-form.select-livewire
                wire:model.live="filters.sectorId"
                name="filters.sectorId"
                :default="'Todos os setores'"
                :options="collect($sectors)->map(fn ($sector) => ['value' => $sector->id, 'label' => $sector->title])->prepend(['value' => 'all', 'label' => 'Todos'])->values()->all()"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="'Itens por pagina'" />
            <x-form.select-livewire
                wire:model.live="filters.perPage"
                name="filters.perPage"
                :options="[
                    ['value' => 10, 'label' => '10'],
                    ['value' => 25, 'label' => '25'],
                    ['value' => 50, 'label' => '50'],
                ]"
            />
        </div>
    </x-page.filter>

    @if ($isInvoiceScoped)
        <div class="mb-3 flex flex-col gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs text-blue-800 md:flex-row md:items-center md:justify-between">
            <span>{{ 'A lista esta filtrada pelos ativos vinculados a uma nota fiscal/item especifico.' }}</span>
            <x-button
                wire:click="clearInvoiceFilter"
                :text="'Limpar filtro da nota'"
                icon="fa-solid fa-filter-circle-xmark"
                variant="blue_text"
            />
        </div>
    @endif

    <x-page.table :pagination="$assets" :empty-message="'Nenhum ativo encontrado.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="'Ativo'" />
                <x-page.table-th class="hidden md:table-cell" :value="'Localizacao'" />
                <x-page.table-th class="hidden lg:table-cell" :value="'Origem'" />
                <x-page.table-th class="w-72 text-center" :value="'Acoes'" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($assets as $asset)
                <tr>
                    <x-page.table-td>
                        <div class="flex flex-col gap-1">
                            <span class="font-semibold text-gray-800">{{ $asset->code }}</span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="hidden md:table-cell">
                        <div class="flex flex-col gap-1 text-xs text-gray-600">
                            <span>{{ $asset->unit?->title ?? 'Sem unidade' }}</span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="hidden lg:table-cell">
                        <div class="flex flex-col gap-1 text-xs text-gray-600">
                            <span>{{ $asset->invoiceItem?->description ?? '-' }}</span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td>
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <x-button
                                :href="route('assets.show', $asset->uuid)"
                                icon="fa-solid fa-eye"
                                :title="'Visualizar'"
                                variant="blue_text"
                            />

                            @can('release', \App\Models\Assets\Asset::class)
                                <livewire:assets.release-asset-form :asset-uuid="$asset->uuid" :icon-only="true" :key="'release-index-'.$asset->id" />
                            @endcan

                            @can('transfer', \App\Models\Assets\Asset::class)
                                <livewire:assets.transfer-asset-form :asset-uuid="$asset->uuid" :icon-only="true" :key="'transfer-index-'.$asset->id" />
                            @endcan

                            @can('changeState', \App\Models\Assets\Asset::class)
                                <livewire:assets.change-state-form :asset-uuid="$asset->uuid" :icon-only="true" :key="'state-index-'.$asset->id" />
                            @endcan
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
