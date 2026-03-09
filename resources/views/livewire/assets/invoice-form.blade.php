<div>
    <x-alert.flash />

    <x-page.header
        :title="$invoiceId ? 'Editar nota fiscal' : 'Nova nota fiscal'"
        :subtitle="$invoiceId ? 'Ajuste os dados da nota cadastrada' : 'Cadastre uma nota para entrada de ativos'"
        icon="fa-solid fa-receipt"
    >
        <x-slot name="button">
            <x-button
                :href="$invoiceUuid ? route('assets.invoices.show', $invoiceUuid) : route('assets.invoices.index')"
                :text="'Cancelar'"
                icon="fa-solid fa-arrow-left"
                variant="gray_outline"
            />
        </x-slot>
    </x-page.header>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 md:grid-cols-12">
            <div class="md:col-span-4">
                <x-form.label :value="'Numero da nota'" />
                <x-form.input
                    type="text"
                    wire:model="invoiceNumber"
                    placeholder="Ex.: 256, 1.658, 25.526"
                    data-mask="invoiceNumber"
                    maxlength="15"
                />
                <x-form.error for="invoiceNumber" />
            </div>

            <div class="md:col-span-2">
                <x-form.label :value="'Serie'" />
                <x-form.input type="text" wire:model="invoiceSeries" placeholder="Ex.: A1" />
                <x-form.error for="invoiceSeries" />
            </div>

            <div class="md:col-span-3">
                <x-form.label :value="'Ordem de fornecimento'" />
                <x-form.input
                    type="text"
                    wire:model="supplyOrder"
                    placeholder="0000-0000 ou 00000-0000"
                    data-mask="supplyOrder"
                    maxlength="10"
                />
                <x-form.error for="supplyOrder" />
            </div>

            <div class="md:col-span-3">
                <x-form.label :value="'Bloco financeiro'" />
                <x-form.select-livewire
                    wire:model="financialBlockId"
                    name="financialBlockId"
                    default="Selecione o bloco"
                    :collection="$financialBlocks"
                    label-field="title"
                    label-acronym="acronym"
                    value-field="id"
                />
                <x-form.error for="financialBlockId" />
            </div>

            <div class="md:col-span-4">
                <x-form.label :value="'Fornecedor'" />
                <x-form.select-livewire
                    wire:model="supplierId"
                    name="supplierId"
                    default="Selecione o fornecedor"
                    :collection="$suppliers"
                    label-field="title"
                    label-acronym="document"
                    value-field="id"
                />
                <x-form.error for="supplierId" />
            </div>

            @can('administration.manage.suppliers')
                <div class="md:col-span-2 flex items-end">
                    <x-button
                        :href="route('administration.manage.suppliers')"
                        :text="'Novo fornecedor'"
                        icon="fa-solid fa-plus"
                        variant="blue_outline"
                        class="w-full"
                    />
                </div>
            @endcan

            <div class="md:col-span-3">
                <x-form.label :value="'Data de emissao'" />
                <x-form.input type="date" wire:model="issueDate" placeholder="Selecione a data de emissao" max="{{ now()->toDateString() }}" />
                <x-form.error for="issueDate" />
            </div>

            <div class="md:col-span-3">
                <x-form.label :value="'Data de recebimento'" />
                <x-form.input type="date" wire:model="receivedDate" placeholder="Selecione a data de recebimento" max="{{ now()->toDateString() }}" />
                <x-form.error for="receivedDate" />
            </div>

            <div class="md:col-span-2">
                <x-form.label :value="'Valor total'" />
                <x-form.input type="text" :value="$totalAmount" disabled />
                <p class="mt-1 text-xs text-gray-500">O valor total Ã© calculado automaticamente pelos itens da nota.</p>
            </div>

            <div class="md:col-span-12">
                <x-form.label :value="'Observacoes'" />
                <x-form.textarea wire:model="notes" rows="4" placeholder="Observacoes adicionais da nota fiscal" />
                <x-form.error for="notes" />
            </div>

            <div class="md:col-span-12 flex justify-end gap-2 pt-2">
                <x-button
                    :href="$invoiceUuid ? route('assets.invoices.show', $invoiceUuid) : route('assets.invoices.index')"
                    :text="'Cancelar'"
                    variant="gray_outline"
                />
                <x-button
                    type="submit"
                    :text="$invoiceId ? 'Editar' : 'Salvar'"
                    icon="fa-solid fa-floppy-disk"
                />
            </div>
        </form>
    </div>
</div>
