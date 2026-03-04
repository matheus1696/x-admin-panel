<div>
    <x-alert.flash />

    <x-page.header
        :title="__('assets.assets_index.title')"
        :subtitle="__('assets.assets_index.subtitle')"
        icon="fa-solid fa-boxes-stacked"
        color="amber"
    />

    <x-page.filter :title="__('assets.assets_index.filters_title')">
        <div class="md:col-span-4">
            <x-form.label :value="__('assets.assets_index.fields.search')" />
            <x-form.input
                type="text"
                :placeholder="__('assets.assets_index.placeholders.search')"
                wire:model.live.debounce.500ms="filters.search"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="__('assets.assets_index.fields.state')" />
            <x-form.select-livewire
                wire:model.live="filters.state"
                name="filters.state"
                :options="[
                    ['value' => 'all', 'label' => __('assets.filters.all')],
                    ['value' => 'IN_STOCK', 'label' => __('assets.states.in_stock')],
                    ['value' => 'RELEASED', 'label' => __('assets.states.released')],
                    ['value' => 'IN_USE', 'label' => __('assets.states.in_use')],
                    ['value' => 'MAINTENANCE', 'label' => __('assets.states.maintenance')],
                    ['value' => 'DAMAGED', 'label' => __('assets.states.damaged')],
                    ['value' => 'RETURNED_TO_PATRIMONY', 'label' => __('assets.states.returned_to_patrimony')],
                ]"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="__('assets.assets_index.fields.unit')" />
            <x-form.select-livewire
                wire:model.live="filters.unitId"
                name="filters.unitId"
                :default="__('assets.filters.all_units')"
                :options="collect($units)->map(fn ($unit) => ['value' => $unit->id, 'label' => $unit->title])->prepend(['value' => 'all', 'label' => __('assets.filters.all')])->values()->all()"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="__('assets.assets_index.fields.sector')" />
            <x-form.select-livewire
                wire:model.live="filters.sectorId"
                name="filters.sectorId"
                :default="__('assets.filters.all_sectors')"
                :options="collect($sectors)->map(fn ($sector) => ['value' => $sector->id, 'label' => $sector->title])->prepend(['value' => 'all', 'label' => __('assets.filters.all')])->values()->all()"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="__('assets.assets_index.fields.per_page')" />
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
            <span>{{ __('assets.assets_index.invoice_scope_notice') }}</span>
            <x-button
                wire:click="clearInvoiceFilter"
                :text="__('assets.assets_index.actions.clear_invoice_filter')"
                icon="fa-solid fa-filter-circle-xmark"
                variant="blue_text"
            />
        </div>
    @endif

    <x-page.table :pagination="$assets" :empty-message="__('assets.assets_index.empty')">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="__('assets.assets_index.table.asset')" />
                <x-page.table-th class="hidden md:table-cell" :value="__('assets.assets_index.table.location')" />
                <x-page.table-th class="hidden lg:table-cell" :value="__('assets.assets_index.table.invoice')" />
                <x-page.table-th class="w-24 text-center" :value="__('assets.assets_index.table.actions')" />
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
                            <span>{{ $asset->unit?->title ?? __('assets.assets_index.labels.no_unit') }}</span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="hidden lg:table-cell">
                        <div class="flex flex-col gap-1 text-xs text-gray-600">
                            <span>{{ $asset->invoiceItem?->description ?? '-' }}</span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td>
                        <div class="flex items-center justify-center">
                            <x-button
                                :href="route('assets.show', $asset->uuid)"
                                icon="fa-solid fa-eye"
                                :title="__('assets.actions.view')"
                                variant="blue_text"
                            />
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
