<div>

    <x-page.header
        :title="'Liberacao de Ativos'"
        :subtitle="'Monte um pedido com multiplos ativos em estoque e execute a liberacao em lote'"
        icon="fa-solid fa-right-from-bracket"
    >
        <x-slot name="button">
            <x-button
                type="button"
                wire:click="openAddItemModal"
                :text="'Adicionar item'"
                icon="fa-solid fa-plus"
            />
        </x-slot>
    </x-page.header>

    <x-page.card :title="'Dados do pedido'">
        <form wire:submit.prevent="createReleaseOrder" class="grid grid-cols-1 gap-4 md:grid-cols-12">
            <div class="md:col-span-4">
                <x-form.label :value="'Unidade de destino'" />
                <x-form.select-livewire
                    wire:model.live="unitId"
                    name="unitId"
                    :default="'Selecione a unidade'"
                    :options="collect($units)->map(fn ($unit) => ['value' => $unit->id, 'label' => $unit->title])->values()->all()"
                />
                <x-form.error for="unitId" />
            </div>

            <div class="md:col-span-4">
                <x-form.label :value="'Setor de destino'" />
                <div wire:key="release-order-sector-select-{{ $unitId ?: 'none' }}">
                    <x-form.select-livewire
                        wire:model.live="sectorId"
                        name="sectorId"
                        :default="$unitId ? 'Sem setor' : 'Selecione primeiro a unidade'"
                        :disabled="!$unitId"
                        :options="collect($sectors)->map(fn ($sector) => ['value' => $sector->id, 'label' => $sector->title])->values()->all()"
                    />
                </div>
                <x-form.error for="sectorId" />
            </div>

            <div class="md:col-span-4">
                <x-form.label :value="'Ativos selecionados'" />
                <x-form.input type="text" :value="count($selectedAssetIds)" disabled />
            </div>

            <div class="md:col-span-6">
                <x-form.label :value="'Solicitante'" />
                <x-form.input type="text" wire:model="requesterName" :placeholder="'Nome do solicitante'" />
                <x-form.error for="requesterName" />
            </div>

            <div class="md:col-span-6">
                <x-form.label :value="'Recebedor (opcional)'" />
                <x-form.input type="text" wire:model="receiverName" :placeholder="'Nome de quem vai receber'" />
                <x-form.error for="receiverName" />
            </div>

            <div class="md:col-span-12">
                <div class="mb-2 flex items-center justify-between">
                    <x-form.label :value="'Itens selecionados'" />
                    <x-button type="button" wire:click="openAddItemModal" :text="'Adicionar item'" icon="fa-solid fa-plus" variant="green_outline" />
                </div>

                @if ($selectedAssets->isEmpty())
                    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">
                        Nenhum item selecionado para liberacao.
                    </div>
                @else
                    <div class="overflow-hidden rounded-xl text-xs border border-gray-200 bg-white">
                        <div class="grid grid-cols-12 gap-3 border-b border-gray-200 bg-gray-50 px-4 py-2 text-xs font-semibold tracking-wide text-gray-600">
                            <div class="col-span-1">Patrimonio</div>
                            <div class="col-span-8">Item</div>
                            <div class="col-span-1">Nota</div>
                            <div class="col-span-1 text-center">Bloco</div>
                            <div class="col-span-1 text-center">Acoes</div>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach ($selectedAssets as $asset)
                                @php
                                    $itemLabel = $asset->invoiceItem?->description ?: $asset->description;
                                @endphp
                                <div class="grid grid-cols-12 items-center gap-3 px-4 py-3 text-xs">
                                    <div class="col-span-1 text-gray-700">{{ $asset->patrimony_number ?: '-' }}</div>
                                    <div class="col-span-8">{{ $itemLabel }}</div>
                                    <div class="col-span-1 text-gray-700">{{ $asset->invoiceItem?->invoice?->invoice_number ?: '-' }}</div>
                                    <div class="col-span-1 text-center text-gray-700">{{ $asset->invoiceItem?->invoice?->financialBlock?->acronym ?: '-' }}</div>
                                    <div class="col-span-1 flex justify-center">
                                        <x-button
                                            type="button"
                                            wire:click="removeSelectedAsset({{ $asset->id }})"
                                            icon="fa-solid fa-trash"
                                            :title="'Remover item'"
                                            variant="red_text"
                                        />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <x-form.error for="selectedAssetIds" />
            </div>

            <div class="md:col-span-12">
                <x-form.label :value="'Observacoes'" />
                <x-form.textarea wire:model="notes" :placeholder="'Informacoes adicionais para o pedido'" />
                <x-form.error for="notes" />
            </div>

            <div class="md:col-span-12 flex justify-end gap-2">
                <x-button :href="route('assets.stock.index')" :text="'Voltar ao estoque'" variant="gray_outline" />
                <x-button type="submit" :text="'Liberar e gerar folha de rosto'" icon="fa-solid fa-file-signature" />
            </div>
        </form>
    </x-page.card>

    <x-modal :show="$showModal" wire:key="release-order-add-item-modal">
        @if ($modalKey === 'add-item')
            <x-slot name="header">
                <h2 class="text-sm font-semibold uppercase text-gray-700">
                    {{ 'Adicionar item ao pedido de liberacao' }}
                </h2>
            </x-slot>

            <x-page.filter :title="'Filtros dos ativos em estoque'" :showClear="true" clearAction="clearFilters" :accordionOpen="true">
                <div class="md:col-span-8">
                    <x-form.label :value="'Busca'" />
                    <x-form.input
                        type="text"
                        :placeholder="'Codigo, descricao, patrimonio ou nota'"
                        wire:model.live.debounce.500ms="filters.search"
                    />
                </div>

                <div class="md:col-span-4">
                    <x-form.label :value="'Bloco'" />
                    <x-form.select-livewire
                        wire:model.live="filters.financialBlockId"
                        name="filters.financialBlockId"
                        :options="collect($financialBlocks)->map(fn ($block) => ['value' => $block->id, 'label' => ($block->acronym ?: $block->title)])->prepend(['value' => 'all', 'label' => 'Todos'])->values()->all()"
                    />
                </div>
            </x-page.filter>

            <x-page.table :pagination="$assets" :empty-message="'Nenhum ativo em estoque para selecao.'">
                <x-slot name="thead">
                    <tr>
                        <x-page.table-th :value="'Item'" />
                        <x-page.table-th class="w-40" :value="'Codigo'" />
                        <x-page.table-th class="w-40" :value="'Patrimonio'" />
                        <x-page.table-th class="w-32" :value="'Nota'" />
                        <x-page.table-th class="w-24 text-center" :value="'Bloco'" />
                        <x-page.table-th class="w-20 text-center" :value="'Acoes'" />
                    </tr>
                </x-slot>

                <x-slot name="tbody">
                    @foreach ($assets as $asset)
                        @php
                            $itemLabel = $asset->invoiceItem?->description ?: $asset->description;
                            $isSelected = in_array($asset->id, $selectedAssetIds, true);
                        @endphp
                        <tr>
                            <x-page.table-td :value="$itemLabel" />
                            <x-page.table-td :value="$asset->code" />
                            <x-page.table-td :value="$asset->patrimony_number ?: '-'" />
                            <x-page.table-td :value="$asset->invoiceItem?->invoice?->invoice_number ?: '-'" />
                            <x-page.table-td class="text-center" :value="$asset->invoiceItem?->invoice?->financialBlock?->acronym ?: '-'" />
                            <x-page.table-td>
                                <div class="flex items-center justify-center">
                                    @if ($isSelected)
                                        <span class="text-xs font-medium text-emerald-700">Adicionado</span>
                                    @else
                                        <x-button
                                            type="button"
                                            wire:click="addAssetToRelease({{ $asset->id }})"
                                            icon="fa-solid fa-plus"
                                            :title="'Adicionar ao pedido'"
                                            variant="green_text"
                                        />
                                    @endif
                                </div>
                            </x-page.table-td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-page.table>

            <div class="mt-3 flex justify-end">
                <x-button type="button" wire:click="closeModal" :text="'Fechar'" variant="gray_outline" />
            </div>
        @endif
    </x-modal>
</div>

