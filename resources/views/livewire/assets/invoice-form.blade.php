<div>
    <x-alert.flash />

    <x-page.header
        :title="$invoiceId ? __('assets.invoices.form.edit_title') : __('assets.invoices.form.create_title')"
        :subtitle="$invoiceId ? __('assets.invoices.form.edit_subtitle') : __('assets.invoices.form.create_subtitle')"
        icon="fa-solid fa-receipt"
        color="blue"
    >
        <x-slot name="button">
            <x-button
                :href="$invoiceUuid ? route('assets.invoices.show', $invoiceUuid) : route('assets.invoices.index')"
                :text="__('assets.actions.cancel')"
                icon="fa-solid fa-arrow-left"
                variant="gray_outline"
            />
        </x-slot>
    </x-page.header>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 md:grid-cols-12">
            <div class="md:col-span-4">
                <x-form.label :value="__('assets.invoices.fields.invoice_number')" />
                <x-form.input type="text" wire:model="invoiceNumber" />
                <x-form.error for="invoiceNumber" />
            </div>

            <div class="md:col-span-2">
                <x-form.label :value="__('assets.invoices.fields.invoice_series')" />
                <x-form.input type="text" wire:model="invoiceSeries" />
                <x-form.error for="invoiceSeries" />
            </div>

            <div class="md:col-span-6">
                <x-form.label :value="__('assets.invoices.fields.supplier_name')" />
                <x-form.input type="text" wire:model="supplierName" />
                <x-form.error for="supplierName" />
            </div>

            <div class="md:col-span-4">
                <x-form.label :value="__('assets.invoices.fields.supplier_document')" />
                <x-form.input type="text" wire:model="supplierDocument" />
                <x-form.error for="supplierDocument" />
            </div>

            <div class="md:col-span-3">
                <x-form.label :value="__('assets.invoices.fields.issue_date')" />
                <x-form.input type="date" wire:model="issueDate" />
                <x-form.error for="issueDate" />
            </div>

            <div class="md:col-span-3">
                <x-form.label :value="__('assets.invoices.fields.received_date')" />
                <x-form.input type="date" wire:model="receivedDate" />
                <x-form.error for="receivedDate" />
            </div>

            <div class="md:col-span-2">
                <x-form.label :value="__('assets.invoices.fields.total_amount')" />
                <x-form.input type="number" step="0.01" min="0" wire:model="totalAmount" />
                <x-form.error for="totalAmount" />
            </div>

            <div class="md:col-span-12">
                <x-form.label :value="__('assets.invoices.fields.notes')" />
                <x-form.textarea wire:model="notes" rows="4" />
                <x-form.error for="notes" />
            </div>

            <div class="md:col-span-12 flex justify-end gap-2 pt-2">
                <x-button
                    :href="$invoiceUuid ? route('assets.invoices.show', $invoiceUuid) : route('assets.invoices.index')"
                    :text="__('assets.actions.cancel')"
                    variant="gray_outline"
                />
                <x-button
                    type="submit"
                    :text="$invoiceId ? __('assets.actions.edit') : __('assets.actions.save')"
                    icon="fa-solid fa-floppy-disk"
                />
            </div>
        </form>
    </div>
</div>
