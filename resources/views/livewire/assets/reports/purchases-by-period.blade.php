<div>
    <x-page.header :title="__('assets.reports.purchases_by_period.title')" :subtitle="__('assets.reports.purchases_by_period.subtitle')" icon="fa-solid fa-file-invoice-dollar" color="blue">
        <x-slot name="button">
            <x-button wire:click="exportCsv" :text="__('assets.reports.actions.export_csv')" icon="fa-solid fa-file-csv" variant="blue_outline" />
        </x-slot>
    </x-page.header>

    <x-page.filter :title="__('assets.reports.filters_title')" :accordion-open="true">
        <div class="md:col-span-4">
            <x-form.label :value="__('assets.reports.fields.start_date')" />
            <x-form.input type="date" wire:model.live="startDate" />
        </div>
        <div class="md:col-span-4">
            <x-form.label :value="__('assets.reports.fields.end_date')" />
            <x-form.input type="date" wire:model.live="endDate" />
        </div>
    </x-page.filter>

    <x-page.table :empty-message="__('assets.reports.empty')">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="__('assets.reports.purchases_by_period.table.invoice')" />
                <x-page.table-th class="hidden md:table-cell" :value="__('assets.reports.purchases_by_period.table.issue_date')" />
                <x-page.table-th :value="__('assets.reports.purchases_by_period.table.supplier')" />
                <x-page.table-th class="text-center" :value="__('assets.reports.purchases_by_period.table.items')" />
                <x-page.table-th class="text-center" :value="__('assets.reports.purchases_by_period.table.total')" />
            </tr>
        </x-slot>
        <x-slot name="tbody">
            @foreach ($reportRows as $row)
                <tr>
                    <x-page.table-td :value="$row->invoice_number" />
                    <x-page.table-td class="hidden md:table-cell" :value="optional($row->issue_date)->format('d/m/Y')" />
                    <x-page.table-td :value="$row->supplier_name" />
                    <x-page.table-td class="text-center" :value="$row->items_count" />
                    <x-page.table-td class="text-center" :value="number_format((float) $row->total_amount, 2, ',', '.')" />
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
