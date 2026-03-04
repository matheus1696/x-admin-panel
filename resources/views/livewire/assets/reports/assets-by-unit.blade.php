<div>
    <x-page.header :title="__('assets.reports.assets_by_unit.title')" :subtitle="__('assets.reports.assets_by_unit.subtitle')" icon="fa-solid fa-building" color="blue">
        <x-slot name="button">
            <x-button wire:click="exportCsv" :text="__('assets.reports.actions.export_csv')" icon="fa-solid fa-file-csv" variant="blue_outline" />
        </x-slot>
    </x-page.header>

    <x-page.table :empty-message="__('assets.reports.empty')">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="__('assets.reports.assets_by_unit.table.unit')" />
                <x-page.table-th class="text-center" :value="__('assets.reports.assets_by_unit.table.total')" />
                <x-page.table-th class="text-center" :value="__('assets.states.in_stock')" />
                <x-page.table-th class="text-center" :value="__('assets.states.released')" />
                <x-page.table-th class="text-center" :value="__('assets.states.in_use')" />
                <x-page.table-th class="text-center" :value="__('assets.states.maintenance')" />
            </tr>
        </x-slot>
        <x-slot name="tbody">
            @foreach ($reportRows as $row)
                <tr>
                    <x-page.table-td :value="$row->unit_title" />
                    <x-page.table-td class="text-center" :value="$row->total_assets" />
                    <x-page.table-td class="text-center" :value="$row->in_stock_count" />
                    <x-page.table-td class="text-center" :value="$row->released_count" />
                    <x-page.table-td class="text-center" :value="$row->in_use_count" />
                    <x-page.table-td class="text-center" :value="$row->maintenance_count" />
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
