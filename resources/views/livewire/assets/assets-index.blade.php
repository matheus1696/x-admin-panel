<div>
    <x-alert.flash />

    <x-page.header
        :title="'Ativos Operacionais'"
        :subtitle="'Consulte ativos liberados por estado, unidade e setor'"
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

    <x-page.table :empty-message="'Nenhum item encontrado para agrupamento.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="'Item'" />
                <x-page.table-th class="w-24 text-center" :value="'Total'" />
                <x-page.table-th class="w-24 text-center" :value="'Em uso'" />
                <x-page.table-th class="w-32 text-center" :value="'Em manutencao'" />
                <x-page.table-th class="w-28 text-center" :value="'Inserviveis'" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($groupedItems as $groupedItem)
                <tr>
                    <x-page.table-td>
                        <a
                            href="{{ route('assets.items.global', ['item' => $groupedItem->item]) }}"
                            class="text-blue-700 hover:text-blue-900 hover:underline"
                        >
                            {{ $groupedItem->item }}
                        </a>
                    </x-page.table-td>
                    <x-page.table-td class="text-center" :value="$groupedItem->quantity" />
                    <x-page.table-td class="text-center" :value="$groupedItem->in_use_count" />
                    <x-page.table-td class="text-center" :value="$groupedItem->maintenance_count" />
                    <x-page.table-td class="text-center" :value="$groupedItem->damaged_count" />
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
