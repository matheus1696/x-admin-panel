<div>

    <x-page.header :title="'Folha de rosto da liberacao'" :subtitle="'Documento para conferencia e assinatura do recebimento dos ativos'" icon="fa-solid fa-file-signature">
        <x-slot name="button">
            <div class="flex items-center gap-2">
                <x-button :href="route('assets.release-orders.index')" :text="'Historico'" icon="fa-solid fa-list" variant="gray_outline" />
                <x-button :href="route('assets.release-orders.create')" :text="'Novo pedido'" icon="fa-solid fa-plus" variant="gray_outline" />
                <button
                    type="button"
                    onclick="window.open('{{ route('assets.release-orders.pdf', $order->uuid) }}', '_blank')"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-800"
                >
                    <i class="fa-solid fa-print"></i>
                    PDF
                </button>
            </div>
        </x-slot>
    </x-page.header>

    <div class="space-y-4">
        <x-page.card>
            <div class="rounded-xl border border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-white p-4">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.18em] text-emerald-700">Pedido de liberacao</p>
                        <p class="mt-1 text-xl font-bold text-gray-900">{{ $order->code }}</p>
                        <p class="mt-1 text-xs text-gray-600">
                            Emitido em {{ optional($order->released_at)->format('d/m/Y \a\s H:i') ?: '-' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs md:w-[340px]">
                        <div class="rounded-lg border border-emerald-200 bg-white px-3 py-2">
                            <p class="text-[10px] uppercase tracking-wide text-gray-500">Status</p>
                            <p class="font-semibold text-emerald-700">Liberado</p>
                        </div>
                        <div class="rounded-lg border border-emerald-200 bg-white px-3 py-2">
                            <p class="text-[10px] uppercase tracking-wide text-gray-500">Total de ativos</p>
                            <p class="font-semibold text-gray-900">{{ $order->total_assets }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 text-sm md:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-3">
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">Destino</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $order->toUnit?->title ?: '-' }}</p>
                    <p class="text-xs text-gray-600">{{ $order->toSector?->title ?: 'Sem setor' }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-3">
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">Responsavel pela liberacao</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $order->releasedBy?->name ?: '-' }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-3">
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">Solicitante</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $order->requester_name }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-3 md:col-span-3">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-500">Recebedor</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ $order->receiver_name ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-500">Data/Hora da liberacao</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ optional($order->released_at)->format('d/m/Y H:i') ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($order->notes)
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-amber-700">Observacoes</p>
                    <p class="mt-1 text-sm text-amber-900">{{ $order->notes }}</p>
                </div>
            @endif
        </x-page.card>

        <div>
            <div class="mb-1 flex items-center justify-between pl-2">
                <p class="text-sm font-semibold uppercase tracking-wide text-gray-700">Itens liberados</p>
                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                    {{ $order->items->count() }} itens
                </span>
            </div>

            <x-page.table :empty-message="'Nenhum item registrado neste pedido.'">
                <x-slot name="thead">
                    <tr>
                        <x-page.table-th :value="'Item'" />
                        <x-page.table-th class="w-40" :value="'Codigo'" />
                        <x-page.table-th class="w-40" :value="'Patrimonio'" />
                        <x-page.table-th class="w-32" :value="'Nota'" />
                        <x-page.table-th class="w-24 text-center" :value="'Bloco'" />
                    </tr>
                </x-slot>

                <x-slot name="tbody">
                    @foreach ($order->items as $item)
                        <tr>
                            <x-page.table-td>
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-800">{{ $item->item_description }}</span>
                                    <span class="text-xs text-gray-500">Ativo: {{ $item->asset_code }}</span>
                                </div>
                            </x-page.table-td>
                            <x-page.table-td :value="$item->asset_code" />
                            <x-page.table-td :value="$item->patrimony_number ?: '-'" />
                            <x-page.table-td :value="$item->invoice_number ?: '-'" />
                            <x-page.table-td class="text-center" :value="$item->financial_block_label ?: '-'" />
                        </tr>
                    @endforeach
                </x-slot>
            </x-page.table>
        </div>

        <x-page.card>
            <div class="mb-3 flex items-center justify-between">
                <p class="text-sm font-semibold uppercase tracking-wide text-gray-700">Assinaturas</p>
                <span class="text-xs text-gray-500">Conferencia no ato do recebimento</span>
            </div>
            <div class="grid grid-cols-1 gap-8 pt-3 md:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-4">
                    <div class="h-16 border-b border-gray-400"></div>
                    <p class="mt-2 text-xs uppercase tracking-wide text-gray-600">Assinatura de quem entrega</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-4">
                    <div class="h-16 border-b border-gray-400"></div>
                    <p class="mt-2 text-xs uppercase tracking-wide text-gray-600">Assinatura de quem recebe</p>
                </div>
            </div>
        </x-page.card>
    </div>
</div>
