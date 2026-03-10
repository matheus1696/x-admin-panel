<div class="space-y-4">
    <x-page.header title="Relatorios de Ponto" subtitle="Controle de Ponto" icon="fa-solid fa-chart-column" />

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
        <aside class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm xl:col-span-3">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Aside</p>
            <p class="mt-3 text-sm text-gray-600">Consulte os registros por periodo e acompanhe quem ainda nao registrou ponto hoje.</p>
        </aside>

        <main class="space-y-4 xl:col-span-9">
            <x-page.filter title="Filtros">
                <div class="md:col-span-3">
                    <x-form.label :value="'Data inicial'" />
                    <x-form.input type="date" wire:model.live="filters.dateFrom" />
                </div>
                <div class="md:col-span-3">
                    <x-form.label :value="'Data final'" />
                    <x-form.input type="date" wire:model.live="filters.dateTo" />
                </div>
                <div class="md:col-span-3">
                    <x-form.label value="Usuario" />
                    <x-form.select-livewire
                        wire:model.live="filters.userId"
                        name="filters.userId"
                        :options="$users->map(fn ($user) => ['value' => $user->id, 'label' => $user->name])->prepend(['value' => '', 'label' => 'Todos'])->values()->all()"
                    />
                </div>
                <div class="md:col-span-3">
                    <x-form.label value="Status" />
                    <x-form.select-livewire
                        wire:model.live="filters.status"
                        name="filters.status"
                        :options="collect($statuses)->map(fn ($status) => ['value' => $status->value, 'label' => $status->value])->prepend(['value' => 'all', 'label' => 'Todos'])->values()->all()"
                    />
                </div>
            </x-page.filter>

            <div class="flex justify-end">
                <x-button wire:click="export" text="Exportar" icon="fa-solid fa-file-export" variant="blue_solid" />
            </div>

            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Entradas por periodo</p>
                <div class="mt-4 space-y-2">
                    @forelse ($entriesByPeriod as $entry)
                        <div class="flex items-center justify-between rounded-xl border border-gray-100 px-3 py-2 text-sm">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $entry->user?->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $entry->occurred_at?->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <span class="text-xs font-semibold text-gray-600">{{ $entry->status }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Nenhum registro encontrado.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Usuarios sem registro hoje</p>
                <div class="mt-4 space-y-2">
                    @forelse ($usersWithoutEntryToday as $user)
                        <div class="rounded-xl border border-gray-100 px-3 py-2 text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                    @empty
                        <p class="text-sm text-gray-500">Todos os usuarios ativos registraram ponto hoje.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</div>
