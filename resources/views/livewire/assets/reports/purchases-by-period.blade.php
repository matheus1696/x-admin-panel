<div>
    <x-page.header :title="'Compras por periodo'" :subtitle="'Notas fiscais emitidas no periodo informado'" icon="fa-solid fa-file-invoice-dollar" color="blue">
        <x-slot name="button">
            <x-button wire:click="exportCsv" :text="'Exportar CSV'" icon="fa-solid fa-file-csv" variant="blue_outline" />
        </x-slot>
    </x-page.header>

    <x-page.filter :title="'Filtros do relatorio'" :accordion-open="true">
        <div class="md:col-span-4">
            <x-form.label :value="'Data inicial'" />
            <x-form.input type="date" wire:model.live="startDate" />
        </div>
        <div class="md:col-span-4">
            <x-form.label :value="'Data final'" />
            <x-form.input type="date" wire:model.live="endDate" />
        </div>
    </x-page.filter>

    <x-page.table :empty-message="'Nenhum dado encontrado para os filtros informados.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="'Nota'" />
                <x-page.table-th class="hidden md:table-cell" :value="'Emissao'" />
                <x-page.table-th :value="'Fornecedor'" />
                <x-page.table-th class="text-center" :value="'Itens'" />
                <x-page.table-th class="text-center" :value="'Valor'" />
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
