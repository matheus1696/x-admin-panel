<div>
    <x-page.header :title="__('assets.reports.assets_by_state.title')" :subtitle="__('assets.reports.assets_by_state.subtitle')" icon="fa-solid fa-chart-pie" color="blue">
        <x-slot name="button">
            <x-button wire:click="exportCsv" :text="__('assets.reports.actions.export_csv')" icon="fa-solid fa-file-csv" variant="blue_outline" />
        </x-slot>
    </x-page.header>

    <x-page.table :empty-message="__('assets.reports.empty')">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="__('assets.reports.assets_by_state.table.state')" />
                <x-page.table-th class="text-center" :value="__('assets.reports.assets_by_state.table.total')" />
            </tr>
        </x-slot>
        <x-slot name="tbody">
            @foreach ($reportRows as $row)
                <tr>
                    <x-page.table-td :value="__('assets.states.'.strtolower($row['state']))" />
                    <x-page.table-td class="text-center" :value="$row['total']" />
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
