<div>
    <x-alert.flash />

    <x-page.header
        :title="'Entrada de Ativos'"
        :subtitle="'Gerencie notas fiscais vinculadas ao patrimonio'"
        icon="fa-solid fa-file-invoice-dollar"
    >
        <x-slot name="button">
            <x-button
                :href="route('assets.invoices.create')"
                :text="'Nova nota'"
                icon="fa-solid fa-plus"
            />
        </x-slot>
    </x-page.header>

    <x-page.filter :title="'Filtros de notas'">
        <div class="md:col-span-8">
            <x-form.label :value="'Busca'" />
            <x-form.input
                type="text"
                :placeholder="'Busque por numero da nota ou fornecedor'"
                wire:model.live.debounce.500ms="filters.search"
            />
        </div>

        <div class="md:col-span-4">
            <x-form.label :value="'Itens por pagina'" />
            <x-form.select-livewire
                wire:model.live="filters.perPage"
                name="filters.perPage"
                :options="[
                    ['value' => 10, 'label' => '10'],
                    ['value' => 25, 'label' => '25'],
                    ['value' => 50, 'label' => '50'],
                ]"
                :default="'Selecione a quantidade'"
            />
        </div>
    </x-page.filter>

    <x-page.table :pagination="$invoices" :empty-message="'Nenhuma nota fiscal encontrada.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="'Nota'" />
                <x-page.table-th class="hidden md:table-cell" :value="'Fornecedor'" />
                <x-page.table-th class="hidden lg:table-cell" :value="'Emissao'" />
                <x-page.table-th class="w-24 text-center" :value="'Itens'" />
                <x-page.table-th class="w-32 text-center" :value="'Acoes'" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($invoices as $invoice)
                <tr>
                    <x-page.table-td>
                        <div class="flex flex-col gap-1">
                            <span class="font-semibold text-gray-800">{{ $invoice->invoice_number }}</span>
                            <span class="text-xs text-gray-500">
                                {{ 'Total' }}: {{ number_format((float) $invoice->total_amount, 2, ',', '.') }}
                            </span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="hidden md:table-cell">
                        <div class="flex flex-col gap-1">
                            <span>{{ $invoice->supplier_name }}</span>
                            @if ($invoice->supplier_document)
                                <span class="text-xs text-gray-500">{{ $invoice->supplier_document }}</span>
                            @endif
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="hidden lg:table-cell" :value="optional($invoice->issue_date)->format('d/m/Y')" />
                    <x-page.table-td class="text-center" :value="$invoice->items_count" />
                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            <x-button
                                :href="route('assets.invoices.show', $invoice->uuid)"
                                icon="fa-solid fa-eye"
                                :title="'Visualizar'"
                                variant="blue_text"
                            />
                            @if (! $invoice->is_finalized)
                                <x-button
                                    :href="route('assets.invoices.edit', $invoice->uuid)"
                                    icon="fa-solid fa-pen"
                                    :title="'Editar'"
                                    variant="green_text"
                                />
                            @endif
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
