<div class="space-y-4">
    <x-page.header title="Visualizar Registro" subtitle="{{ $entry->user?->name ?? '-' }}" icon="fa-solid fa-clock" />

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
        <aside class="space-y-4 xl:col-span-4">
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Aside</p>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500">Usuario</p>
                        <p class="font-semibold text-gray-900">{{ $entry->user?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="font-semibold text-gray-900">{{ $entry->status }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Local</p>
                        <p class="font-semibold text-gray-900">{{ $entry->location?->name ?? '-' }}</p>
                    </div>
                </div>
            </section>
        </aside>

        <main class="space-y-4 xl:col-span-8">
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Foto</p>
                @if ($entry->photo_path)
                    <img src="{{ asset('storage/'.$entry->photo_path) }}" alt="Foto do registro" class="mt-4 h-80 w-full rounded-2xl object-cover" />
                @else
                    <div class="mt-4 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-sm text-gray-500">Sem foto registrada.</div>
                @endif
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Metadados</p>
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 text-sm">
                    <div><p class="text-xs text-gray-500">Data/Hora</p><p class="font-semibold text-gray-900">{{ $entry->occurred_at?->format('d/m/Y H:i:s') }}</p></div>
                    <div><p class="text-xs text-gray-500">Precisao</p><p class="font-semibold text-gray-900">{{ $entry->accuracy ?? '-' }}</p></div>
                    <div><p class="text-xs text-gray-500">IP</p><p class="font-semibold text-gray-900 break-all">{{ data_get($entry->device_meta, 'ip', '-') }}</p></div>
                    <div><p class="text-xs text-gray-500">User Agent</p><p class="font-semibold text-gray-900 break-all">{{ data_get($entry->device_meta, 'user_agent', '-') }}</p></div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Mapa</p>
                <div class="mt-4 text-sm text-gray-700">
                    @if ($entry->latitude !== null && $entry->longitude !== null)
                        <a href="https://www.google.com/maps?q={{ $entry->latitude }},{{ $entry->longitude }}" target="_blank" class="text-emerald-700 hover:underline">
                            Abrir localizacao no mapa
                        </a>
                    @else
                        <p class="text-gray-500">Sem coordenadas registradas.</p>
                    @endif
                </div>
            </section>
        </main>
    </div>
</div>
