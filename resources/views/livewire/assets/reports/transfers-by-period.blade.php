<div>
    <x-page.header :title="'Transferencias por periodo'" :subtitle="'Volume de transferencias registradas no periodo'" icon="fa-solid fa-right-left" color="blue">
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
                <x-page.table-th :value="'Data'" />
                <x-page.table-th class="text-center" :value="'Transferencias'" />
            </tr>
        </x-slot>
        <x-slot name="tbody">
            @foreach ($reportRows as $row)
                <tr>
                    <x-page.table-td :value="\Carbon\Carbon::parse($row->event_date)->format('d/m/Y')" />
                    <x-page.table-td class="text-center" :value="$row->total" />
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
