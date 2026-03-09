<div>
    <x-alert.flash />

    <x-page.header
        :title="'Estoque de Ativos'"
        :subtitle="'Itens disponiveis para liberacao por unidade'"
        icon="fa-solid fa-boxes-stacked"
    >
        <x-slot name="button">
            @can('transfer', \App\Models\Assets\Asset::class)
                <x-button
                    :href="route('assets.release-orders.index')"
                    :text="'Liberacao de ativos'"
                    icon="fa-solid fa-file-signature"
                />
            @endcan
        </x-slot>
    </x-page.header>

    <x-page.filter :title="'Filtros de estoque'" :showClear="true" clearAction="clearFilters">
        <div class="md:col-span-6">
            <x-form.label :value="'Busca'" />
            <x-form.input
                type="text"
                :placeholder="'Busque pela descricao do item'"
                wire:model.live.debounce.500ms="filters.search"
            />
        </div>

        <div class="md:col-span-3">
            <x-form.label :value="'Bloco'" />
            <x-form.select-livewire
                wire:model.live="filters.financialBlockId"
                name="filters.financialBlockId"
                :options="collect($financialBlocks)->map(fn ($block) => ['value' => $block->id, 'label' => ($block->acronym ?: $block->title)])->prepend(['value' => 'all', 'label' => 'Todos'])->values()->all()"
            />
        </div>

        <div class="md:col-span-3">
            <x-form.label :value="'Itens por pagina'" />
            <x-form.select-livewire
                wire:model.live="filters.perPage"
                name="filters.perPage"
                :options="[
                    ['value' => 10, 'label' => '10'],
                    ['value' => 25, 'label' => '25'],
                    ['value' => 50, 'label' => '50'],
                ]"
            />
        </div>
    </x-page.filter>

    <x-page.table :pagination="$groupedStockItems" :empty-message="'Nenhum item disponivel em estoque.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="'Item'" />
                <x-page.table-th class="w-40 text-center" :value="'Bloco'" />
                <x-page.table-th class="w-40 text-center" :value="'Estoque'" />
                <x-page.table-th class="w-24 text-center" :value="'Acoes'" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($groupedStockItems as $groupedItem)
                <tr>
                    <x-page.table-td :value="$groupedItem->item_description" />
                    <x-page.table-td
                        class="text-center"
                        :value="$groupedItem->financial_blocks_count > 1 ? 'Multiplos' : ($groupedItem->financial_block_label ?: '-')"
                    />
                    <x-page.table-td class="text-center" :value="$groupedItem->stock_quantity" />
                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-1">
                            <x-button
                                wire:click="openStockItem('{{ base64_encode($groupedItem->item_description) }}')"
                                icon="fa-solid fa-eye"
                                :title="'Ver itens do grupo'"
                                variant="blue_text"
                            />
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>

    <x-modal :show="$showModal" wire:key="assets-stock-release-modal">
        @if ($modalKey === 'release-stock')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    {{ 'Liberar ativo do estoque' }}
                </h2>
            </x-slot>

            <form wire:submit.prevent="release" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-6">
                    <x-form.label :value="'Unidade de destino'" />
                    <x-form.select-livewire
                        wire:model.live="unitId"
                        name="unitId"
                        :default="'Selecione a unidade'"
                        :options="collect($units)->map(fn ($unit) => ['value' => $unit->id, 'label' => $unit->title])->values()->all()"
                    />
                    <x-form.error for="unitId" />
                </div>

                <div class="md:col-span-6">
                    <x-form.label :value="'Setor de destino'" />
                    <x-form.select-livewire
                        wire:model.live="sectorId"
                        name="sectorId"
                        :default="'Sem setor'"
                        :options="collect($sectors)->map(fn ($sector) => ['value' => $sector->id, 'label' => $sector->title])->values()->all()"
                    />
                    <x-form.error for="sectorId" />
                </div>

                <div class="md:col-span-12">
                    <x-form.label :value="'Observacoes'" />
                    <x-form.textarea wire:model="notes" rows="3" :placeholder="'Opcional'" />
                    <x-form.error for="notes" />
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
                        :text="'Liberar'"
                        icon="fa-solid fa-right-from-bracket"
                    />
                </div>
            </form>
        @endif

        @if ($modalKey === 'stock-item-list')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    {{ 'Notas fiscais com item em estoque: '.$selectedStockItem }}
                </h2>
            </x-slot>

            <x-page.table :empty-message="'Nenhuma nota fiscal com saldo e item em estoque para este item.'">
                <x-slot name="thead">
                    <tr>
                        <x-page.table-th :value="'Nota fiscal'" />
                        <x-page.table-th class="hidden md:table-cell" :value="'Ordem'" />
                        <x-page.table-th class="hidden md:table-cell" :value="'Fornecedor'" />
                        <x-page.table-th class="hidden md:table-cell" :value="'Bloco'" />
                        <x-page.table-th class="hidden md:table-cell text-right" :value="'Valor'" />
                        <x-page.table-th class="text-center" :value="'Qtd em estoque'" />
                        <x-page.table-th class="w-24 text-center" :value="'Acoes'" />
                    </tr>
                </x-slot>

                <x-slot name="tbody">
                    @foreach ($selectedItemInvoices as $invoice)
                        <tr>
                            <x-page.table-td :value="$invoice->invoice_number" />
                            <x-page.table-td class="hidden md:table-cell" :value="$invoice->supply_order ?: '-'" />
                            <x-page.table-td class="hidden md:table-cell" :value="$invoice->supplier_name" />
                            <x-page.table-td class="hidden md:table-cell" :value="$invoice->financial_block_label ?: '-'" />
                            <x-page.table-td class="hidden md:table-cell text-right" :value="number_format((float) $invoice->total_amount, 2, ',', '.')" />
                            <x-page.table-td class="text-center" :value="$invoice->stock_quantity" />
                            <x-page.table-td>
                                <div class="flex items-center justify-center gap-1">
                                    <x-button
                                        wire:click="openStockItemInvoiceAssets({{ $invoice->invoice_id }})"
                                        icon="fa-solid fa-eye"
                                        :title="'Ver ativos da nota'"
                                        variant="blue_text"
                                    />
                                </div>
                            </x-page.table-td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-page.table>
        @endif

        @if ($modalKey === 'stock-item-invoice-assets')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    {{ 'Ativos em estoque por nota: '.$selectedStockItem }}
                </h2>
            </x-slot>

            <x-page.table :empty-message="'Nenhum ativo em estoque encontrado para esta nota.'">
                <x-slot name="thead">
                    <tr>
                        <x-page.table-th :value="'Codigo'" />
                        <x-page.table-th class="hidden md:table-cell" :value="'Patrimonio'" />
                        <x-page.table-th class="hidden md:table-cell" :value="'Nota Fiscal'" />
                        <x-page.table-th class="hidden md:table-cell" :value="'Bloco'" />
                        <x-page.table-th class="hidden md:table-cell" :value="'Unidade atual'" />
                        <x-page.table-th class="w-24 text-center" :value="'Acoes'" />
                    </tr>
                </x-slot>

                <x-slot name="tbody">
                    @foreach ($selectedItemAssets as $asset)
                        <tr>
                            <x-page.table-td :value="$asset->code" />
                            <x-page.table-td class="hidden md:table-cell" :value="$asset->patrimony_number ?: '-'" />
                            <x-page.table-td class="hidden md:table-cell" :value="$asset->invoiceItem?->invoice?->invoice_number ?? '-'" />
                            <x-page.table-td
                                class="hidden md:table-cell"
                                :value="$asset->invoiceItem?->invoice?->financialBlock?->acronym ?: ($asset->invoiceItem?->invoice?->financialBlock?->title ?? '-')"
                            />
                            <x-page.table-td class="hidden md:table-cell" :value="$asset->unit?->title ?? 'Sem unidade'" />
                            <x-page.table-td>
                                <div class="flex items-center justify-center gap-1">
                                    <x-button
                                        :href="route('assets.show', $asset->uuid)"
                                        icon="fa-solid fa-eye"
                                        :title="'Visualizar ativo'"
                                        variant="blue_text"
                                    />

                                    @can('transfer', \App\Models\Assets\Asset::class)
                                        <x-button
                                            wire:click="openRelease({{ $asset->id }})"
                                            icon="fa-solid fa-right-from-bracket"
                                            :title="'Liberar do estoque'"
                                            variant="green_text"
                                        />
                                    @endcan
                                </div>
                            </x-page.table-td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-page.table>
        @endif

        @if ($modalKey === 'invoice-form')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    {{ 'Cadastro de nota fiscal (estoque)' }}
                </h2>
            </x-slot>

            <form wire:submit.prevent="saveInvoice" class="grid grid-cols-1 gap-4 md:grid-cols-12">
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
                        wire:model.live="financialBlockId"
                        name="financialBlockId"
                        :default="'Selecione o bloco'"
                        :options="collect($financialBlocks)->map(fn ($block) => ['value' => $block->id, 'label' => (($block->acronym ? $block->acronym.' - ' : '').$block->title)])->values()->all()"
                    />
                    <x-form.error for="financialBlockId" />
                </div>

                <div class="md:col-span-6">
                    <x-form.label :value="'Fornecedor'" />
                    <x-form.select-livewire
                        wire:model.live="supplierId"
                        name="supplierId"
                        :default="'Selecione o fornecedor'"
                        :options="collect($suppliers)->map(fn ($supplier) => ['value' => $supplier->id, 'label' => (($supplier->document ? $supplier->document.' - ' : '').$supplier->title)])->values()->all()"
                    />
                    <x-form.error for="supplierId" />
                </div>

                <div class="md:col-span-3">
                    <x-form.label :value="'Data de emissao'" />
                    <x-form.input type="date" wire:model="issueDate" max="{{ now()->toDateString() }}" />
                    <x-form.error for="issueDate" />
                </div>

                <div class="md:col-span-3">
                    <x-form.label :value="'Data de recebimento'" />
                    <x-form.input type="date" wire:model="receivedDate" max="{{ now()->toDateString() }}" />
                    <x-form.error for="receivedDate" />
                </div>

                <div class="md:col-span-12">
                    <x-form.label :value="'Observacoes'" />
                    <x-form.textarea wire:model="invoiceNotes" rows="3" :placeholder="'Opcional'" />
                    <x-form.error for="invoiceNotes" />
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
                        :text="'Salvar nota fiscal'"
                        icon="fa-solid fa-floppy-disk"
                    />
                </div>
            </form>
        @endif
    </x-modal>
</div>
