<div>
    <x-alert.flash />

    <x-page.header
        :title="'Historico de Liberacoes'"
        :subtitle="'Acompanhe os pedidos de liberacao de ativos realizados'"
        icon="fa-solid fa-clipboard-list"
    >
        <x-slot name="button">
            <x-button :href="route('assets.release-orders.create')" :text="'Nova liberacao'" icon="fa-solid fa-plus" />
        </x-slot>
    </x-page.header>

    <x-page.table :empty-message="'Nenhuma liberacao registrada ainda.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="w-48" :value="'Pedido'" />
                <x-page.table-th class="w-36" :value="'Data'" />
                <x-page.table-th :value="'Destino'" />
                <x-page.table-th class="w-24 text-center" :value="'Itens'" />
                <x-page.table-th class="w-36" :value="'Responsavel'" />
                <x-page.table-th class="w-20 text-center" :value="'Acoes'" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($releaseOrders as $order)
                <tr>
                    <x-page.table-td :value="$order->code" />
                    <x-page.table-td :value="optional($order->released_at)->format('d/m/Y H:i') ?: '-'" />
                    <x-page.table-td>
                        <div class="flex flex-col">
                            <span>{{ $order->toUnit?->title ?: '-' }}</span>
                            <span class="text-xs text-gray-500">{{ $order->toSector?->title ?: 'Sem setor' }}</span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="text-center" :value="$order->total_assets" />
                    <x-page.table-td :value="$order->releasedBy?->name ?: '-'" />
                    <x-page.table-td>
                        <div class="flex items-center justify-center">
                            <x-button
                                :href="route('assets.release-orders.show', $order->uuid)"
                                icon="fa-solid fa-eye"
                                :title="'Ver folha de rosto'"
                                variant="blue_text"
                            />
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>

