<div>
    <x-alert.flash />

    <x-page.header
        :title="__('assets.invoices.show.title', ['number' => $invoice->invoice_number])"
        :subtitle="__('assets.invoices.show.subtitle')"
        icon="fa-solid fa-file-circle-check"
        color="blue"
    >
        <x-slot name="button">
            @can('viewAny', \App\Models\Assets\Asset::class)
                <x-button
                    :href="route('assets.index', ['invoice_uuid' => $invoice->uuid])"
                    :text="__('assets.invoices.actions.view_assets')"
                    icon="fa-solid fa-boxes-stacked"
                    variant="gray_outline"
                />
            @endcan
            <x-button
                :href="route('assets.invoices.edit', $invoice->uuid)"
                :text="__('assets.actions.edit')"
                icon="fa-solid fa-pen"
                variant="blue_outline"
            />
            <x-button
                :text="__('assets.invoices.items.actions.new')"
                icon="fa-solid fa-box-open"
                wire:click="createItem"
            />
        </x-slot>
    </x-page.header>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    {{ __('assets.invoices.show.summary_title') }}
                </h3>

                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ __('assets.invoices.fields.supplier_name') }}</dt>
                        <dd class="text-gray-700">{{ $invoice->supplier_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ __('assets.invoices.fields.supplier_document') }}</dt>
                        <dd class="text-gray-700">{{ $invoice->supplier_document ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ __('assets.invoices.fields.issue_date') }}</dt>
                        <dd class="text-gray-700">{{ optional($invoice->issue_date)->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ __('assets.invoices.fields.received_date') }}</dt>
                        <dd class="text-gray-700">{{ optional($invoice->received_date)->format('d/m/Y') ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ __('assets.invoices.fields.total_amount') }}</dt>
                        <dd class="text-gray-700">{{ number_format((float) $invoice->total_amount, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ __('assets.invoices.fields.notes') }}</dt>
                        <dd class="text-gray-700">{{ $invoice->notes ?: '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="lg:col-span-8">
            <x-page.table :empty-message="__('assets.invoices.items.empty')">
                <x-slot name="thead">
                    <tr>
                        <x-page.table-th :value="__('assets.invoices.items.table.description')" />
                        <x-page.table-th class="hidden md:table-cell" :value="__('assets.invoices.items.table.quantity')" />
                        <x-page.table-th class="hidden md:table-cell" :value="__('assets.invoices.items.table.total_price')" />
                        <x-page.table-th class="hidden lg:table-cell" :value="__('assets.invoices.items.table.assets_created')" />
                        <x-page.table-th class="w-28 text-center" :value="__('assets.invoices.table.actions')" />
                    </tr>
                </x-slot>

                <x-slot name="tbody">
                    @foreach ($invoice->items as $item)
                        <tr>
                            <x-page.table-td>
                                <div class="flex flex-col gap-1">
                                    <span class="font-medium text-gray-800">{{ $item->description }}</span>
                                    <span class="text-xs text-gray-500">
                                        {{ $item->brand ?: '-' }} / {{ $item->model ?: '-' }}
                                    </span>
                                    <div class="mt-1 overflow-hidden rounded-full bg-gray-200">
                                        @php
                                            $receivedPercent = $item->quantity > 0 ? min(100, (int) round(($item->assets_count / $item->quantity) * 100)) : 0;
                                            $remaining = max(0, (int) $item->quantity - (int) $item->assets_count);
                                        @endphp
                                        <div class="h-2 rounded-full bg-gradient-to-r from-emerald-600 to-emerald-700" style="width: {{ $receivedPercent }}%"></div>
                                    </div>
                                    <span class="text-[11px] font-medium text-gray-600">
                                        {{ __('assets.invoices.receive_stock.summary', ['received' => $item->assets_count, 'total' => $item->quantity, 'remaining' => $remaining]) }}
                                    </span>
                                </div>
                            </x-page.table-td>
                            <x-page.table-td class="hidden md:table-cell" :value="$item->quantity" />
                            <x-page.table-td class="hidden md:table-cell" :value="number_format((float) $item->total_price, 2, ',', '.')" />
                            <x-page.table-td class="hidden lg:table-cell">
                                <div class="flex flex-col gap-1 text-xs text-gray-600">
                                    <span>{{ $item->assets_count }}</span>
                                    <span>{{ __('assets.invoices.receive_stock.remaining_short', ['remaining' => max(0, (int) $item->quantity - (int) $item->assets_count)]) }}</span>
                                </div>
                            </x-page.table-td>
                            <x-page.table-td>
                                <div class="flex items-center justify-center gap-2">
                                    <x-button
                                        :href="route('assets.index', ['invoice_uuid' => $invoice->uuid, 'invoice_item_id' => $item->id])"
                                        icon="fa-solid fa-list"
                                        :title="__('assets.invoices.items.actions.view_assets')"
                                        variant="gray_text"
                                    />
                                    @can('receiveStock', \App\Models\Assets\Asset::class)
                                        <livewire:assets.receive-stock-form :asset-invoice-item-id="$item->id" :key="'receive-stock-'.$item->id" />
                                    @endcan
                                    <x-button
                                        wire:click="editItem({{ $item->id }})"
                                        icon="fa-solid fa-pen"
                                        :title="__('assets.actions.edit')"
                                        variant="blue_text"
                                    />
                                    <x-button
                                        wire:click="deleteItem({{ $item->id }})"
                                        icon="fa-solid fa-trash"
                                        :title="__('assets.invoices.items.actions.delete')"
                                        variant="red_text"
                                    />
                                </div>
                            </x-page.table-td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-page.table>
        </div>
    </div>

    <x-modal :show="$showModal" wire:key="asset-invoice-item-modal">
        @if ($modalKey === 'invoice-item-form')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    {{ $itemId ? __('assets.invoices.items.edit_title') : __('assets.invoices.items.create_title') }}
                </h2>
            </x-slot>

            <form wire:submit.prevent="saveItem" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-4">
                    <x-form.label :value="__('assets.invoices.items.fields.item_code')" />
                    <x-form.input type="text" wire:model="itemCode" />
                    <x-form.error for="itemCode" />
                </div>

                <div class="md:col-span-8">
                    <x-form.label :value="__('assets.invoices.items.fields.description')" />
                    <x-form.input type="text" wire:model="description" />
                    <x-form.error for="description" />
                </div>

                <div class="md:col-span-3">
                    <x-form.label :value="__('assets.invoices.items.fields.quantity')" />
                    <x-form.input type="number" min="1" wire:model="quantity" />
                    <x-form.error for="quantity" />
                </div>

                <div class="md:col-span-3">
                    <x-form.label :value="__('assets.invoices.items.fields.unit_price')" />
                    <x-form.input type="number" step="0.01" min="0" wire:model="unitPrice" />
                    <x-form.error for="unitPrice" />
                </div>

                <div class="md:col-span-3">
                    <x-form.label :value="__('assets.invoices.items.fields.total_price')" />
                    <x-form.input type="number" step="0.01" min="0" wire:model="totalPrice" />
                    <x-form.error for="totalPrice" />
                </div>

                <div class="md:col-span-3">
                    <x-form.label :value="__('assets.invoices.items.fields.brand')" />
                    <x-form.input type="text" wire:model="brand" />
                    <x-form.error for="brand" />
                </div>

                <div class="md:col-span-12">
                    <x-form.label :value="__('assets.invoices.items.fields.model')" />
                    <x-form.input type="text" wire:model="model" />
                    <x-form.error for="model" />
                </div>

                <div class="md:col-span-12 flex justify-end gap-2 pt-2">
                    <x-button
                        type="button"
                        wire:click="closeModal"
                        :text="__('assets.actions.cancel')"
                        variant="gray_outline"
                    />
                    <x-button
                        type="submit"
                        :text="__('assets.actions.save')"
                        icon="fa-solid fa-floppy-disk"
                    />
                </div>
            </form>
        @endif
    </x-modal>
</div>
