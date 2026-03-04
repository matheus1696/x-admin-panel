<div>
    <x-button
        wire:click="open"
        :text="__('assets.invoices.receive_stock.action')"
        icon="fa-solid fa-boxes-packing"
        variant="green_text"
        :disabled="$remainingQuantity === 0"
    />

    <x-modal :show="$showModal" wire:key="receive-stock-modal-{{ $assetInvoiceItemId }}">
        @if ($modalKey === 'receive-stock')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">{{ __('assets.invoices.receive_stock.title') }}</h2>
            </x-slot>

            <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-12 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">
                    {{ __('assets.invoices.receive_stock.remaining', ['remaining' => $remainingQuantity]) }}
                </div>

                <div class="md:col-span-4">
                    <x-form.label :value="__('assets.invoices.receive_stock.fields.quantity')" />
                    <x-form.input type="number" min="1" wire:model="quantity" />
                    <x-form.error for="quantity" />
                </div>

                <div class="md:col-span-4">
                    <x-form.label :value="__('assets.invoices.receive_stock.fields.acquired_date')" />
                    <x-form.input type="date" wire:model="acquiredDate" />
                    <x-form.error for="acquiredDate" />
                </div>

                <div class="md:col-span-4">
                    <x-form.label :value="__('assets.invoices.receive_stock.fields.brand')" />
                    <x-form.input type="text" wire:model="brand" />
                    <x-form.error for="brand" />
                </div>

                <div class="md:col-span-8">
                    <x-form.label :value="__('assets.invoices.receive_stock.fields.description')" />
                    <x-form.input type="text" wire:model="description" />
                    <x-form.error for="description" />
                </div>

                <div class="md:col-span-4">
                    <x-form.label :value="__('assets.invoices.receive_stock.fields.model')" />
                    <x-form.input type="text" wire:model="model" />
                    <x-form.error for="model" />
                </div>

                <div class="md:col-span-12 flex justify-end gap-2">
                    <x-button type="button" wire:click="closeModal" :text="__('assets.actions.cancel')" variant="gray_outline" />
                    <x-button type="submit" :text="__('assets.invoices.receive_stock.submit')" icon="fa-solid fa-box-open" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
