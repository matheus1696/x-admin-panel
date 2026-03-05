<div>
    <x-page.header :title="'Relatorio de ativos por estado'" :subtitle="'Consolidado atual por estado patrimonial'" icon="fa-solid fa-chart-pie" color="blue">
        <x-slot name="button">
            <x-button wire:click="exportCsv" :text="'Exportar CSV'" icon="fa-solid fa-file-csv" variant="blue_outline" />
        </x-slot>
    </x-page.header>

    <x-page.table :empty-message="'Nenhum dado encontrado para os filtros informados.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="'Estado'" />
                <x-page.table-th class="text-center" :value="'Total'" />
            </tr>
        </x-slot>
        <x-slot name="tbody">
            @foreach ($reportRows as $row)
                <tr>
                    <x-page.table-td :value="[
                        'IN_STOCK' => 'Em estoque',
                        'IN_USE' => 'Em uso',
                        'MAINTENANCE' => 'Em manutencao',
                        'DAMAGED' => 'Inservivel',
                    ][strtoupper((string) $row['state'])] ?? (string) $row['state']" />
                    <x-page.table-td class="text-center" :value="$row['total']" />
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
