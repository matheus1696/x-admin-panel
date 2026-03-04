<div>
    <x-alert.flash />

    <x-page.header
        :title="__('assets.invoices.index.title')"
        :subtitle="__('assets.invoices.index.subtitle')"
        icon="fa-solid fa-file-invoice-dollar"
        color="blue"
    >
        <x-slot name="button">
            @can('viewAny', \App\Models\Assets\Asset::class)
                <x-button
                    :href="route('assets.index')"
                    :text="__('assets.invoices.actions.assets')"
                    icon="fa-solid fa-boxes-stacked"
                    variant="gray_outline"
                />
            @endcan
            <x-button
                :href="route('assets.invoices.create')"
                :text="__('assets.invoices.actions.new')"
                icon="fa-solid fa-plus"
            />
        </x-slot>
    </x-page.header>

    <x-page.filter :title="__('assets.invoices.index.filters_title')" :accordion-open="true">
        <div class="md:col-span-8">
            <x-form.label :value="__('assets.invoices.fields.search')" />
            <x-form.input
                type="text"
                :placeholder="__('assets.invoices.placeholders.search')"
                wire:model.live.debounce.500ms="filters.search"
            />
        </div>

        <div class="md:col-span-4">
            <x-form.label :value="__('assets.invoices.fields.per_page')" />
            <x-form.select-livewire
                wire:model.live="filters.perPage"
                name="filters.perPage"
                :options="[
                    ['value' => 10, 'label' => '10'],
                    ['value' => 25, 'label' => '25'],
                    ['value' => 50, 'label' => '50'],
                ]"
                :default="__('assets.invoices.placeholders.per_page')"
            />
        </div>
    </x-page.filter>

    <x-page.table :pagination="$invoices" :empty-message="__('assets.invoices.index.empty')">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="__('assets.invoices.table.invoice')" />
                <x-page.table-th class="hidden md:table-cell" :value="__('assets.invoices.table.supplier')" />
                <x-page.table-th class="hidden lg:table-cell" :value="__('assets.invoices.table.issue_date')" />
                <x-page.table-th class="w-24 text-center" :value="__('assets.invoices.table.items')" />
                <x-page.table-th class="w-32 text-center" :value="__('assets.invoices.table.actions')" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($invoices as $invoice)
                <tr>
                    <x-page.table-td>
                        <div class="flex flex-col gap-1">
                            <span class="font-semibold text-gray-800">{{ $invoice->invoice_number }}</span>
                            <span class="text-xs text-gray-500">
                                {{ __('assets.invoices.labels.total') }}: {{ number_format((float) $invoice->total_amount, 2, ',', '.') }}
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
                                :title="__('assets.actions.view')"
                                variant="blue_text"
                            />
                            <x-button
                                :href="route('assets.invoices.edit', $invoice->uuid)"
                                icon="fa-solid fa-pen"
                                :title="__('assets.actions.edit')"
                                variant="green_text"
                            />
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
