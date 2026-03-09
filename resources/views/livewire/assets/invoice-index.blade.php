<div>
    <x-alert.flash />

    <x-page.header :title="'Entrada de Ativos'" :subtitle="'Gerencie notas fiscais dentro do fluxo de estoque'" icon="fa-solid fa-file-invoice-dollar">
        <x-slot name="button">
            <div class="flex items-center gap-2">
                <x-button
                    wire:click="openCreateInvoice"
                    :text="'Informar Entrada'"
                    icon="fa-solid fa-plus"
                />
            </div>
        </x-slot>
    </x-page.header>

    <x-page.filter :title="'Filtros de notas'" :showClear="true" clearAction="clearFilters">
        <div class="md:col-span-2">
            <x-form.label :value="'Nota Fiscal'" />
            <x-form.input
                type="text"
                :placeholder="'Ex.: 256.565'"
                wire:model.live.debounce.500ms="filters.invoiceNumber"
                data-mask="invoiceNumber"
                maxlength="15"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="'Ordem'" />
            <x-form.input
                type="text"
                :placeholder="'0000-0000 ou 00000-0000'"
                wire:model.live.debounce.500ms="filters.supplyOrder"
                data-mask="supplyOrder"
                maxlength="10"
            />
        </div>

        <div class="md:col-span-3">
            <x-form.label :value="'Fornecedor'" />
            <x-form.select-livewire
                wire:model.live="filters.supplierId"
                name="filters.supplierId"
                :default="'Todos os fornecedores'"
                :options="collect($suppliers)->map(fn ($supplier) => ['value' => $supplier->id, 'label' => $supplier->title])->prepend(['value' => 'all', 'label' => 'Todos'])->values()->all()"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="'Bloco'" />
            <x-form.select-livewire
                wire:model.live="filters.financialBlockId"
                name="filters.financialBlockId"
                :default="'Todos os blocos'"
                :options="collect($financialBlocks)->map(fn ($block) => ['value' => $block->id, 'label' => ($block->acronym ?: $block->title)])->prepend(['value' => 'all', 'label' => 'Todos'])->values()->all()"
            />
        </div>

        <div class="md:col-span-2">
            <x-form.label :value="'Status'" />
            <x-form.select-livewire
                wire:model.live="filters.status"
                name="filters.status"
                :options="[
                    ['value' => 'all', 'label' => 'Todos'],
                    ['value' => 'open', 'label' => 'Em cadastro'],
                    ['value' => 'finalized', 'label' => 'Finalizada'],
                ]"
            />
        </div>

        <div class="md:col-span-1">
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
                <x-page.table-th class="w-24" :value="'Nota'" />
                <x-page.table-th class="w-24" :value="'Ordem'" />
                <x-page.table-th :value="'Fornecedor'" />
                <x-page.table-th class="w-20 text-center" :value="'Bloco'" />
                <x-page.table-th class="w-32 text-right"  :value="'Valor'" />
                <x-page.table-th class="w-28 text-center" :value="'Status'" />
                <x-page.table-th class="w-32 text-center" :value="'Acoes'" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($invoices as $invoice)
                <tr>
                    <x-page.table-td :value="$invoice->invoice_number" />
                    <x-page.table-td class="hidden md:table-cell" :value="$invoice->supply_order ?: '-'" />
                    <x-page.table-td class="hidden md:table-cell">
                        <div class="flex flex-col gap-1">
                            <span>{{ $invoice->supplier_name }}</span>
                            @if ($invoice->supplier_document)
                                <span class="text-xs text-gray-500">{{ $invoice->supplier_document }}</span>
                            @endif
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="text-center" :value="$invoice->financialBlock?->acronym ?: '-'" />
                    <x-page.table-td class="hidden lg:table-cell text-right" :value="number_format((float) $invoice->total_amount, 2, ',', '.')" />
                    <x-page.table-td class="text-center" :value="$invoice->is_finalized ? 'Finalizada' : 'Em cadastro'" />
                    <x-page.table-td>
                        <div class="flex items-center justify-start gap-1">
                            <x-button
                                wire:click="openViewInvoice('{{ $invoice->uuid }}')"
                                icon="fa-solid fa-eye"
                                :title="'Visualizar'"
                                variant="green_text"
                            />
                            @if (! $invoice->is_finalized)
                                <x-button
                                    wire:click="openEditInvoice('{{ $invoice->uuid }}')"
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

    <x-modal :show="$showModal" wire:key="invoice-index-create-modal">
        @if ($modalKey === 'invoice-show')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    {{ 'Visualizacao da nota fiscal' }}
                </h2>
            </x-slot>

            @if ($viewInvoice)
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-12">
                        <div class="rounded-lg bg-emerald-300/10 px-3 py-2 text-center">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ 'Nota Fiscal' }}</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $viewInvoice->invoice_number }}</p>
                        </div>
                        <div class="rounded-lg bg-emerald-300/10 px-3 py-2 text-center">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ 'Ordem' }}</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $viewInvoice->supply_order ?: '-' }}</p>
                        </div>
                        <div class="col-span-6 rounded-lg bg-emerald-300/10 px-3 py-2">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ 'Fornecedor' }}</p>
                            <p class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $viewInvoice->supplier_name }}</p>
                        </div>
                        <div class="rounded-lg bg-emerald-300/10 px-3 py-2 text-center">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ 'Bloco' }}</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $viewInvoice->financialBlock?->acronym ?: '-' }}</p>
                        </div>
                        <div class="col-span-2 rounded-lg bg-emerald-300/10 px-3 py-2">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ 'Valor' }}</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ 'R$ '.number_format((float) $viewInvoice->total_amount, 2, ',', '.') }}</p>
                        </div>
                        <div class="rounded-lg bg-emerald-300/10 px-1 py-2 text-center">
                            <p class="px-2 text-[9px] font-semibold uppercase tracking-wider text-slate-500">{{ 'Status' }}</p>
                            <span class="mt-1 inline-flex rounded-full px-2 py-1 text-[11px] font-semibold {{ $viewInvoice->is_finalized ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $viewInvoice->is_finalized ? 'Finalizada' : 'Cadastrando' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                        @if (! $viewInvoice->is_finalized)
                            <div class="md:col-span-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="mb-3 flex items-center justify-between">
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        {{ $itemId ? 'Editar item' : 'Adicionar item' }}
                                    </h3>
                                </div>

                                <form wire:submit.prevent="saveViewInvoiceItem" class="grid grid-cols-1 gap-3">
                                    <div>
                                        <x-form.label :value="'Produto'" />
                                        <x-form.select-livewire
                                            wire:model.live="itemProductId"
                                            name="itemProductId"
                                            :default="'Selecione o produto'"
                                            :options="collect($products)->map(fn ($product) => ['value' => $product->id, 'label' => $product->title])->values()->all()"
                                        />
                                        <x-form.error for="itemProductId" />
                                    </div>

                                    <div>
                                        <x-form.label :value="'Unidade de medida'" />
                                        <x-form.select-livewire
                                            wire:model.live="itemProductMeasureUnitId"
                                            name="itemProductMeasureUnitId"
                                            :default="'Selecione a unidade'"
                                            :options="collect($measureUnits)->map(fn ($unit) => ['value' => $unit->id, 'label' => $unit->acronym.' - '.$unit->title])->values()->all()"
                                        />
                                        <x-form.error for="itemProductMeasureUnitId" />
                                    </div>

                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <x-form.label :value="'Quantidade'" />
                                            <x-form.input type="number" min="1" wire:model="itemQuantity" />
                                            <x-form.error for="itemQuantity" />
                                        </div>
                                        <div>
                                            <x-form.label :value="'Valor unitario'" />
                                            <x-form.input type="number" min="0.01" step="0.01" wire:model="itemUnitPrice" />
                                            <x-form.error for="itemUnitPrice" />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div>
                                            <x-form.label :value="'Codigo patrimonial'" />
                                            <x-form.input type="text" wire:model="itemCode" :placeholder="'Opcional'" />
                                            <x-form.error for="itemCode" />
                                        </div>
                                        <div>
                                            <x-form.label :value="'Marca'" />
                                            <x-form.input type="text" wire:model="itemBrand" :placeholder="'Opcional'" />
                                            <x-form.error for="itemBrand" />
                                        </div>
                                        <div>
                                            <x-form.label :value="'Modelo'" />
                                            <x-form.input type="text" wire:model="itemModel" :placeholder="'Opcional'" />
                                            <x-form.error for="itemModel" />
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-2 pt-1">                                        
                                        @if ($itemId)
                                            <x-button type="button" wire:click="cancelEditViewInvoiceItem" :text="'Cancelar edicao'" icon="fa-solid fa-xmark" variant="red_outline" fullWidth="true"/>
                                        @endif
                                        <x-button type="submit" :text="$itemId ? 'Salvar item' : 'Adicionar item'" :icon="$itemId ? 'fa-solid fa-floppy-disk' :  'fa-solid fa-plus'" fullWidth="true"/>
                                    </div>
                                </form>
                            </div>
                        @endif

                        <div class="{{ $viewInvoice->is_finalized ? 'md:col-span-12' : 'md:col-span-7' }}">
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ 'Lista de itens' }}</h3>
                                @if (! $viewInvoice->is_finalized && $viewInvoice->items->count() > 0 && (float) $viewInvoice->total_amount > 0)
                                    <x-button
                                        wire:click="openFinalizeConfirm"
                                        :text="'Concluir cadastro'"
                                        icon="fa-solid fa-circle-check"
                                        variant="green"
                                    />
                                @endif
                            </div>

                            <x-page.table :empty-message="'Nenhum item cadastrado para esta nota.'">
                                <x-slot name="thead">
                                    <tr>
                                        <x-page.table-th :value="'Item'" />
                                        <x-page.table-th class="w-20 text-center" :value="'Qtd'" />
                                        <x-page.table-th class="w-24 text-center" :value="'Recebidos'" />
                                        <x-page.table-th class="w-24 text-right" :value="'Total'" />
                                        @if (! $viewInvoice->is_finalized)
                                            <x-page.table-th class="w-24 text-center" :value="'Acoes'" />
                                        @endif
                                    </tr>
                                </x-slot>

                                <x-slot name="tbody">
                                    @foreach ($viewInvoice->items as $item)
                                        <tr>
                                            <x-page.table-td>
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-gray-800">{{ $item->product?->title ?? $item->description }}</span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ ($item->brand ?: '-') }} / {{ ($item->model ?: '-') }}
                                                    </span>
                                                </div>
                                            </x-page.table-td>
                                            <x-page.table-td class="text-center" :value="$item->quantity" />
                                            <x-page.table-td class="text-center" :value="$item->assets_count" />
                                            <x-page.table-td class="text-right" :value="number_format((float) $item->total_price, 2, ',', '.')" />
                                            @if (! $viewInvoice->is_finalized)
                                                <x-page.table-td>
                                                    <div class="flex items-center justify-center gap-2">
                                                        <x-button
                                                            wire:click="editViewInvoiceItem({{ $item->id }})"
                                                            icon="fa-solid fa-pen"
                                                            :title="'Editar item'"
                                                            variant="green_text"
                                                        />
                                                        <x-button
                                                            wire:click="deleteViewInvoiceItem({{ $item->id }})"
                                                            icon="fa-solid fa-trash"
                                                            :title="'Excluir item'"
                                                            variant="red_text"
                                                        />
                                                    </div>
                                                </x-page.table-td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-page.table>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        @if ($modalKey === 'invoice-form')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    {{ $invoiceId ? 'Editar nota fiscal' : 'Nova nota fiscal' }}
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
                        :text="'Salvar nota fiscal'"
                        icon="fa-solid fa-floppy-disk"
                    />
                </div>
            </form>
        @endif
    </x-modal>

    <x-modal :show="$showFinalizeConfirm" :closeable="false" wire:key="invoice-index-finalize-confirm-modal">
        <x-slot name="header">
            <h2 class="text-sm font-semibold text-gray-700 uppercase">
                {{ 'Confirmar finalizacao' }}
            </h2>
        </x-slot>

        <div class="space-y-3">
            <p class="text-sm text-gray-700">
                {{ 'Ao concluir o cadastro, a nota fiscal sera bloqueada para edicao de itens e dados principais.' }}
            </p>
            <p class="text-xs text-amber-700">
                {{ 'Deseja realmente finalizar este cadastro?' }}
            </p>
        </div>

        <x-slot name="footer">
            <x-button
                type="button"
                wire:click="cancelFinalizeConfirm"
                :text="'Cancelar'"
                variant="gray_outline"
            />
            <x-button
                type="button"
                wire:click="confirmFinalizeViewInvoice"
                :text="'Confirmar finalizacao'"
                icon="fa-solid fa-circle-check"
                variant="green"
            />
        </x-slot>
    </x-modal>
</div>
