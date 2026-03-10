<div class="space-y-4">
    <x-page.header title="Registros de Ponto" subtitle="Controle de Ponto" icon="fa-solid fa-table-list" />

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
        <aside class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm xl:col-span-3">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Aside</p>
            <p class="mt-3 text-sm text-gray-600">Acompanhe os registros da equipe com filtros por periodo, usuario e status.</p>
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

            <x-page.table :pagination="$entries">
                <x-slot name="thead">
                    <tr>
                        <x-page.table-th value="Usuario" />
                        <x-page.table-th :value="'Data/Hora'" />
                        <x-page.table-th value="Status" />
                        <x-page.table-th value="Local" />
                        <x-page.table-th class="w-24 text-center" :value="'Acoes'" />
                    </tr>
                </x-slot>
                <x-slot name="tbody">
                    @forelse ($entries as $entry)
                        <tr>
                            <x-page.table-td :value="$entry->user?->name" />
                            <x-page.table-td :value="$entry->occurred_at?->format('d/m/Y H:i:s')" />
                            <x-page.table-td :value="$entry->status" />
                            <x-page.table-td :value="$entry->location?->name ?? '-'" />
                            <x-page.table-td class="text-center">
                                <x-button :href="route('time-clock.entries.show', $entry->uuid)" text="Visualizar" variant="green_text" icon="fa-solid fa-eye" />
                            </x-page.table-td>
                        </tr>
                    @empty
                        <tr>
                            <x-page.table-td colspan="5" class="py-8 text-center text-sm text-gray-500">Nenhum registro encontrado.</x-page.table-td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-page.table>
        </main>
    </div>
</div>
