<div>
    <x-alert.flash />

    <div wire:poll.30s="silentRefresh" x-data="{
        tab: 'dashboard',
        expandedTaskId: null,
        stepFormTaskId: null,
        draggedStepId: null,
        draggedFromStatusId: null,
        dragOverStatusId: null,
        dragOverInsertBeforeId: null,
        buildStepTargetOrder(columnIds, insertBeforeId = null) {
            if (this.draggedStepId === null) {
                return columnIds;
            }

            const base = columnIds.filter((id) => id !== this.draggedStepId);
            const result = [];
            let inserted = false;

            for (const id of base) {
                if (insertBeforeId !== null && id === insertBeforeId && ! inserted) {
                    result.push(this.draggedStepId);
                    inserted = true;
                }

                result.push(id);
            }

            if (! inserted) {
                result.push(this.draggedStepId);
            }

            return result;
        },
        dropStepOnColumn(statusId, columnIds, insertBeforeId = null) {
            if (this.draggedStepId === null || this.draggedFromStatusId === null) {
                return;
            }

            const targetOrder = this.buildStepTargetOrder(columnIds, insertBeforeId);
            $wire.requestStepKanbanDrop(this.draggedStepId, this.draggedFromStatusId, statusId, targetOrder);

            this.draggedStepId = null;
            this.draggedFromStatusId = null;
            this.dragOverStatusId = null;
            this.dragOverInsertBeforeId = null;
        }
    }"
    x-on:step-form-closed.window="stepFormTaskId = null">

        <!-- Botões Superiores -->
        <div class="flex justify-center md:justify-start mb-4 overflow-x-auto rounded-2xl border border-gray-200 bg-white p-1">
            <div class="flex min-w-max gap-1">
                <button type="button" class="relative flex min-w-[104px] items-center justify-center rounded-xl px-3 py-2.5 text-[11px] font-medium whitespace-nowrap transition-all duration-200 sm:min-w-0 sm:px-6 sm:text-xs" :class="tab === 'dashboard' ? 'bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" @click="tab = 'dashboard'">
                    <span class="flex flex-col items-center gap-1 sm:flex-row sm:gap-2">
                        <i class="fa-regular fa-chart-bar" :class="tab === 'dashboard' ? 'text-white' : 'text-gray-400'"></i>
                        <span>Dashboard</span>
                    </span>
                </button>

                <button type="button" class="relative flex min-w-[88px] items-center justify-center rounded-xl px-3 py-2.5 text-[11px] font-medium whitespace-nowrap transition-all duration-200 sm:min-w-0 sm:px-6 sm:text-xs" :class="tab === 'list' ? 'bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" @click="tab = 'list'">
                    <span class="flex flex-col items-center gap-1 sm:flex-row sm:gap-2">
                        <i class="fa-regular fa-list-alt" :class="tab === 'list' ? 'text-white' : 'text-gray-400'"></i>
                        <span>Lista</span>
                    </span>
                </button>

                <button type="button" class="relative flex min-w-[126px] items-center justify-center rounded-xl px-3 py-2.5 text-[11px] font-medium whitespace-nowrap transition-all duration-200 sm:min-w-0 sm:px-6 sm:text-xs" :class="tab === 'step-kanban' ? 'bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" @click="tab = 'step-kanban'">
                    <span class="flex flex-col items-center gap-1 sm:flex-row sm:gap-2">
                        <i class="fa-solid fa-grip" :class="tab === 'step-kanban' ? 'text-white' : 'text-gray-400'"></i>
                        <span>Kanban Etapas</span>
                    </span>
                </button>

                @if ($canManageSettings)
                    <button type="button" class="relative flex min-w-[112px] items-center justify-center rounded-xl px-3 py-2.5 text-[11px] font-medium whitespace-nowrap transition-all duration-200 sm:min-w-0 sm:px-6 sm:text-xs" :class="tab === 'settings' ? 'bg-gradient-to-r from-sky-700 via-cyan-700 to-sky-900 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" @click="tab = 'settings'">
                        <span class="flex flex-col items-center gap-1 sm:flex-row sm:gap-2">
                            <i class="fa-solid fa-sliders" :class="tab === 'settings' ? 'text-white' : 'text-gray-400'"></i>
                            <span>Configurações</span>
                        </span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Dashboard -->
        <div x-show="tab === 'dashboard'" x-cloak class="space-y-6">
            @php
                $taskStatusTotal = collect($dashboard['tasks_by_status_active'] ?? [])->sum('total');
                $taskStatusOffset = 0;
                $taskStatusSegments = [];

                foreach (($dashboard['tasks_by_status_active'] ?? []) as $item) {
                    if ($taskStatusTotal === 0 || $item['total'] === 0) {
                        continue;
                    }

                    $taskColor = match ($item['color'] ?? null) {
                        'blue' => '#2563eb',
                        'yellow' => '#ca8a04',
                        'green' => '#15803d',
                        'red' => '#dc2626',
                        default => '#6b7280',
                    };

                    $slice = round(($item['total'] / $taskStatusTotal) * 100, 2);
                    $end = min(100, $taskStatusOffset + $slice);
                    $taskStatusSegments[] = "{$taskColor} {$taskStatusOffset}% {$end}%";
                    $taskStatusOffset = $end;
                }

                $taskStatusChartStyle = $taskStatusSegments !== []
                    ? 'background: conic-gradient(' . implode(', ', $taskStatusSegments) . ');'
                    : 'background: #e5e7eb;';

                $stepStatusTotal = collect($dashboard['steps_by_status_active'] ?? [])->sum('total');
                $stepStatusOffset = 0;
                $stepStatusSegments = [];

                foreach (($dashboard['steps_by_status_active'] ?? []) as $item) {
                    if ($stepStatusTotal === 0 || $item['total'] === 0) {
                        continue;
                    }

                    $stepColor = match ($item['color'] ?? null) {
                        'blue' => '#2563eb',
                        'yellow' => '#ca8a04',
                        'green' => '#15803d',
                        'red' => '#dc2626',
                        default => '#6b7280',
                    };

                    $slice = round(($item['total'] / $stepStatusTotal) * 100, 2);
                    $end = min(100, $stepStatusOffset + $slice);
                    $stepStatusSegments[] = "{$stepColor} {$stepStatusOffset}% {$end}%";
                    $stepStatusOffset = $end;
                }

                $stepStatusChartStyle = $stepStatusSegments !== []
                    ? 'background: conic-gradient(' . implode(', ', $stepStatusSegments) . ');'
                    : 'background: #e5e7eb;';

                $taskResponsibleMax = max(1, (int) (collect($dashboard['tasks_by_responsible'] ?? [])->max('total') ?? 0));
                $stepResponsibleMax = max(1, (int) (collect($dashboard['steps_by_responsible'] ?? [])->max('total') ?? 0));
                $organizationMax = max(1, (int) (collect($dashboard['steps_by_organization'] ?? [])->max('total') ?? 0));
                $stepsTotal = collect($dashboard['tasks_by_step_status'] ?? [])->sum('total');
            @endphp

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

                <!-- Dashboard Tarefas --> 
                <section class="overflow-hidden rounded-3xl border border-emerald-800 bg-white shadow-sm">
                    <div class="border-b border-emerald-100 bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 px-6 py-5 text-white">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase text-white">Painel de Tarefas</p>
                                <p class="text-xs text-white/80">Distribuição ativa, responsáveis e atrasos</p>
                            </div>
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                                {{ $dashboard['tasks_active_total'] ?? 0 }} ativas
                            </span>
                        </div>
                    </div>

                    <div class="space-y-6 p-6">
                        <div class="grid grid-cols-2 gap-6 md:grid-cols-3">

                            <div class="flex flex-col items-center justify-center gap-4">
                                <div class="relative h-40 w-40 rounded-full" style="{{ $taskStatusChartStyle }}">
                                    <div class="absolute inset-6 rounded-full bg-white shadow-inner"></div>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                        <span class="text-3xl font-bold text-gray-900">{{ $dashboard['total'] ?? 0 }}</span>
                                        <span class="text-[11px] font-semibold uppercase tracking-[0.25em] text-gray-500">Tarefas</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 space-y-5">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-semibold uppercase text-gray-500">Status</h4>
                                        <span class="text-[10px] text-gray-400 hidden md:block">Tarefas ativas</span>
                                    </div>

                                    @forelse (($dashboard['tasks_by_status_active'] ?? []) as $item)
                                        @php
                                            $taskBarColor = match ($item['color'] ?? null) {
                                                'blue' => 'from-blue-500 to-blue-700',
                                                'yellow' => 'from-yellow-400 to-amber-600',
                                                'green' => 'from-green-500 to-emerald-700',
                                                'red' => 'from-red-500 to-rose-700',
                                                default => 'from-slate-500 to-slate-700',
                                            };
                                        @endphp
                                        <div class="space-y-1.5">
                                            <div class="flex items-center justify-between gap-3 text-xs">
                                                <span class="text-xs truncate text-gray-600">{{ $item['label'] }}</span>
                                                <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                                            </div>
                                            <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                                @if ($item['total'] > 0)
                                                    <div class="h-full rounded-full bg-gradient-to-r {{ $taskBarColor }}" style="width: {{ max(10, $taskStatusTotal > 0 ? (int) round(($item['total'] / $taskStatusTotal) * 100) : 0) }}%"></div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-xs text-gray-400">Nenhuma tarefa ativa no momento.</div>
                                    @endforelse
                                </div>
                            </div>                          

                            <div class="col-span-3 space-y-3 border-y border-emerald-800/20 py-6">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-semibold uppercase text-gray-500">Responsáveis</h4>
                                    <span class="text-[11px] text-gray-400">Por tarefa</span>
                                </div>

                                @forelse (($dashboard['tasks_by_responsible'] ?? []) as $item)
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between gap-3 text-xs">
                                            <span class="text-xs truncate text-gray-600">{{ $item['label'] }}</span>
                                            <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                                        </div>
                                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-600 to-emerald-800"
                                                    style="width: {{ max(10, (int) round(($item['total'] / $taskResponsibleMax) * 100)) }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-xs text-gray-400">Nenhum responsável vinculado.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-2xl border border-rose-100 bg-rose-50/40 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <h4 class="text-xs font-semibold uppercase tracking-[0.25em] text-rose-700">Tarefas Atrasadas</h4>
                                <span class="text-[11px] text-rose-500">Top 8</span>
                            </div>

                            <div class="space-y-2">
                                @forelse (($dashboard['overdue_tasks'] ?? []) as $item)
                                    <button type="button" wire:click="openAsideTask({{ $item['id'] }})" class="w-full rounded-2xl border border-rose-100 bg-white px-4 py-3 text-left transition hover:border-rose-200 hover:bg-rose-50/40">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="truncate text-xs font-semibold text-gray-900">
                                                    <span class="font-mono text-[11px] text-rose-700">{{ $item['code'] }}</span>
                                                    <span class="mx-1 text-gray-300">-</span>
                                                    <span>{{ $item['title'] }}</span>
                                                </p>
                                                <p class="mt-1 truncate text-[11px] text-gray-500"><span class="text-gray-400">Responsável:</span>{{ $item['responsible'] }}</p>
                                            </div>
                                            <span class="rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-700">
                                                {{ $item['deadline_at'] ? \Illuminate\Support\Carbon::parse($item['deadline_at'])->format('d/m/Y') : '-' }}
                                            </span>
                                        </div>
                                    </button>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-emerald-800 bg-emerald-50 px-4 py-4 text-center text-xs text-emerald-700">Nenhuma tarefa atrasada.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Dashboard Etapas --> 
                <section class="overflow-hidden rounded-3xl border border-amber-700 bg-white shadow-sm">
                    <div class="border-b border-amber-100 bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 px-6 py-5 text-white">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase">Painel de Etapas</p>
                                <p class="text-xs text-white/80">Status, responsáveis, setores e pendências</p>
                            </div>
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                                {{ $dashboard['steps_active_total'] ?? 0 }} ativas
                            </span>
                        </div>
                    </div>

                    <div class="space-y-6 p-6">

                        <div class="grid grid-cols-2 gap-6 md:grid-cols-3">

                            <div class="flex flex-col items-center justify-center gap-4">
                                <div class="relative h-40 w-40 rounded-full" style="{{ $stepStatusChartStyle }}">
                                    <div class="absolute inset-6 rounded-full bg-white shadow-inner"></div>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                        <span class="text-3xl font-bold text-gray-900">{{ $stepsTotal }}</span>
                                        <span class="text-[11px] font-semibold uppercase tracking-[0.25em] text-gray-500">Etapas</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 space-y-5">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-semibold uppercase text-gray-500">Status</h4>
                                        <span class="text-[10px] text-gray-400 hidden md:block">Etapas ativas</span>
                                    </div>

                                    @forelse (($dashboard['steps_by_status_active'] ?? []) as $item)
                                        @php
                                            $stepBarColor = match ($item['color'] ?? null) {
                                                'blue' => 'from-blue-500 to-blue-700',
                                                'yellow' => 'from-yellow-400 to-amber-600',
                                                'green' => 'from-green-500 to-emerald-700',
                                                'red' => 'from-red-500 to-rose-700',
                                                default => 'from-slate-500 to-slate-700',
                                            };
                                        @endphp
                                        <div class="space-y-1.5">
                                            <div class="flex items-center justify-between gap-3 text-xs">
                                                <span class="text-xs text-gray-600">{{ $item['label'] }}</span>
                                                <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                                            </div>
                                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                                @if ($item['total'] > 0)
                                                    <div class="h-full rounded-full bg-gradient-to-r {{ $stepBarColor }}"
                                                     style="width: {{ max(10, $stepStatusTotal > 0 ? (int) round(($item['total'] / $stepStatusTotal) * 100) : 0) }}%"></div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-xs text-gray-400">Nenhuma etapa ativa no momento.</div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-span-3 grid grid-cols-1 gap-10 lg:grid-cols-2 border-y border-gray-300/80 py-6">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-semibold uppercase text-gray-500">Responsáveis</h4>
                                        <span class="text-[11px] text-gray-400">Por etapa</span>
                                    </div>

                                    @forelse (($dashboard['steps_by_responsible'] ?? []) as $item)
                                        <div class="space-y-1.5">
                                            <div class="flex items-center justify-between gap-3 text-xs">
                                                <span class="text-xs text-gray-600">{{ $item['label'] }}</span>
                                                <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                                            </div>
                                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                                <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-orange-700"
                                                        style="width: {{ max(10, (int) round(($item['total'] / $stepResponsibleMax) * 100)) }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-xs text-gray-400">Nenhum responsável vinculado.</div>
                                    @endforelse
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-semibold uppercase text-gray-500">Setores</h4>
                                        <span class="text-[11px] text-gray-400">Por etapa</span>
                                    </div>

                                    @forelse (($dashboard['steps_by_organization'] ?? []) as $item)
                                        <div class="space-y-1.5">
                                            <div class="flex items-center justify-between gap-3 text-xs">
                                                <span class="text-xs text-gray-600">{{ $item['label'] }}</span>
                                                <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                                            </div>
                                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                                <div class="h-full rounded-full bg-gradient-to-r from-slate-500 to-slate-700"
                                                        style="width: {{ max(10, (int) round(($item['total'] / $organizationMax) * 100)) }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-xs text-gray-400">Nenhum setor vinculado.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-rose-100 bg-rose-50/40 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <h4 class="text-xs font-semibold uppercase tracking-[0.25em] text-rose-700">Etapas Atrasadas</h4>
                                <span class="text-[11px] text-rose-500">Top 8</span>
                            </div>

                            <div class="space-y-2">
                                @forelse (($dashboard['overdue_steps'] ?? []) as $item)
                                    <button type="button" wire:click="openAsideTaskStep({{ $item['id'] }})" class="w-full rounded-2xl border border-rose-100 bg-white px-4 py-3 text-left transition hover:border-rose-200 hover:bg-rose-50/40">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="truncate text-xs font-semibold text-gray-900">
                                                    <span class="font-mono text-[11px] text-rose-700">{{ $item['code'] }}</span>
                                                    <span class="mx-1 text-gray-300">-</span>
                                                    <span>{{ $item['title'] }}</span>
                                                </p>
                                                <p class="mt-1 truncate text-[11px] text-gray-500">{{ $item['task_code'] ? $item['task_code'].' - ' : '' }}{{ $item['responsible'] }}</p>
                                            </div>
                                            <span class="rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-700">
                                                {{ $item['deadline_at'] ? \Illuminate\Support\Carbon::parse($item['deadline_at'])->format('d/m/Y') : '-' }}
                                            </span>
                                        </div>
                                    </button>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-amber-200 bg-amber-50 px-4 py-4 text-center text-xs text-amber-700">Nenhuma etapa atrasada.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div x-show="tab === 'list'" x-cloak>
            <section class="overflow-hidden rounded-3xl border border-emerald-800 bg-white shadow-sm">
                <div class="border-b border-emerald-800 bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 px-6 py-5 text-white">
                    <div class="flex flex-row gap-3 items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase">Lista de Tarefas</p>
                            <p class="mt-1 text-xs text-white/80 hidden md:block">Acompanhe tarefas, responsáveis, prazos e desdobre etapas sem sair da listagem.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                                {{ $tasks->total() }} tarefas
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 p-4 md:p-6">
                    <x-page.filter title="Filtros da Lista" :showClear="true" clearAction="resetListFilters">
                        <div class="col-span-12 md:col-span-6 xl:col-span-4">
                            <x-form.label value="Título" />
                            <x-form.input wire:model.live.debounce.500ms="filters.title" placeholder="Buscar por título da tarefa..." />
                        </div>

                        <div class="col-span-12 md:col-span-6 xl:col-span-4">
                            <x-form.label value="Setor" />
                            <x-form.select-livewire
                                wire:model.live="filters.organization_id"
                                name="filters.organization_id"
                                :collection="$accessibleOrganizations"
                                labelField="title"
                                labelAcronym="acronym"
                                valueField="id"
                                placeholder="Todos os setores"
                            />
                        </div>

                        <div class="col-span-12 md:col-span-6 xl:col-span-4">
                            <x-form.label value="Responsável" />
                            <x-form.select-livewire
                                wire:model.live="filters.user_id"
                                name="filters.user_id"
                                :collection="$responsibleUsers"
                                valueField="id"
                                labelField="name"
                                placeholder="Todos os responsáveis"
                            />
                        </div>

                        <div class="col-span-12 md:col-span-6 xl:col-span-3">
                            <x-form.label value="Categoria" />
                            <x-form.select-livewire
                                wire:model.live="filters.task_category_id"
                                name="filters.task_category_id"
                                :collection="$taskCategories"
                                valueField="id"
                                labelField="title"
                                placeholder="Todas as categorias"
                            />
                        </div>

                        <div class="col-span-12 md:col-span-6 xl:col-span-3">
                            <x-form.label value="Status" />
                            <x-form.select-livewire
                                wire:model.live="filters.task_status_id"
                                name="filters.task_status_id"
                                :collection="$taskStatuses"
                                valueField="id"
                                labelField="title"
                                placeholder="Todos os status"
                            />
                        </div>

                        <div class="col-span-12 md:col-span-6 xl:col-span-3">
                            <x-form.label value="Prioridade" />
                            <x-form.select-livewire
                                wire:model.live="filters.task_priority_id"
                                name="filters.task_priority_id"
                                :collection="$taskPriorities"
                                valueField="id"
                                labelField="title"
                                placeholder="Todas as prioridades"
                            />
                        </div>

                        <div class="col-span-12 md:col-span-6 xl:col-span-3">
                            <x-form.label value="Atrasada" />
                            <x-form.select-livewire
                                wire:model.live="filters.is_overdue"
                                name="filters.is_overdue"
                                :options="[
                                    ['value' => 'all', 'label' => 'Todas'],
                                    ['value' => 'yes', 'label' => 'Sim'],
                                    ['value' => 'no', 'label' => 'Não'],
                                ]"
                                :searchable="false"
                            />
                        </div>
                    </x-page.filter>

                    @forelse ($tasks as $task)
                        <article class="overflow-hidden rounded-3xl border {{ $task->finished_at ? 'border-emerald-800 bg-emerald-50/40' : 'border-gray-200 bg-white' }} shadow-sm transition-colors duration-200 hover:border-emerald-800 hover:bg-emerald-50/20">
                            <div class="space-y-4 p-4 md:p-5">
                                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                    <div class="min-w-0 flex-1 space-y-3">
                                        <div class="flex items-start gap-2">

                                            <div class="min-w-0 flex-1 flex items-center gap-2">
                                                
                                                <x-button
                                                    type="button"
                                                    variant="green_text"
                                                    @click.stop="expandedTaskId = expandedTaskId === {{ $task->id }} ? null : {{ $task->id }}"
                                                    title="{{ $task->taskSteps->count() ? 'Mostrar etapas' : 'Sem etapas' }}"
                                                    class="mt-0.5 flex items-center rounded-lg"
                                                >
                                                    <i class="fas fa-chevron-right text-[10px] transition-transform duration-200" :class="expandedTaskId === {{ $task->id }} ? 'rotate-90' : ''"></i>
                                                </x-button>

                                                <button
                                                    type="button"
                                                    wire:click="openAsideTask({{ $task->id }})"
                                                    class="w-full text-left"
                                                >
                                                    <p class="truncate text-xs font-semibold text-gray-900">
                                                        <span class="font-mono text-xs uppercase text-emerald-700">{{ $task->code }}</span>
                                                        <span class="mx-2 text-gray-300">-</span>
                                                        <span class="text-xs">{{ $task->title }}</span>
                                                    </p>
                                                </button>
                                            </div>

                                            <x-button
                                                type="button"
                                                variant="green_text"
                                                @click.stop="expandedTaskId = {{ $task->id }}; stepFormTaskId = stepFormTaskId === {{ $task->id }} ? null : {{ $task->id }}"
                                                title="Nova etapa"
                                            >
                                                <i class="fas fa-plus text-[10px]"></i>
                                            </x-button>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                                                Responsável: {{ $task->user?->name ?? 'Não definido' }}
                                            </span>

                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                                                {{ $task->taskCategory?->title ?? 'Sem categoria' }}
                                            </span>

                                            @if ($task->taskStatus)
                                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $task->taskStatus->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">
                                                    {{ $task->taskStatus->title }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-500">
                                                    Sem status
                                                </span>
                                            @endif

                                            @if ($task->taskPriority)
                                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $task->taskPriority->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">
                                                    {{ $task->taskPriority->title }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-500">
                                                    Sem prioridade
                                                </span>
                                            @endif

                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                                                Início: {{ $task->started_at?->format('d/m/Y') ?? '-' }}
                                            </span>

                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $task->deadline_at && $task->deadline_at->isPast() && ! $task->finished_at ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700' }}">
                                                Prazo: {{ $task->deadline_at?->format('d/m/Y') ?? '-' }}
                                            </span>

                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                                                Etapas: {{ $task->taskSteps->count() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div x-show="expandedTaskId === {{ $task->id }}" x-cloak class="border-t border-amber-200 bg-amber-50/40">
                                <div class="space-y-4 p-4 md:p-5">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-amber-700">Etapas da Tarefa</p>
                                        </div>
                                        <span class="rounded-full border border-amber-200 bg-white px-3 py-1 text-xs font-semibold text-amber-700">
                                            {{ $task->taskSteps->count() }} etapas
                                        </span>
                                    </div>

                                    <div x-show="stepFormTaskId === {{ $task->id }}" x-cloak class="pt-2">
                                        @include('livewire.task._partials.task-step-form', ['taskId' => $task->id])
                                    </div>

                                    <div class="space-y-3">
                                        @forelse ($task->taskSteps as $step)
                                            <div class="rounded-2xl border border-amber-200 bg-white px-4 py-4 shadow-sm">
                                                <div class="space-y-3">
                                                    <div class="min-w-0">
                                                        <x-button type="button" variant="yellow_text" wire:click="openAsideTaskStep({{ $step->id }})">
                                                            <div class="line-clamp-1 text-left">
                                                                <span class="text-[11px] font-mono font-medium text-amber-700">{{ $step->code }}</span>
                                                                <span class="mx-1 text-gray-300">-</span>
                                                                <span class="text-xs font-medium text-gray-800" title="{{ $step->title }}">{{ $step->title }}</span>
                                                            </div>
                                                        </x-button>
                                                    </div>

                                                    <div class="flex flex-wrap gap-2">

                                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                                                            Setor: {{ $step->organization?->acronym ?? $step->organization?->title ?? 'Sem setor' }}
                                                        </span>

                                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                                                            Responsável: {{ $step->user?->name ?? 'Não definido' }}
                                                        </span>

                                                        @if ($step->taskStepStatus)
                                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $step->taskStepStatus->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">
                                                                {{ $step->taskStepStatus->title }}
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-500">
                                                                Sem status
                                                            </span>
                                                        @endif

                                                        @if ($step->taskPriority)
                                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $step->taskPriority->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">
                                                                {{ $step->taskPriority->title }}
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-500">
                                                                Sem prioridade
                                                            </span>
                                                        @endif

                                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-700">
                                                            Início: {{ $step->started_at?->format('d/m/Y') ?? '-' }}
                                                        </span>

                                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $step->deadline_at && $step->deadline_at->isPast() && ! $step->finished_at ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700' }}">
                                                            Prazo: {{ $step->deadline_at?->format('d/m/Y') ?? '-' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="rounded-2xl border border-dashed border-amber-200 bg-white/80 px-4 py-6 text-center text-xs text-gray-400">
                                                Esta tarefa ainda não possui etapas.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-gray-200 bg-gray-50 px-6 py-12 text-center text-xs text-gray-400">
                            Nenhuma tarefa encontrada.
                        </div>
                    @endforelse

                    @if ($tasks->hasPages())
                        <div class="border-t border-gray-200 pt-4">
                            {{ $tasks->links() }}
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <div x-show="tab === 'step-kanban'" x-cloak>
            <section class="overflow-hidden rounded-3xl border border-amber-200 bg-white shadow-sm">
                <div class="border-b border-amber-200 bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 px-6 py-5 text-white">
                    <div class="flex gap-3 items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase">Kanban de Etapas</p>
                            <p class="mt-1 text-xs text-white/80 hidden md:block">Movimente etapas entre colunas com o mesmo padrão visual das demais visões do ambiente.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                                {{ collect($stepKanban ?? [])->count() }} colunas
                            </span>
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                                {{ collect($stepKanban ?? [])->flatMap(fn ($column) => $column['steps'])->count() }} etapas
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-b border-amber-100 bg-amber-50/70 px-4 py-4 md:px-6">
                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)_minmax(0,1fr)_auto]">
                        <x-form.select-livewire
                            wire:model.live="stepKanbanFilters.task_id"
                            name="stepKanbanFilters.task_id"
                            :collection="$stepKanbanTaskOptions"
                            valueField="id"
                            labelField="title"
                            placeholder="Todos os projetos"
                            borderColor="yellow"
                        />

                        <x-form.select-livewire
                            wire:model.live="stepKanbanFilters.organization_id"
                            name="stepKanbanFilters.organization_id"
                            :collection="$accessibleOrganizations"
                            valueField="id"
                            labelField="title"
                            labelAcronym="acronym"
                            placeholder="Todos os setores"
                            borderColor="yellow"
                        />

                        <x-form.select-livewire
                            wire:model.live="stepKanbanFilters.user_id"
                            name="stepKanbanFilters.user_id"
                            :collection="$responsibleUsers"
                            valueField="id"
                            labelField="name"
                            placeholder="Todos os responsaveis"
                            borderColor="yellow"
                        />

                        <x-button
                            type="button"
                            text="Limpar"
                            icon="fa-solid fa-filter-circle-xmark"
                            variant="gray_outline"
                            wire:click="resetStepKanbanFilters"
                        />
                    </div>

                </div>

                <div class="overflow-x-auto px-4 py-5 md:px-6">
                    <div class="grid min-w-[980px] grid-flow-col auto-cols-[minmax(250px,1fr)] gap-4">
                        @forelse (($stepKanban ?? []) as $column)
                            @php
                                $columnStepIds = $column['steps']
                                    ->pluck('id')
                                    ->map(fn ($id): int => (int) $id)
                                    ->values()
                                    ->all();

                                $columnTheme = match ($column['color'] ?? null) {
                                    'blue' => [
                                        'border' => 'border-blue-200',
                                        'surface' => 'from-blue-50/70 to-white',
                                        'header' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'count' => 'bg-blue-100 text-blue-700',
                                        'drop' => 'bg-blue-100/40',
                                        'placeholder' => 'border-blue-300 bg-blue-100/70 text-blue-700',
                                        'card' => 'border-blue-100',
                                        'code' => 'text-blue-700',
                                    ],
                                    'yellow' => [
                                        'border' => 'border-yellow-200',
                                        'surface' => 'from-yellow-50/70 to-white',
                                        'header' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'count' => 'bg-yellow-100 text-yellow-700',
                                        'drop' => 'bg-yellow-100/40',
                                        'placeholder' => 'border-yellow-300 bg-yellow-100/70 text-yellow-700',
                                        'card' => 'border-yellow-100',
                                        'code' => 'text-yellow-700',
                                    ],
                                    'green' => [
                                        'border' => 'border-green-200',
                                        'surface' => 'from-green-50/70 to-white',
                                        'header' => 'bg-green-100 text-green-800 border-green-200',
                                        'count' => 'bg-green-100 text-green-700',
                                        'drop' => 'bg-green-100/40',
                                        'placeholder' => 'border-green-300 bg-green-100/70 text-green-700',
                                        'card' => 'border-green-100',
                                        'code' => 'text-green-700',
                                    ],
                                    'red' => [
                                        'border' => 'border-red-200',
                                        'surface' => 'from-red-50/70 to-white',
                                        'header' => 'bg-red-100 text-red-800 border-red-200',
                                        'count' => 'bg-red-100 text-red-700',
                                        'drop' => 'bg-red-100/40',
                                        'placeholder' => 'border-red-300 bg-red-100/70 text-red-700',
                                        'card' => 'border-red-100',
                                        'code' => 'text-red-700',
                                    ],
                                    'gray' => [
                                        'border' => 'border-gray-200',
                                        'surface' => 'from-gray-50/80 to-white',
                                        'header' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        'count' => 'bg-gray-100 text-gray-700',
                                        'drop' => 'bg-gray-100/60',
                                        'placeholder' => 'border-gray-300 bg-gray-100/80 text-gray-700',
                                        'card' => 'border-gray-200',
                                        'code' => 'text-gray-700',
                                    ],
                                    default => [
                                        'border' => 'border-slate-200',
                                        'surface' => 'from-slate-50/80 to-white',
                                        'header' => 'bg-slate-100 text-slate-800 border-slate-200',
                                        'count' => 'bg-slate-100 text-slate-700',
                                        'drop' => 'bg-slate-100/50',
                                        'placeholder' => 'border-slate-300 bg-slate-100/80 text-slate-700',
                                        'card' => 'border-slate-200',
                                        'code' => 'text-slate-700',
                                    ],
                                };
                            @endphp
                            <section class="flex h-full flex-col overflow-hidden rounded-3xl border {{ $columnTheme['border'] }} bg-gradient-to-b {{ $columnTheme['surface'] }} shadow-sm">
                                <header class="border-b border-white/70 px-4 pt-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-gray-400">Status</p>
                                            <span class="mt-2 inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $columnTheme['header'] }}">
                                                {{ $column['title'] }}
                                            </span>
                                        </div>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold shadow-sm {{ $columnTheme['count'] }}">
                                            {{ $column['steps']->count() }}
                                        </span>
                                    </div>
                                </header>

                                <div class="flex-1 space-y-0.5 p-3"
                                     @dragover.prevent="
                                        dragOverStatusId = {{ (int) $column['status_id'] }};
                                        dragOverInsertBeforeId = null;
                                     "
                                     @dragleave="
                                        if (dragOverStatusId === {{ (int) $column['status_id'] }}) {
                                            dragOverStatusId = null;
                                            dragOverInsertBeforeId = null;
                                        }
                                     "
                                     @drop.prevent="dropStepOnColumn({{ (int) $column['status_id'] }}, @js($columnStepIds))"
                                     :class="dragOverStatusId === {{ (int) $column['status_id'] }} ? '{{ $columnTheme['drop'] }} rounded-3xl' : ''">
                                    @forelse ($column['steps'] as $step)
                                        <div class="overflow-hidden rounded-xl transition-all duration-150"
                                             @dragover.prevent="
                                                dragOverStatusId = {{ (int) $column['status_id'] }};
                                                dragOverInsertBeforeId = {{ $step->id }};
                                             "
                                             @drop.prevent="dropStepOnColumn({{ (int) $column['status_id'] }}, @js($columnStepIds), {{ $step->id }})"></div>

                                        <div class="overflow-hidden rounded-xl transition-all duration-150"
                                             :class="draggedStepId !== null && dragOverStatusId === {{ (int) $column['status_id'] }} && dragOverInsertBeforeId === {{ $step->id }} ? 'max-h-14 opacity-100 mb-1' : 'max-h-0 opacity-0'">
                                            <div class="flex items-center gap-2 rounded-xl border-2 border-dashed px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.2em] {{ $columnTheme['placeholder'] }}">
                                                <i class="fa-solid fa-down-long"></i>
                                                <span>Solte aqui</span>
                                            </div>
                                        </div>

                                        <article class="rounded-2xl border {{ $columnTheme['card'] }} bg-white p-3 shadow-sm transition hover:shadow-md cursor-grab active:cursor-grabbing"
                                                 draggable="true"
                                                 @dragstart="
                                                     draggedStepId = {{ $step->id }};
                                                     draggedFromStatusId = {{ (int) $column['status_id'] }};
                                                  "
                                                 @dragend="
                                                    draggedStepId = null;
                                                    draggedFromStatusId = null;
                                                    dragOverStatusId = null;
                                                    dragOverInsertBeforeId = null;
                                                 ">
                                            <div class="flex items-start justify-between gap-3">
                                                <button type="button" wire:click="openAsideTaskStep({{ $step->id }})" class="min-w-0 flex-1 text-left">
                                                    <p class="truncate text-xs font-semibold text-gray-900">
                                                        <span class="font-mono {{ $columnTheme['code'] }}">{{ $step->code }}</span>
                                                        <span class="mx-1 text-gray-300">-</span>
                                                        <span>{{ $step->title }}</span>
                                                    </p>
                                                    <p class="mt-1 truncate text-[11px] text-gray-500">
                                                        {{ $step->task?->code ? $step->task->code.' - ' : '' }}{{ $step->task?->title ?? 'Sem tarefa vinculada' }}
                                                    </p>
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-3 gap-2">
                                                <div class="flex items-center gap-1 mt-1 truncate text-[11px] text-gray-500">
                                                    <span>Setor:</span>
                                                    <p>{{ $step->organization?->acronym ?? $step->organization?->title ?? 'Sem setor' }}</p>
                                                </div>

                                                <div class="col-span-2 flex items-center gap-1 mt-1 truncate text-[11px] text-gray-500">
                                                    <span>Responsável:</span>
                                                    <p>{{ $step->user?->name ?? 'Sem responsável' }}</p>
                                                </div>
                                            </div>
                                        </article>
                                    @empty
                                        <div class="flex min-h-44 items-center justify-center rounded-2xl border border-dashed border-amber-200 bg-white/80 px-4 py-6 text-center text-xs text-gray-400">
                                            Nenhuma etapa nesta coluna.
                                        </div>
                                    @endforelse

                                    @if ($column['steps']->isNotEmpty())
                                        <div class="overflow-hidden rounded-xl transition-all duration-150"
                                             @dragover.prevent="
                                                dragOverStatusId = {{ (int) $column['status_id'] }};
                                                dragOverInsertBeforeId = null;
                                             "
                                             @drop.prevent="dropStepOnColumn({{ (int) $column['status_id'] }}, @js($columnStepIds))"
                                             :class="draggedStepId !== null && dragOverStatusId === {{ (int) $column['status_id'] }} && dragOverInsertBeforeId === null ? 'max-h-14 opacity-100 mt-1' : 'max-h-0 opacity-0'">
                                            <div class="flex items-center gap-2 rounded-xl border-2 border-dashed px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.2em] {{ $columnTheme['placeholder'] }}">
                                                <i class="fa-solid fa-down-long"></i>
                                                <span>Solte no fim da coluna</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </section>
                        @empty
                            <div class="rounded-3xl border border-dashed border-gray-200 bg-gray-50 px-6 py-10 text-center text-xs text-gray-400">
                                Nenhuma etapa encontrada para montar o kanban.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        @if ($canManageSettings)
        <div x-show="tab === 'settings'" x-cloak>
            <div class="space-y-6">
                <section class="overflow-hidden rounded-3xl border border-sky-800 bg-white shadow-sm">
                    <div class="border-b border-sky-200 bg-gradient-to-r from-sky-700 via-sky-700 to-sky-900 px-6 py-5 text-white">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase">Configurações do Ambiente</p>
                                <p class="mt-1 text-xs text-white/80">Centralize a parametrização e os acessos do ambiente em uma única área.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                                    {{ $taskHubCategories->count() }} categorias
                                </span>
                                <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                                    {{ $accessEntries->count() }} acessos
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 p-6">
                        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                            <section class="space-y-4 rounded-3xl border border-sky-100 bg-white p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-sky-700">Categorias</p>
                                        <h3 class="mt-2 text-xs font-semibold text-gray-900">Categorias deste Ambiente</h3>
                                        <p class="mt-1 text-xs text-gray-500">
                                            As categorias cadastradas aqui aparecem apenas neste ambiente.
                                        </p>
                                    </div>

                                    @if ($canManageMembers)
                                        <x-button
                                            type="button"
                                            text="Nova Categoria"
                                            icon="fa-solid fa-plus"
                                            variant="gray_outline"
                                            wire:click="createTaskCategory"
                                        />
                                    @endif
                                </div>

                                <div class="space-y-3">
                                    @forelse ($taskHubCategories as $category)
                                        <div class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-sky-50/40 px-4 py-4 md:flex-row md:items-start md:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="truncate text-xs font-semibold text-gray-900">{{ $category->title }}</p>
                                                    <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $category->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                        {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                                                    </span>
                                                </div>

                                                <p class="mt-2 text-xs text-gray-500">
                                                    {{ $category->description ?: 'Sem descrição cadastrada.' }}
                                                </p>
                                            </div>

                                            @if ($canManageMembers)
                                                <div class="flex items-center gap-2">
                                                    <x-button
                                                        type="button"
                                                        icon="fa-solid fa-pen-to-square"
                                                        variant="gray_outline"
                                                        wire:click="editTaskCategory({{ (int) $category->id }})"
                                                    />

                                                    <x-button
                                                        type="button"
                                                        icon="{{ $category->is_active ? 'fa-solid fa-toggle-on' : 'fa-solid fa-toggle-off' }}"
                                                        variant="gray_outline"
                                                        wire:click="toggleTaskCategoryStatus({{ (int) $category->id }})"
                                                    />
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-10 text-center text-xs text-gray-400">
                                            Nenhuma categoria local cadastrada para este ambiente.
                                        </div>
                                    @endforelse
                                </div>
                            </section>

                            <aside class="rounded-3xl border border-sky-100 bg-sky-50/50 p-5">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-sky-700">Orientação</p>
                                <h4 class="mt-2 text-xs font-semibold text-gray-900">Como funciona</h4>

                                <div class="mt-4 space-y-3 text-xs text-gray-600">
                                    <div class="rounded-2xl border border-white bg-white px-4 py-3">
                                        <p class="font-semibold text-gray-900">Escopo local</p>
                                        <p class="mt-1">Cada categoria criada aqui fica vinculada apenas a este ambiente.</p>
                                    </div>

                                    <div class="rounded-2xl border border-white bg-white px-4 py-3">
                                        <p class="font-semibold text-gray-900">Uso imediato</p>
                                        <p class="mt-1">Assim que salvar, a categoria já aparece no formulário de nova tarefa e no detalhe das tarefas deste ambiente.</p>
                                    </div>

                                    <div class="rounded-2xl border border-white bg-white px-4 py-3">
                                        <p class="font-semibold text-gray-900">Controle do proprietário</p>
                                        <p class="mt-1">Somente o proprietário do ambiente pode criar, editar ou inativar categorias locais.</p>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-3xl border border-sky-800 bg-white shadow-sm">
                    <div class="border-b border-sky-200 bg-gradient-to-r from-sky-700 via-sky-700 to-sky-900 px-6 py-5 text-white">
                        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase">Status do Ambiente</p>
                                <p class="mt-1 text-xs text-white/80">Gerencie os status de tarefas e etapas usados no fluxo deste ambiente.</p>
                            </div>
                            <span class="rounded-full border border-white/30 bg-white/10 px-3 py-1 text-xs font-semibold">
                                {{ $taskHubTaskStatuses->count() + $taskHubTaskStepStatuses->count() }} status
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 p-6 xl:grid-cols-2">
                        <section class="space-y-4 rounded-2xl border border-sky-100 bg-sky-50/30 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-sky-700">Tarefas</p>
                                    <h4 class="mt-2 text-xs font-semibold text-gray-900">Status de Tarefa</h4>
                                </div>
                                <x-button type="button" text="Novo" icon="fa-solid fa-plus" variant="gray_outline" wire:click="createTaskStatus" />
                            </div>

                            <div class="space-y-2">
                                @forelse ($taskHubTaskStatuses as $status)
                                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-white px-3 py-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-xs font-semibold text-gray-900">{{ $status->title }}</p>
                                            <p class="mt-1 text-[11px] text-gray-500">
                                                {{ $status->is_default ? 'Padrão' : 'Opcional' }} - {{ $status->is_active ? 'Ativo' : 'Inativo' }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <x-button type="button" icon="fa-solid fa-pen-to-square" variant="gray_outline" wire:click="editTaskStatus({{ (int) $status->id }})" />
                                            <x-button type="button" icon="{{ $status->is_default ? 'fa-solid fa-star' : 'fa-regular fa-star' }}" variant="gray_outline" wire:click="setTaskStatusDefault({{ (int) $status->id }})" />
                                            <x-button type="button" icon="{{ $status->is_active ? 'fa-solid fa-toggle-on' : 'fa-solid fa-toggle-off' }}" variant="gray_outline" wire:click="toggleTaskStatus({{ (int) $status->id }})" />
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-center text-xs text-gray-400">
                                        Nenhum status de tarefa cadastrado.
                                    </div>
                                @endforelse
                            </div>
                        </section>

                        <section class="space-y-4 rounded-2xl border border-sky-100 bg-sky-50/30 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-sky-700">Etapas</p>
                                    <h4 class="mt-2 text-xs font-semibold text-gray-900">Status de Etapa</h4>
                                </div>
                                <x-button type="button" text="Novo" icon="fa-solid fa-plus" variant="gray_outline" wire:click="createTaskStepStatus" />
                            </div>

                            <div class="space-y-2">
                                @forelse ($taskHubTaskStepStatuses as $status)
                                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-white px-3 py-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-xs font-semibold text-gray-900">{{ $status->title }}</p>
                                            <p class="mt-1 text-[11px] text-gray-500">
                                                {{ $status->is_default ? 'Padrão' : 'Opcional' }} - {{ $status->is_active ? 'Ativo' : 'Inativo' }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <x-button type="button" icon="fa-solid fa-pen-to-square" variant="gray_outline" wire:click="editTaskStepStatus({{ (int) $status->id }})" />
                                            <x-button type="button" icon="{{ $status->is_default ? 'fa-solid fa-star' : 'fa-regular fa-star' }}" variant="gray_outline" wire:click="setTaskStepStatusDefault({{ (int) $status->id }})" />
                                            <x-button type="button" icon="{{ $status->is_active ? 'fa-solid fa-toggle-on' : 'fa-solid fa-toggle-off' }}" variant="gray_outline" wire:click="toggleTaskStepStatus({{ (int) $status->id }})" />
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-center text-xs text-gray-400">
                                        Nenhum status de etapa cadastrado.
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    </div>
                </section>

                <section class="overflow-hidden rounded-3xl border border-sky-800 bg-white shadow-sm">
                    <div class="border-b border-sky-800 bg-gradient-to-r from-sky-700 via-sky-700 to-sky-900 px-6 py-5 text-white">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase">Compartilhamento do Ambiente</p>
                                <p class="mt-1 text-xs text-white/80">Defina quem pode operar o ambiente individualmente ou por setor.</p>
                            </div>
                            <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700">
                                {{ $accessEntries->count() }} acessos ativos
                            </span>
                        </div>
                    </div>

                    <div class="space-y-6 p-6">
                        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr] border-b border-sky-800 pb-5">
                            <section class="space-y-4 rounded-3xl border border-slate-200 bg-white p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-600">Usuários</p>
                                        <h4 class="mt-2 text-xs font-semibold text-gray-900">Compartilhamento de Usuários</h4>
                                        <p class="mt-1 text-xs text-gray-500">Controle o acesso individual ao ambiente.</p>
                                    </div>

                                    @if ($canManageMembers)
                                        <x-button
                                            type="button"
                                            text="Adicionar"
                                            icon="fa-solid fa-user-plus"
                                            variant="gray_outline"
                                            wire:click="openMemberShareModal"
                                        />
                                    @endif
                                </div>

                                <div class="space-y-3">
                                    @forelse ($accessEntries as $entry)
                                        @if ($entry['type'] !== 'sector')
                                            <div class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-4 md:flex-row md:items-center md:justify-between">
                                                <div class="min-w-0">
                                                    <p class="truncate text-xs font-semibold text-gray-900">{{ $entry['user']->name ?? 'Usuário' }}</p>
                                                    <p class="mt-1 text-xs text-gray-500">{{ $entry['user']->email ?? 'Sem e-mail' }}</p>
                                                    @if (! empty($entry['sector_labels']))
                                                        <p class="mt-1 text-[11px] text-gray-400">
                                                            Setor: {{ implode(', ', $entry['sector_labels']) }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    @if ($entry['type'] === 'owner')
                                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-emerald-700">
                                                            Proprietário
                                                        </span>
                                                    @else
                                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-700">
                                                            Membro
                                                        </span>
                                                    @endif

                                                    @if ($canManageMembers && $entry['type'] === 'member' && $entry['membership_id'])
                                                        <x-button
                                                            type="button"
                                                            text="Remover"
                                                            icon="fa-solid fa-user-minus"
                                                            variant="gray_outline"
                                                            wire:click="removeMember({{ (int) $entry['membership_id'] }})"
                                                        />
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-10 text-center text-xs text-gray-400">
                                            Nenhum membro vinculado a este ambiente.
                                        </div>
                                    @endforelse
                                </div>
                            </section>

                            <aside class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-600">Orientação</p>
                                <h4 class="mt-2 text-xs font-semibold text-gray-900">Como funciona</h4>

                                <div class="mt-4 space-y-3 text-xs text-gray-600">
                                    <div class="rounded-2xl border border-white bg-white px-4 py-3">
                                        <p class="font-semibold text-gray-900">Acesso direto</p>
                                        <p class="mt-1">Adicione pessoas específicas quando o acesso precisar ser individual.</p>
                                    </div>

                                    <div class="rounded-2xl border border-white bg-white px-4 py-3">
                                        <p class="font-semibold text-gray-900">Controle fino</p>
                                        <p class="mt-1">Ideal para consultores, apoio temporário ou exceções fora dos setores.</p>
                                    </div>

                                    <div class="rounded-2xl border border-white bg-white px-4 py-3">
                                        <p class="font-semibold text-gray-900">Gestão restrita</p>
                                        <p class="mt-1">Somente o proprietário do ambiente pode adicionar ou remover membros.</p>
                                    </div>
                                </div>
                            </aside>
                        </div>

                        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                            <section class="space-y-4 rounded-3xl border border-slate-200 bg-white p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-600">Setores</p>
                                        <h4 class="mt-2 text-xs font-semibold text-gray-900">Compartilhamento por Setor</h4>
                                        <p class="mt-1 text-xs text-gray-500">Vincule setores para liberar acesso em lote.</p>
                                    </div>

                                    @if ($canManageMembers)
                                        <x-button
                                            type="button"
                                            text="Adicionar"
                                            icon="fa-solid fa-users"
                                            variant="gray_outline"
                                            wire:click="openOrganizationShareModal"
                                        />
                                    @endif
                                </div>

                                <div class="space-y-3">
                                    @forelse ($organizationAccesses as $organization)
                                        <div class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-4 md:flex-row md:items-center md:justify-between">
                                            <div class="min-w-0">
                                                <p class="truncate text-xs font-semibold text-gray-900">{{ $organization->acronym ?? $organization->title }}</p>
                                                <p class="mt-1 text-xs text-gray-500">{{ $organization->title }}</p>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-700">
                                                    Setor
                                                </span>

                                                <x-button
                                                    type="button"
                                                    icon="fa-solid fa-users"
                                                    variant="gray_outline"
                                                    wire:click="toggleOrganizationUsers({{ (int) $organization->id }})"
                                                />

                                                @if ($canManageMembers)
                                                    <x-button
                                                        type="button"
                                                        icon="fa-solid fa-trash"
                                                        variant="gray_outline"
                                                        wire:click="removeOrganizationAccess({{ (int) $organization->id }})"
                                                    />
                                                @endif
                                            </div>
                                        </div>

                                        @if (in_array((int) $organization->id, $expandedOrganizationIds, true))
                                            <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-600">
                                                @php($userNames = $organization->users?->pluck('name')->filter()->values() ?? collect())
                                                @if ($userNames->isEmpty())
                                                    Nenhum usuário vinculado a este setor.
                                                @else
                                                    <p>{{ $userNames->implode(', ') }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-8 text-center text-xs text-gray-400">
                                            Nenhum setor vinculado a este ambiente.
                                        </div>
                                    @endforelse
                                </div>
                            </section>

                            <aside class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-600">Orientação</p>
                                <h4 class="mt-2 text-xs font-semibold text-gray-900">Como funciona</h4>

                                <div class="mt-4 space-y-3 text-xs text-gray-600">
                                    <div class="rounded-2xl border border-white bg-white px-4 py-3">
                                        <p class="font-semibold text-gray-900">Acesso automático</p>
                                        <p class="mt-1">Quem pertence ao setor vinculado passa a enxergar este ambiente automaticamente.</p>
                                    </div>

                                    <div class="rounded-2xl border border-white bg-white px-4 py-3">
                                        <p class="font-semibold text-gray-900">Escala melhor</p>
                                        <p class="mt-1">Use esse modo quando o ambiente for operado por uma equipe inteira, não por pessoas isoladas.</p>
                                    </div>

                                    <div class="rounded-2xl border border-white bg-white px-4 py-3">
                                        <p class="font-semibold text-gray-900">Transparência</p>
                                        <p class="mt-1">O botão de usuários mostra quem está herdando acesso a partir do setor selecionado.</p>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        @endif

        <div x-show="tab === 'list' || tab === 'dashboard' || tab === 'step-kanban'" x-cloak class="fixed right-4 bottom-4 z-20 md:right-6 md:bottom-6">
            <x-button
                type="button"
                text="Nova Tarefa"
                icon="fa-solid fa-plus"
                wire:click="enableCreateTask"
            />
        </div>

        @if ($selectedTaskId)
            <div wire:click="closedAsideTask()" class="fixed inset-0 z-30 bg-black/50"></div>
            <div class="fixed top-0 right-0 z-40 h-screen w-full overflow-hidden border-l border-gray-200 bg-white shadow-xl md:w-3/5">
                <livewire:task.task-aside :taskId="$selectedTaskId" wire:key="aside-task-{{ $selectedTaskId }}" />
            </div>
        @endif

        @if ($selectedStepId)
            <div wire:click="closedAsideTaskStep()" class="fixed inset-0 z-30 bg-black/50"></div>
            <div class="fixed top-0 right-0 z-40 h-screen w-full overflow-hidden border-l border-gray-200 bg-white shadow-xl md:w-3/5">
                <livewire:task.task-step-aside :stepId="$selectedStepId" wire:key="aside-step-{{ $selectedStepId }}" />
            </div>
        @endif

        <x-modal :show="$showModal">
            @if ($modalKey === 'modal-task-create')
                <x-slot name="header">
                    Nova Tarefa
                </x-slot>

                <form wire:submit.prevent="storeTask" class="space-y-4">
                    @include('livewire.task._partials.task-form', ['showActions' => false])

                    <div class="flex justify-end gap-2 pt-2">
                        <x-button text="Cancelar" variant="gray_outline" wire:click="cancelCreateTask" type="button" />
                        <x-button type="button" text="Salvar Tarefa" icon="fa-solid fa-check" wire:click="storeTask" />
                    </div>
                </form>
            @elseif ($modalKey === 'modal-task-category-create')
                <x-slot name="header">
                    Nova Categoria do Ambiente
                </x-slot>

                <form wire:submit.prevent="storeTaskCategory" class="space-y-4">
                    <div>
                        <x-form.label value="Título" for="taskHubCategoryTitle" />
                        <x-form.input
                            id="taskHubCategoryTitle"
                            name="taskHubCategoryTitle"
                            wire:model.defer="taskHubCategoryTitle"
                            placeholder="Ex.: Demandas operacionais"
                        />
                        <x-form.error for="taskHubCategoryTitle" />
                    </div>

                    <div>
                        <x-form.label value="Descrição" for="taskHubCategoryDescription" />
                        <x-form.textarea
                            id="taskHubCategoryDescription"
                            name="taskHubCategoryDescription"
                            wire:model.defer="taskHubCategoryDescription"
                            rows="3"
                            placeholder="Descreva quando esta categoria deve ser usada"
                        />
                        <x-form.error for="taskHubCategoryDescription" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" type="button" />
                        <x-button type="button" text="Salvar Categoria" icon="fa-solid fa-check" wire:click="storeTaskCategory" />
                    </div>
                </form>
            @elseif ($modalKey === 'modal-task-category-edit')
                <x-slot name="header">
                    Editar Categoria do Ambiente
                </x-slot>

                <form wire:submit.prevent="updateTaskCategory" class="space-y-4">
                    <div>
                        <x-form.label value="Título" for="taskHubCategoryTitle" />
                        <x-form.input
                            id="taskHubCategoryTitle"
                            name="taskHubCategoryTitle"
                            wire:model.defer="taskHubCategoryTitle"
                            placeholder="Ex.: Demandas operacionais"
                        />
                        <x-form.error for="taskHubCategoryTitle" />
                    </div>

                    <div>
                        <x-form.label value="Descrição" for="taskHubCategoryDescription" />
                        <x-form.textarea
                            id="taskHubCategoryDescription"
                            name="taskHubCategoryDescription"
                            wire:model.defer="taskHubCategoryDescription"
                            rows="3"
                            placeholder="Descreva quando esta categoria deve ser usada"
                        />
                        <x-form.error for="taskHubCategoryDescription" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" type="button" />
                        <x-button type="button" text="Atualizar Categoria" icon="fa-solid fa-check" wire:click="updateTaskCategory" />
                    </div>
                </form>
            @elseif ($modalKey === 'modal-task-member-share')
                <x-slot name="header">
                    Compartilhar com Usuário
                </x-slot>

                <form wire:submit.prevent="addMember" class="space-y-4">
                    <div>
                        <x-form.label value="Usuário" for="member_user_id" />
                        <x-form.select-livewire
                            id="member_user_id"
                            name="member_user_id"
                            wire:model.defer="member_user_id"
                            :collection="$availableMemberUsers"
                            valueField="id"
                            labelField="name"
                            placeholder="Selecione um usuário"
                            borderColor="gray"
                        />
                        <x-form.error for="member_user_id" />
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-gray-600">
                        Esse vínculo concede acesso direto ao ambiente para a pessoa selecionada, sem depender do setor dela.
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" type="button" />
                        <x-button type="button" text="Adicionar Usuário" icon="fa-solid fa-user-plus" wire:click="addMember" />
                    </div>
                </form>
            @elseif ($modalKey === 'modal-task-organization-share')
                <x-slot name="header">
                    Compartilhar com Setor
                </x-slot>

                <form wire:submit.prevent="addOrganizationAccess" class="space-y-4">
                    <div>
                        <x-form.label value="Setor" for="member_organization_id" />
                        <x-form.select-livewire
                            id="member_organization_id"
                            name="member_organization_id"
                            wire:model.defer="member_organization_id"
                            :collection="$availableOrganizations"
                            valueField="id"
                            labelField="title"
                            labelAcronym="acronym"
                            placeholder="Selecione um setor"
                            borderColor="gray"
                        />
                        <x-form.error for="member_organization_id" />
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-gray-600">
                        Esse vínculo libera o ambiente para todos os usuários associados ao setor selecionado.
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" type="button" />
                        <x-button type="button" text="Adicionar Setor" icon="fa-solid fa-users" wire:click="addOrganizationAccess" />
                    </div>
                </form>
            @elseif ($modalKey === 'modal-step-completion-move')
                <x-slot name="header">
                    {{
                        $pendingStepMoveReasonType === 'reopen'
                            ? 'Reabrir Etapa'
                            : ($pendingStepMoveReasonType === 'cancellation' ? 'Cancelar Etapa' : 'Concluir Etapa')
                    }}
                </x-slot>

                <div class="space-y-4">
                    <p class="text-xs text-gray-600">
                        @if ($pendingStepMoveReasonType === 'reopen')
                            Informe o motivo que deve ser registrado na atividade da tarefa para reabrir esta etapa.
                        @elseif ($pendingStepMoveReasonType === 'cancellation')
                            Informe o motivo que deve ser registrado na atividade da tarefa para cancelar esta etapa.
                        @else
                            Informe o texto que deve ser registrado na atividade da tarefa para concluir esta etapa.
                        @endif
                    </p>

                    <x-form.textarea
                        wire:model.defer="stepCompletionComment"
                        placeholder="{{
                            $pendingStepMoveReasonType === 'reopen'
                                ? 'Descreva o motivo da reabertura...'
                                : ($pendingStepMoveReasonType === 'cancellation'
                                    ? 'Descreva o motivo do cancelamento...'
                                    : 'Descreva a conclusão da etapa...')
                        }}"
                    />

                    @error('stepCompletionComment')
                        <p class="text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end gap-2 pt-2">
                        <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" type="button" />
                        <x-button
                            type="button"
                            :text="$pendingStepMoveReasonType === 'reopen'
                                ? 'Reabrir Etapa'
                                : ($pendingStepMoveReasonType === 'cancellation' ? 'Cancelar Etapa' : 'Concluir Etapa')"
                            :icon="$pendingStepMoveReasonType === 'reopen'
                                ? 'fa-solid fa-rotate-left'
                                : ($pendingStepMoveReasonType === 'cancellation' ? 'fa-solid fa-ban' : 'fa-solid fa-check')"
                            wire:click="confirmStepCompletionMove"
                        />
                    </div>
                </div>
            @endif
        </x-modal>
    </div>
</div>
