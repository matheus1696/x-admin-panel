<div>
    <x-page.header
        :title="'Nota Fiscal '.$invoice->invoice_number"
        :subtitle="'Consulte os dados e itens da nota fiscal'"
        icon="fa-solid fa-file-circle-check"
    >
        <x-slot name="button">
            @if (! $invoice->is_finalized)
                <x-button
                    :text="'Novo item'"
                    icon="fa-solid fa-box-open"
                    wire:click="createItem"
                />
            @endif
        </x-slot>
    </x-page.header>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    {{ 'Resumo da nota' }}
                </h3>

                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Fornecedor' }}</dt>
                        <dd class="text-gray-700">{{ $invoice->supplier_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Bloco financeiro' }}</dt>
                        <dd class="text-gray-700">{{ $invoice->financialBlock?->acronym ? $invoice->financialBlock->acronym.' - '.$invoice->financialBlock->title : ($invoice->financialBlock?->title ?? '-') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Documento' }}</dt>
                        <dd class="text-gray-700">{{ $invoice->supplier_document ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Ordem de fornecimento' }}</dt>
                        <dd class="text-gray-700">{{ $invoice->supply_order ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Data de emissao' }}</dt>
                        <dd class="text-gray-700">{{ optional($invoice->issue_date)->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Data de recebimento' }}</dt>
                        <dd class="text-gray-700">{{ optional($invoice->received_date)->format('d/m/Y') ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Valor total' }}</dt>
                        <dd class="text-gray-700">{{ number_format((float) $invoice->total_amount, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Status' }}</dt>
                        <dd class="text-gray-700">{{ $invoice->is_finalized ? 'Finalizada' : 'Em cadastro' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Observacoes' }}</dt>
                        <dd class="text-gray-700">{{ $invoice->notes ?: '-' }}</dd>
                    </div>
                </dl>

                @if (! $invoice->is_finalized)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <x-button
                            :text="'Finalizar cadastro'"
                            icon="fa-solid fa-circle-check"
                            variant="green"
                            class="w-full"
                            wire:click="finalizeInvoice"
                        />
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-8">
            <x-page.table :empty-message="'Nenhum item cadastrado para esta nota.'">
                <x-slot name="thead">
                    <tr>
                        <x-page.table-th :value="'Item'" />
                        <x-page.table-th class="hidden md:table-cell" :value="'Qtd'" />
                        <x-page.table-th class="hidden md:table-cell" :value="'Valor total'" />
                        <x-page.table-th class="w-28 text-center" :value="'Acoes'" />
                    </tr>
                </x-slot>

                <x-slot name="tbody">
                    @foreach ($invoice->items as $item)
                        <tr>
                            <x-page.table-td>
                                <div class="flex flex-col gap-1">
                                    <span class="font-medium text-gray-800">{{ $item->product?->title ?? $item->description }}</span>
                                    <span class="text-xs text-gray-500">
                                        {{ $item->brand ?: '-' }} / {{ $item->model ?: '-' }}
                                        @if ($item->measureUnit?->acronym)
                                            {{ ' | '.$item->measureUnit->acronym }}
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ 'Codigo patrimonial: '.($item->item_code ?: '-') }}
                                    </span>
                                </div>
                            </x-page.table-td>
                            <x-page.table-td class="hidden md:table-cell" :value="$item->quantity" />
                            <x-page.table-td class="hidden md:table-cell" :value="number_format((float) $item->total_price, 2, ',', '.')" />
                            <x-page.table-td>
                                @if (! $invoice->is_finalized)
                                    <div class="flex items-center justify-center gap-2">
                                        <x-button
                                            wire:click="editItem({{ $item->id }})"
                                            icon="fa-solid fa-pen"
                                            :title="'Editar'"
                                            variant="blue_text"
                                        />
                                        <x-button
                                            wire:click="deleteItem({{ $item->id }})"
                                            icon="fa-solid fa-trash"
                                            :title="'Excluir item'"
                                            variant="red_text"
                                        />
                                    </div>
                                @else
                                    <div class="text-center text-xs text-gray-400">-</div>
                                @endif
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
                    {{ $itemId ? 'Editar item da nota' : 'Novo item da nota' }}
                </h2>
            </x-slot>

            <form wire:submit.prevent="saveItem" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-8">
                    <x-form.label :value="'Produto'" />
                    <x-form.select-livewire
                        wire:model.live="productId"
                        name="productId"
                        default="Selecione o produto"
                        :options="collect($products)->map(fn ($product) => ['value' => $product->id, 'label' => $product->title])->values()->all()"
                    />
                    <x-form.error for="productId" />
                </div>

                <div class="md:col-span-4">
                    <x-form.label :value="'Unidade de medida'" />
                    <x-form.select-livewire
                        wire:model.live="productMeasureUnitId"
                        name="productMeasureUnitId"
                        default="Selecione a unidade"
                        :options="collect($measureUnits)->map(fn ($unit) => ['value' => $unit->id, 'label' => $unit->acronym.' - '.$unit->title])->values()->all()"
                    />
                    <x-form.error for="productMeasureUnitId" />
                </div>

                <div class="md:col-span-6">
                    <x-form.label :value="'Marca'" />
                    <x-form.input type="text" wire:model="brand" placeholder="Ex: Dell, HP, Samsung" />
                    <x-form.error for="brand" />
                </div>

                <div class="md:col-span-6">
                    <x-form.label :value="'Modelo'" />
                    <x-form.input type="text" wire:model="model" placeholder="Ex: Inspiron 15, ProBook 440" />
                    <x-form.error for="model" />
                </div>

                <div class="md:col-span-6">
                    <x-form.label :value="'Codigo patrimonial'" />
                    <x-form.input type="text" wire:model="itemCode" placeholder="Opcional" />
                    <x-form.error for="itemCode" />
                </div>

                <div class="md:col-span-3">
                    <x-form.label :value="'Quantidade'" />
                    <x-form.input type="number" min="1" wire:model.live="quantity" placeholder="Ex: 10" />
                    <x-form.error for="quantity" />
                </div>

                <div class="md:col-span-3">
                    <x-form.label :value="'Valor unitario'" />
                    <x-form.input type="number" step="0.01" min="0.01" wire:model.live="unitPrice" placeholder="Ex: 1999.90" />
                    <x-form.error for="unitPrice" />
                </div>

                <div class="md:col-span-3">
                    <x-form.label :value="'Valor total'" />
                    <x-form.input type="number" step="0.01" min="0" wire:model="totalPrice" disabled />
                    <x-form.error for="totalPrice" />
                </div>

                <div class="md:col-span-12 flex justify-end gap-2 pt-2">
                    <x-button
                        type="button"
                        wire:click="closeModal"
                        :text="'Cancelar'"
                        variant="gray_outline"
                    />
                    <x-button
                        type="submit"
                        :text="'Salvar'"
                        icon="fa-solid fa-floppy-disk"
                    />
                </div>
            </form>
        @endif
    </x-modal>
</div>
