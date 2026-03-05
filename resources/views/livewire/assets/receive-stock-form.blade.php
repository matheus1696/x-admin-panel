<div>
    <x-button
        wire:click="open"
        :text="'Receber'"
        icon="fa-solid fa-boxes-packing"
        variant="green_text"
        :disabled="$remainingQuantity === 0"
    />

    <x-modal :show="$showModal" wire:key="receive-stock-modal-{{ $assetInvoiceItemId }}">
        @if ($modalKey === 'receive-stock')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">{{ 'Receber em estoque' }}</h2>
            </x-slot>

            <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-12 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">
                    {{ 'Saldo disponivel para este item: '.$remainingQuantity.' unidade(s).' }}
                </div>

                <div class="md:col-span-4">
                    <x-form.label :value="'Quantidade'" />
                    <x-form.input type="number" min="1" wire:model="quantity" />
                    <x-form.error for="quantity" />
                </div>

                <div class="md:col-span-4">
                    <x-form.label :value="'Data de aquisicao'" />
                    <x-form.input type="date" wire:model="acquiredDate" />
                    <x-form.error for="acquiredDate" />
                </div>

                <div class="md:col-span-4">
                    <x-form.label :value="'Marca'" />
                    <x-form.input type="text" wire:model="brand" />
                    <x-form.error for="brand" />
                </div>

                <div class="md:col-span-8">
                    <x-form.label :value="'Descricao'" />
                    <x-form.input type="text" wire:model="description" />
                    <x-form.error for="description" />
                </div>

                <div class="md:col-span-4">
                    <x-form.label :value="'Modelo'" />
                    <x-form.input type="text" wire:model="model" />
                    <x-form.error for="model" />
                </div>

                <div class="md:col-span-12 flex justify-end gap-2">
                    <x-button type="button" wire:click="closeModal" :text="'Cancelar'" variant="gray_outline" />
                    <x-button type="submit" :text="'Confirmar entrada'" icon="fa-solid fa-box-open" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
