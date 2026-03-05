<div>
    <x-page.header :title="'Relatorio de ativos por unidade'" :subtitle="'Distribuicao patrimonial consolidada por unidade'" icon="fa-solid fa-building" color="blue">
        <x-slot name="button">
            <x-button wire:click="exportCsv" :text="'Exportar CSV'" icon="fa-solid fa-file-csv" variant="blue_outline" />
        </x-slot>
    </x-page.header>

    <x-page.table :empty-message="'Nenhum dado encontrado para os filtros informados.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="'Unidade'" />
                <x-page.table-th class="text-center" :value="'Total'" />
                <x-page.table-th class="text-center" :value="'Em estoque'" />
                <x-page.table-th class="text-center" :value="'Em uso'" />
                <x-page.table-th class="text-center" :value="'Em manutencao'" />
                <x-page.table-th class="text-center" :value="'Inservivel'" />
            </tr>
        </x-slot>
        <x-slot name="tbody">
            @foreach ($reportRows as $row)
                <tr>
                    <x-page.table-td :value="$row->unit_title" />
                    <x-page.table-td class="text-center" :value="$row->total_assets" />
                    <x-page.table-td class="text-center" :value="$row->in_stock_count" />
                    <x-page.table-td class="text-center" :value="$row->in_use_count" />
                    <x-page.table-td class="text-center" :value="$row->maintenance_count" />
                    <x-page.table-td class="text-center" :value="$row->unserviceable_count" />
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
