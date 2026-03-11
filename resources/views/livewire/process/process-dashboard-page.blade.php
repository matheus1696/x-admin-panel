<div class="space-y-6">
    @php
        $statusTotal = collect($dashboard['statuses'] ?? [])->sum('total');
        $statusOffset = 0;
        $statusSegments = [];

        foreach (($dashboard['statuses'] ?? []) as $item) {
            if ($statusTotal === 0 || ($item['total'] ?? 0) === 0) {
                continue;
            }

            $slice = round(($item['total'] / $statusTotal) * 100, 2);
            $end = min(100, $statusOffset + $slice);
            $statusSegments[] = "{$item['color']} {$statusOffset}% {$end}%";
            $statusOffset = $end;
        }

        $statusChartStyle = $statusSegments !== []
            ? 'background: conic-gradient('.implode(', ', $statusSegments).');'
            : 'background: #e5e7eb;';

        $deadlineRows = [
            ['label' => 'No prazo', 'total' => (int) ($dashboard['deadline_summary']['on_time'] ?? 0), 'color' => '#059669'],
            ['label' => 'Atrasados', 'total' => (int) ($dashboard['deadline_summary']['overdue'] ?? 0), 'color' => '#dc2626'],
            ['label' => 'Sem prazo', 'total' => (int) ($dashboard['deadline_summary']['without_deadline'] ?? 0), 'color' => '#64748b'],
        ];

        $deadlineTotal = collect($deadlineRows)->sum('total');
        $deadlineOffset = 0;
        $deadlineSegments = [];

        foreach ($deadlineRows as $item) {
            if ($deadlineTotal === 0 || $item['total'] === 0) {
                continue;
            }

            $slice = round(($item['total'] / $deadlineTotal) * 100, 2);
            $end = min(100, $deadlineOffset + $slice);
            $deadlineSegments[] = "{$item['color']} {$deadlineOffset}% {$end}%";
            $deadlineOffset = $end;
        }

        $deadlineChartStyle = $deadlineSegments !== []
            ? 'background: conic-gradient('.implode(', ', $deadlineSegments).');'
            : 'background: #e5e7eb;';

        $sectorMax = max(1, (int) (collect($dashboard['current_sectors'] ?? [])->max('total') ?? 0));
        $averageSectorMax = max(1, (float) (collect($dashboard['average_sector_times'] ?? [])->max('average_hours') ?? 0));
        $monthlyMax = max(1, (int) (collect($dashboard['openings_by_month'] ?? [])->max('total') ?? 0));
    @endphp

    <x-page.header title="Dashboard de Processos" subtitle="Visao consolidada do andamento, prazos e distribuicao dos processos" icon="fa-solid fa-chart-column">
        <x-slot name="button">
            <div class="flex items-center gap-2">
                @can('process.view')
                    <x-button href="{{ route('process.index') }}" text="Gestao de Processos" icon="fa-solid fa-list" variant="gray_outline" />
                @endcan
            </div>
        </x-slot>
    </x-page.header>

    <x-page.filter title="Filtros do Dashboard">
        <div class="col-span-12 md:col-span-4">
            <x-form.label value="Periodo" />
            <x-form.select-livewire
                wire:model.live="filters.window"
                name="filters.window"
                :options="[
                    ['value' => '30d', 'label' => 'Ultimos 30 dias'],
                    ['value' => '90d', 'label' => 'Ultimos 90 dias'],
                    ['value' => '180d', 'label' => 'Ultimos 180 dias'],
                    ['value' => '365d', 'label' => 'Ultimos 12 meses'],
                    ['value' => 'all', 'label' => 'Todo o historico'],
                ]"
            />
        </div>

        <div class="col-span-12 md:col-span-4">
            <x-form.label value="Setor atual" />
            <x-form.select-livewire
                wire:model.live="filters.organization_id"
                name="filters.organization_id"
                :options="$organizations->map(fn ($organization) => ['value' => $organization->id, 'label' => $organization->title])->prepend(['value' => 'all', 'label' => 'Todos'])->values()->all()"
            />
        </div>
    </x-page.filter>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-500">Total</p>
            <p class="mt-3 text-4xl font-bold text-gray-900">{{ $dashboard['total'] ?? 0 }}</p>
            <p class="mt-2 text-xs text-gray-500">Processos no recorte selecionado</p>
        </section>

        <section class="rounded-3xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-blue-700">Em andamento</p>
            <p class="mt-3 text-4xl font-bold text-blue-900">{{ $dashboard['in_progress_total'] ?? 0 }}</p>
            <p class="mt-2 text-xs text-blue-700/80">Fluxos ativos em execucao</p>
        </section>

        <section class="rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-emerald-700">No prazo</p>
            <p class="mt-3 text-4xl font-bold text-emerald-900">{{ $dashboard['deadline_summary']['on_time'] ?? 0 }}</p>
            <p class="mt-2 text-xs text-emerald-700/80">Etapas atuais sem estourar prazo</p>
        </section>

        <section class="rounded-3xl border border-rose-100 bg-gradient-to-br from-rose-50 to-white p-5 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-rose-700">Atrasados</p>
            <p class="mt-3 text-4xl font-bold text-rose-900">{{ $dashboard['deadline_summary']['overdue'] ?? 0 }}</p>
            <p class="mt-2 text-xs text-rose-700/80">Demandas que exigem prioridade</p>
        </section>

        <section class="rounded-3xl border border-amber-100 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-amber-700">Tempo medio</p>
            <p class="mt-3 text-4xl font-bold text-amber-900">{{ $dashboard['average_resolution_days'] ?? '-' }}</p>
            <p class="mt-2 text-xs text-amber-700/80">Dias por etapa concluida</p>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="overflow-hidden rounded-3xl border border-emerald-800 bg-white shadow-sm">
            <div class="border-b border-emerald-100 bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 px-6 py-5 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.25em]">Status dos Processos</p>
                <p class="mt-1 text-xs text-white/80">Distribuicao completa por etapa de vida</p>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-[220px_1fr]">
                <div class="flex items-center justify-center">
                    <div class="relative h-44 w-44 rounded-full" style="{{ $statusChartStyle }}">
                        <div class="absolute inset-6 rounded-full bg-white shadow-inner"></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <span class="text-3xl font-bold text-gray-900">{{ $dashboard['total'] ?? 0 }}</span>
                            <span class="text-[11px] font-semibold uppercase tracking-[0.25em] text-gray-500">Processos</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse (($dashboard['statuses'] ?? []) as $item)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between gap-3 text-xs">
                                <span class="truncate text-gray-600">{{ $item['label'] }}</span>
                                <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                @if (($item['total'] ?? 0) > 0)
                                    <div class="h-full rounded-full" style="width: {{ max(10, $statusTotal > 0 ? (int) round(($item['total'] / $statusTotal) * 100) : 0) }}%; background: {{ $item['color'] }}"></div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-xs text-gray-400">
                            Nenhum processo encontrado para o recorte selecionado.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-gradient-to-r from-slate-800 via-slate-900 to-slate-700 px-6 py-5 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.25em]">Cumprimento de Prazo</p>
                <p class="mt-1 text-xs text-white/80">Situacao das etapas atuais</p>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-[220px_1fr]">
                <div class="flex items-center justify-center">
                    <div class="relative h-44 w-44 rounded-full" style="{{ $deadlineChartStyle }}">
                        <div class="absolute inset-6 rounded-full bg-white shadow-inner"></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <span class="text-3xl font-bold text-gray-900">{{ collect($deadlineRows)->sum('total') }}</span>
                            <span class="text-[11px] font-semibold uppercase tracking-[0.25em] text-gray-500">Etapas</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach ($deadlineRows as $item)
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/70 px-4 py-3">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-xs font-medium text-gray-600">{{ $item['label'] }}</span>
                                <span class="text-sm font-bold text-gray-900">{{ $item['total'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-500">Setores</p>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Processos por setor atual</h3>
                </div>
                <span class="text-[11px] text-gray-400">Fila atual</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse (($dashboard['current_sectors'] ?? []) as $item)
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="truncate text-gray-600">{{ $item['label'] }}</span>
                            <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-slate-600 to-slate-800" style="width: {{ max(10, (int) round(($item['total'] / $sectorMax) * 100)) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-xs text-gray-400">
                        Nenhum setor com processos no recorte selecionado.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-500">Tempo medio</p>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Tempo medio de retorno por setor</h3>
                </div>
                <span class="text-[11px] text-gray-400">Etapas concluidas</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse (($dashboard['average_sector_times'] ?? []) as $item)
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="truncate text-gray-600">{{ $item['label'] }}</span>
                            <span class="font-semibold text-gray-900">{{ $item['formatted'] }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-orange-700" style="width: {{ max(10, (int) round(($item['average_hours'] / $averageSectorMax) * 100)) }}%"></div>
                        </div>
                        <p class="text-[11px] text-gray-400">{{ $item['total_steps'] }} etapa(s) concluida(s)</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-xs text-gray-400">
                        Ainda nao ha etapas concluidas suficientes para calcular medias.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-500">Tendencia</p>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">Abertura de processos por mes</h3>
            </div>
            <span class="text-[11px] text-gray-400">Volume historico</span>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4 xl:grid-cols-6">
            @forelse (($dashboard['openings_by_month'] ?? []) as $item)
                <div class="rounded-2xl border border-gray-100 bg-gray-50/70 px-4 py-4 text-center">
                    <div class="mx-auto flex h-28 w-10 items-end rounded-full bg-gray-100 p-1">
                        <div class="w-full rounded-full bg-gradient-to-t from-emerald-700 to-emerald-400" style="height: {{ max(12, (int) round(($item['total'] / $monthlyMax) * 100)) }}%"></div>
                    </div>
                    <p class="mt-3 text-xs font-semibold text-gray-900">{{ $item['total'] }}</p>
                    <p class="mt-1 text-[11px] uppercase tracking-[0.2em] text-gray-400">{{ $item['label'] }}</p>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-center text-xs text-gray-400">
                    Sem volume de abertura para o recorte selecionado.
                </div>
            @endforelse
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="rounded-3xl border border-rose-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-rose-600">Prioridade</p>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Processos atrasados</h3>
                </div>
                <span class="text-[11px] text-gray-400">Top 8</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse (($dashboard['overdue_processes'] ?? []) as $item)
                    <article class="rounded-2xl border border-rose-100 bg-rose-50/40 px-4 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-semibold text-gray-900">
                                    <span class="font-mono text-[11px] text-rose-700">{{ $item['code'] }}</span>
                                    <span class="mx-1 text-gray-300">-</span>
                                    <span>{{ $item['title'] }}</span>
                                </p>
                                <p class="mt-1 text-[11px] text-gray-500">Setor: {{ $item['sector'] }}</p>
                                <p class="mt-1 text-[11px] text-gray-500">Responsavel: {{ $item['owner'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-rose-700">{{ $item['delay_days'] }} dia(s)</p>
                                <p class="mt-1 text-[11px] text-gray-400">{{ $item['due_at']?->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        @can('process.view')
                            <div class="mt-3 flex justify-end">
                                <x-button href="{{ route('process.show', $item['uuid']) }}" text="Abrir" icon="fa-solid fa-arrow-up-right-from-square" variant="gray_text" class="!text-xs" />
                            </div>
                        @endcan
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50 px-4 py-4 text-center text-xs text-emerald-700">
                        Nenhum processo atrasado no recorte selecionado.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-emerald-600">Saude operacional</p>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Processos no prazo</h3>
                </div>
                <span class="text-[11px] text-gray-400">Top 8</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse (($dashboard['healthy_processes'] ?? []) as $item)
                    <article class="rounded-2xl border border-emerald-100 bg-emerald-50/30 px-4 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-semibold text-gray-900">
                                    <span class="font-mono text-[11px] text-emerald-700">{{ $item['code'] }}</span>
                                    <span class="mx-1 text-gray-300">-</span>
                                    <span>{{ $item['title'] }}</span>
                                </p>
                                <p class="mt-1 text-[11px] text-gray-500">Setor: {{ $item['sector'] }}</p>
                                <p class="mt-1 text-[11px] text-gray-500">Responsavel: {{ $item['owner'] }}</p>
                            </div>
                            <div class="text-right">
                                @if ($item['has_deadline'])
                                    <p class="text-xs font-semibold text-emerald-700">No prazo</p>
                                    <p class="mt-1 text-[11px] text-gray-400">{{ $item['due_at']?->format('d/m/Y') }}</p>
                                @else
                                    <p class="text-xs font-semibold text-slate-600">Sem prazo</p>
                                @endif
                            </div>
                        </div>
                        @can('process.view')
                            <div class="mt-3 flex justify-end">
                                <x-button href="{{ route('process.show', $item['uuid']) }}" text="Abrir" icon="fa-solid fa-arrow-up-right-from-square" variant="gray_text" class="!text-xs" />
                            </div>
                        @endcan
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-xs text-gray-400">
                        Nenhum processo ativo disponivel no momento.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
