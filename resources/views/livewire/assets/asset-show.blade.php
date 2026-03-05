<div>
    <x-alert.flash />

    <x-page.header
        :title="'Inventário do Patrimônio '.$asset->code"
        :subtitle="'Acompanhe dados cadastrais e historico operacional'"
        icon="fa-solid fa-box-archive"
        color="amber"
    >
        <x-slot name="button">
            <x-button
                :href="route('assets.index')"
                :text="'Voltar'"
                icon="fa-solid fa-arrow-left"
                variant="gray_outline"
            />
        </x-slot>
    </x-page.header>

    <div class="mb-4 grid grid-cols-1 gap-2 md:grid-cols-2 xl:grid-cols-4">
        @can('release', \App\Models\Assets\Asset::class)
            <livewire:assets.release-asset-form :asset-uuid="$asset->uuid" :key="'release-'.$asset->id" />
        @endcan

        @can('transfer', \App\Models\Assets\Asset::class)
            <livewire:assets.transfer-asset-form :asset-uuid="$asset->uuid" :key="'transfer-'.$asset->id" />
        @endcan

        @can('changeState', \App\Models\Assets\Asset::class)
            <livewire:assets.change-state-form :asset-uuid="$asset->uuid" :key="'state-'.$asset->id" />
        @endcan

        @can('returnToPatrimony', \App\Models\Assets\Asset::class)
            <livewire:assets.return-to-patrimony-form :asset-uuid="$asset->uuid" :key="'return-'.$asset->id" />
        @endcan
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
        <div class="xl:col-span-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    {{ 'Resumo do ativo' }}
                </h3>

                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Descricao' }}</dt>
                        <dd class="text-gray-700">{{ $asset->description }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Estado' }}</dt>
                        <dd class="text-gray-700">{{ [
                            'IN_STOCK' => 'Em estoque',
                            'IN_USE' => 'Em uso',
                            'MAINTENANCE' => 'Em manutencao',
                            'DAMAGED' => 'Inservivel',
                        ][strtoupper((string) $asset->state->value)] ?? (string) $asset->state->value }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Unidade' }}</dt>
                        <dd class="text-gray-700">{{ $asset->unit?->title ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Setor' }}</dt>
                        <dd class="text-gray-700">{{ $asset->sector?->title ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Numero de serie' }}</dt>
                        <dd class="text-gray-700">{{ $asset->serial_number ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Numero patrimonial' }}</dt>
                        <dd class="text-gray-700">{{ $asset->patrimony_number ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">{{ 'Nota fiscal' }}</dt>
                        <dd class="text-gray-700">{{ $asset->invoiceItem?->invoice?->invoice_number ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="xl:col-span-8">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                            {{ 'Linha do tempo' }}
                        </h3>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $totalEvents.' evento(s) registrados' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse ($events as $event)
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ [
                                            'STOCK_RECEIVED' => 'Entrada em estoque',
                                            'RELEASED' => 'Ativo liberado',
                                            'IN_USE' => 'Ativo em uso',
                                            'MAINTENANCE' => 'Ativo em manutencao',
                                            'DAMAGED' => 'Ativo inservivel',
                                            'TRANSFERRED' => 'Transferencia registrada',
                                            'AUDITED' => 'Auditoria executada',
                                            'STATE_CHANGED' => 'Estado alterado',
                                        ][$event->type->value] ?? $event->type->value }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $event->created_at?->format('d/m/Y H:i') }} · {{ $event->actor?->name ?? 'Sistema' }}
                                    </p>
                                </div>

                                <div class="text-xs text-gray-600">
                                    @if ($event->from_unit_id || $event->to_unit_id)
                                        <p>{{ 'Unidade: '.($event->fromUnit?->title ?? '-').' -> '.($event->toUnit?->title ?? '-') }}</p>
                                    @endif
                                    @if ($event->from_sector_id || $event->to_sector_id)
                                        <p>{{ 'Setor: '.($event->fromSector?->title ?? '-').' -> '.($event->toSector?->title ?? '-') }}</p>
                                    @endif
                                    @if ($event->from_state || $event->to_state)
                                        <p>{{ 'Estado: '.($event->from_state ?? '-').' -> '.($event->to_state ?? '-') }}</p>
                                    @endif
                                </div>
                            </div>

                            @if ($event->notes)
                                <p class="mt-2 text-xs leading-relaxed text-gray-600">{{ $event->notes }}</p>
                            @endif

                            @if (($event->payload['photo_path'] ?? null))
                                <a href="{{ asset('storage/'.$event->payload['photo_path']) }}" target="_blank" class="mt-2 inline-flex items-center gap-2 text-xs font-medium text-blue-700 hover:text-blue-800">
                                    <i class="fa-solid fa-image"></i>
                                    {{ 'Ver foto' }}
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">
                            {{ 'Nenhum evento registrado para este ativo.' }}
                        </div>
                    @endforelse
                </div>

                @if ($events->count() < $totalEvents)
                    <div class="mt-4 flex justify-center">
                        <x-button
                            wire:click="loadMoreEvents"
                            :text="'Carregar mais eventos'"
                            icon="fa-solid fa-clock-rotate-left"
                            variant="gray_outline"
                        />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
