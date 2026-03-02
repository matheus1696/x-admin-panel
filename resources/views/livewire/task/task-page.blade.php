<div>
    <x-alert.flash />

    <x-page.header title="Cronograma de Atividades" subtitle="Visualize o resumo e a lista de tarefas" icon="fas fa-list-check" >
        <x-slot name="button">
            <x-button text="Nova Tarefa" icon="fas fa-plus" wire:click="enableCreateTask" />
        </x-slot>
    </x-page.header>

    <div x-data="{
        openAsideTask: false,
        openAsideStep: false,
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
    }">

        <!-- Botões Superiores -->
        <div class="mb-6 flex items-center rounded-xl border border-gray-200 overflow-hidden">
            <button type="button" class="relative px-6 py-2.5 text-sm font-medium transition-all duration-200" :class="tab === 'dashboard' ? 'bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" @click="tab = 'dashboard'">
                <span class="flex items-center gap-2">
                    <i class="fa-regular fa-chart-bar" :class="tab === 'dashboard' ? 'text-white' : 'text-gray-400'"></i>
                    <span>Dashboard</span>
                </span>
            </button>

            <button type="button" class="relative px-6 py-2.5 text-sm font-medium transition-all duration-200" :class="tab === 'list' ? 'bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" @click="tab = 'list'">
                <span class="flex items-center gap-2">
                    <i class="fa-regular fa-list-alt" :class="tab === 'list' ? 'text-white' : 'text-gray-400'"></i>
                    <span>Lista</span>
                </span>
            </button>

            <button type="button" class="relative px-6 py-2.5 text-sm font-medium transition-all duration-200" :class="tab === 'step-kanban' ? 'bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" @click="tab = 'step-kanban'">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-grip" :class="tab === 'step-kanban' ? 'text-white' : 'text-gray-400'"></i>
                    <span>Kanban Etapas</span>
                </span>
            </button>

            <button type="button" class="relative px-6 py-2.5 text-sm font-medium transition-all duration-200" :class="tab === 'sharing' ? 'bg-gradient-to-r from-slate-700 via-slate-800 to-slate-900 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" @click="tab = 'sharing'">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-users" :class="tab === 'sharing' ? 'text-white' : 'text-gray-400'"></i>
                    <span>Compartilhamento</span>
                </span>
            </button>
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
                                <p class="text-sm font-semibold uppercase text-white">Painel de Tarefas</p>
                                <p class="mt-2 text-sm text-white/80">Distribuição ativa, responsáveis e atrasos</p>
                            </div>
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                                {{ $dashboard['tasks_active_total'] ?? 0 }} ativas
                            </span>
                        </div>
                    </div>

                    <div class="space-y-6 p-6">
                        <div class="grid grid-cols-2 gap-6 md:grid-cols-3">

                            <div class="flex flex-col items-center justify-center gap-4">
                                <div class="relative h-44 w-44 rounded-full" style="{{ $taskStatusChartStyle }}">
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
                                        <h4 class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">Status</h4>
                                        <span class="text-[11px] text-gray-400 hidden md:block">Tarefas ativas</span>
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
                                            <div class="flex items-center justify-between gap-3 text-sm">
                                                <span class="truncate text-gray-600">{{ $item['label'] }}</span>
                                                <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                                            </div>
                                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                                <div class="h-full rounded-full bg-gradient-to-r {{ $taskBarColor }}"
                                                     style="width: {{ max(10, $taskStatusTotal > 0 ? (int) round(($item['total'] / $taskStatusTotal) * 100) : 0) }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-sm text-gray-400">Nenhuma tarefa ativa no momento.</div>
                                    @endforelse
                                </div>
                            </div>                          

                            <div class="col-span-3 space-y-3 border-y border-emerald-800/20 py-6">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">Responsáveis</h4>
                                    <span class="text-[11px] text-gray-400">Por tarefa</span>
                                </div>

                                @forelse (($dashboard['tasks_by_responsible'] ?? []) as $item)
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between gap-3 text-sm">
                                            <span class="truncate text-gray-600">{{ $item['label'] }}</span>
                                            <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                                        </div>
                                        <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-600 to-emerald-800"
                                                    style="width: {{ max(10, (int) round(($item['total'] / $taskResponsibleMax) * 100)) }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-sm text-gray-400">Nenhum responsável vinculado.</div>
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
                                    <button type="button" wire:click="openAsideTask({{ $item['id'] }})" @click="openAsideStep = false; openAsideTask = true" class="w-full rounded-2xl border border-rose-100 bg-white px-4 py-3 text-left transition hover:border-rose-200 hover:bg-rose-50/40">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-gray-900">
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
                                    <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50 px-4 py-4 text-center text-sm text-emerald-700">Nenhuma tarefa atrasada.</div>
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
                                <p class="mt-2 text-sm text-white/80">Status, responsáveis, setores e pendências</p>
                            </div>
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                                {{ $dashboard['steps_active_total'] ?? 0 }} ativas
                            </span>
                        </div>
                    </div>

                    <div class="space-y-6 p-6">

                        <div class="grid grid-cols-2 gap-6 md:grid-cols-3">

                            <div class="flex flex-col items-center justify-center gap-4">
                                <div class="relative h-44 w-44 rounded-full" style="{{ $stepStatusChartStyle }}">
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
                                        <h4 class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">Status</h4>
                                        <span class="text-[11px] text-gray-400 hidden md:block">Etapas ativas</span>
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
                                            <div class="flex items-center justify-between gap-3 text-sm">
                                                <span class="truncate text-gray-600">{{ $item['label'] }}</span>
                                                <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                                            </div>
                                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                                <div class="h-full rounded-full bg-gradient-to-r {{ $stepBarColor }}"
                                                     style="width: {{ max(10, $stepStatusTotal > 0 ? (int) round(($item['total'] / $stepStatusTotal) * 100) : 0) }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-sm text-gray-400">Nenhuma etapa ativa no momento.</div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-span-3 grid grid-cols-1 gap-10 lg:grid-cols-2 border-y border-gray-300/80 py-6">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">Responsáveis</h4>
                                        <span class="text-[11px] text-gray-400">Por etapa</span>
                                    </div>

                                    @forelse (($dashboard['steps_by_responsible'] ?? []) as $item)
                                        <div class="space-y-1.5">
                                            <div class="flex items-center justify-between gap-3 text-sm">
                                                <span class="truncate text-gray-600">{{ $item['label'] }}</span>
                                                <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                                            </div>
                                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                                <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-orange-700"
                                                        style="width: {{ max(10, (int) round(($item['total'] / $stepResponsibleMax) * 100)) }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-sm text-gray-400">Nenhum responsável vinculado.</div>
                                    @endforelse
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">Setores</h4>
                                        <span class="text-[11px] text-gray-400">Por etapa</span>
                                    </div>

                                    @forelse (($dashboard['steps_by_organization'] ?? []) as $item)
                                        <div class="space-y-1.5">
                                            <div class="flex items-center justify-between gap-3 text-sm">
                                                <span class="truncate text-gray-600">{{ $item['label'] }}</span>
                                                <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                                            </div>
                                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                                <div class="h-full rounded-full bg-gradient-to-r from-slate-500 to-slate-700"
                                                        style="width: {{ max(10, (int) round(($item['total'] / $organizationMax) * 100)) }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4 text-center text-sm text-gray-400">Nenhum setor vinculado.</div>
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
                                    <button type="button" wire:click="openAsideTaskStep({{ $item['id'] }})" @click="openAsideTask = false; openAsideStep = true" class="w-full rounded-2xl border border-rose-100 bg-white px-4 py-3 text-left transition hover:border-rose-200 hover:bg-rose-50/40">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-gray-900">
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
                                    <div class="rounded-2xl border border-dashed border-amber-200 bg-amber-50 px-4 py-4 text-center text-sm text-amber-700">Nenhuma etapa atrasada.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div x-show="tab === 'list'" x-cloak>
            <div class="overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-sm">
                <!-- Cabeçalho da Tabela -->
                <div class="border-b border-emerald-100 bg-gradient-to-r from-emerald-700 via-emerald-800 to-emerald-800 px-6 py-4">
                    <div class="grid grid-cols-5 gap-4 text-xs font-medium uppercase text-white/80 md:grid-cols-12">
                        <div class="col-span-5 md:col-span-4">Título da tarefa</div>
                        <div class="hidden text-center md:col-span-2 md:block">Responsável</div>
                        <div class="hidden md:col-span-4 md:grid md:grid-cols-3 md:gap-2">
                            <div class="text-center">Categoria</div>
                            <div class="text-center">Prioridade</div>
                            <div class="text-center">Status</div>
                        </div>
                        <div class="hidden text-center md:col-span-1 md:block">Início</div>
                        <div class="hidden text-center md:col-span-1 md:block">Prazo</div>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse ($tasks as $task)
                        <div class="{{ $task->finished_at ? 'bg-emerald-50/40' : 'hover:bg-emerald-50/20' }} transition-colors duration-200">
                            <div class="grid grid-cols-5 items-center gap-4 md:grid-cols-12 py-2 px-3">
                                <div class="col-span-5 min-w-0 md:col-span-4">
                                    <div class="flex justify-start items-center gap-1 line-clamp-1">
                                        <div>
                                            <x-button type="button" variant="green_text" @click.stop="expandedTaskId = expandedTaskId === {{ $task->id }} ? null : {{ $task->id }}" title="{{ $task->taskSteps->count() ? 'Mostrar etapas' : 'Sem etapas' }}" class="flex items-center rounded-lg">
                                                <i class="fas fa-chevron-right text-[10px] transition-transform duration-200" :class="expandedTaskId === {{ $task->id }} ? 'rotate-90' : ''"></i>
                                            </x-button>
                                        </div>

                                        <p wire:click="openAsideTask({{ $task->id }})" @click="openAsideStep = false; openAsideTask = true" class="flex-1 line-clamp-1">
                                            <span class="text-xs font-mono font-medium text-emerald-700 hover:underline">{{ $task->code }}</span>
                                            <i class="fas fa-external-link-alt text-[8px] text-gray-400"></i>
                                            <span class="mx-1 text-gray-300">-</span>
                                            <span class="truncate text-xs text-gray-700" title="{{ $task->title }}">{{ $task->title }}</span>
                                        </p>

                                        <x-button type="button" variant="green_text" @click.stop="expandedTaskId = {{ $task->id }}; stepFormTaskId = stepFormTaskId === {{ $task->id }} ? null : {{ $task->id }}" title="Nova etapa" class="flex items-center rounded-lg">
                                            <i class="fas fa-plus text-[10px]"></i>
                                        </x-button>
                                    </div>
                                </div>

                                <div class="hidden px-2 text-center md:col-span-2 md:block">
                                    <span class="block truncate text-xs text-gray-600">{{ $task->user?->name ?? '-' }}</span>
                                </div>

                                <div class="hidden items-center gap-2 px-2 text-xs md:col-span-4 md:grid md:grid-cols-3">
                                    <div class="truncate text-center text-gray-600">{{ $task->taskCategory?->title ?? '-' }}</div>
                                    <div class="text-center">
                                        @if ($task->taskPriority)
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $task->taskPriority->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">{{ $task->taskPriority->title }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                    <div class="text-center">
                                        @if ($task->taskStatus)
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $task->taskStatus->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">{{ $task->taskStatus->title }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="hidden text-center md:col-span-1 md:block">
                                    <span class="text-xs text-gray-600">{{ $task->started_at?->format('d/m') ?? '-' }}</span>
                                </div>

                                <div class="hidden text-center md:col-span-1 md:block">
                                    @if ($task->deadline_at)
                                        <span class="text-xs {{ $task->deadline_at->isPast() && ! $task->finished_at ? 'font-medium text-red-600' : 'text-gray-600' }}">{{ $task->deadline_at->format('d/m') }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>

                            <div x-show="expandedTaskId === {{ $task->id }}" x-cloak class="border-y border-emerald-100 bg-amber-50/40">
                                <div class="grid grid-cols-5 gap-4 text-[10px] font-medium uppercase tracking-wider border-y border-amber-800/80 text-amber-800/80 md:grid-cols-12 px-4 py-3">
                                    <div class="col-span-5 md:col-span-4">Título da etapa</div>
                                    <div class="hidden md:col-span-3 md:grid md:grid-cols-2 md:gap-2">
                                        <div class="text-center">Setor</div>
                                        <div class="text-center">Responsável</div>
                                    </div>
                                    <div class="hidden md:col-span-3 md:grid md:grid-cols-2 md:gap-2">
                                        <div class="text-center">Prioridade</div>
                                        <div class="text-center">Status</div>
                                    </div>
                                    <div class="hidden text-center md:col-span-1 md:block">Início</div>
                                    <div class="hidden text-center md:col-span-1 md:block">Prazo</div>
                                </div>

                                <div x-show="stepFormTaskId === {{ $task->id }}" x-cloak class="mt-2 px-3">
                                    @include('livewire.task._partials.task-step-form', ['taskId' => $task->id])
                                </div>

                                <div class="mt-1 space-y-0.5 divide-y divide-amber-300/80">
                                    @forelse ($task->taskSteps as $step)
                                        <div class="grid grid-cols-5 gap-4 px-3 py-1.5 md:grid-cols-12">
                                            <div class="col-span-5 min-w-0 md:col-span-4">
                                                <x-button type="button" variant="yellow_text" wire:click="openAsideTaskStep({{ $step->id }})" @click="openAsideTask = false; openAsideStep = true">
                                                    <span class="text-[11px] font-mono font-medium text-amber-700">{{ $step->code }}</span>
                                                    <span class="text-gray-300">-</span>
                                                    <span class="truncate text-xs font-medium text-gray-800" title="{{ $step->title }}">{{ $step->title }}</span>
                                                </x-button>
                                            </div>

                                            <div class="hidden text-[11px] text-gray-500 md:col-span-3 md:grid md:grid-cols-2 md:gap-2">
                                                <div class="truncate text-center">{{ $step->organization?->acronym ?? $step->organization?->title ?? '-' }}</div>
                                                <div class="truncate text-center">{{ $step->user?->name ?? '-' }}</div>
                                            </div>

                                            <div class="hidden md:col-span-3 md:grid md:grid-cols-2 md:gap-2">
                                                <div class="text-center">
                                                    @if ($step->taskPriority)
                                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-medium {{ $step->taskPriority->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">{{ $step->taskPriority->title }}</span>
                                                    @else
                                                        <span class="text-[11px] text-gray-400">-</span>
                                                    @endif
                                                </div>
                                                <div class="text-center">
                                                    @if ($step->taskStepStatus)
                                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-medium {{ $step->taskStepStatus->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">{{ $step->taskStepStatus->title }}</span>
                                                    @else
                                                        <span class="text-[11px] text-gray-400">-</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="hidden text-center md:col-span-1 md:block">
                                                <span class="text-[11px] text-gray-500">{{ $step->started_at?->format('d/m') ?? '-' }}</span>
                                            </div>

                                            <div class="hidden text-center md:col-span-1 md:block">
                                                @if ($step->deadline_at)
                                                    <span class="text-[11px] {{ $step->deadline_at->isPast() && ! $step->finished_at ? 'font-medium text-red-600' : 'text-gray-500' }}">{{ $step->deadline_at->format('d/m') }}</span>
                                                @else
                                                    <span class="text-[11px] text-gray-400">-</span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-xl border border-dashed border-amber-200 bg-white/70 px-4 py-4 text-center text-sm text-gray-400">Esta tarefa ainda não possui etapas.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-gray-400">Nenhuma tarefa encontrada.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div x-show="tab === 'step-kanban'" x-cloak>
            <div class="overflow-hidden rounded-3xl border border-amber-200 bg-white shadow-sm">
                <div class="border-b border-amber-100 bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 px-6 py-4 text-white">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase">Kanban de Etapas</p>
                            <p class="mt-1 text-xs text-white/80 line-clamp-1">Movimente etapas entre colunas sem sair do cronograma</p>
                        </div>
                        <span class="rounded-full border border-white/20 bg-white/10 py-1 text-xs font-semibold w-24 text-center">
                            {{ collect($stepKanban ?? [])->flatMap(fn ($column) => $column['steps'])->count() }} etapas
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto px-4 py-5">
                    <div class="grid min-w-[980px] grid-flow-col auto-cols-[minmax(260px,1fr)] gap-4">
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
                            <section class="flex h-full flex-col rounded-3xl border {{ $columnTheme['border'] }} bg-gradient-to-b {{ $columnTheme['surface'] }}">
                                <header class="border-b border-amber-100 px-4 py-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $columnTheme['header'] }}">
                                                {{ $column['title'] }}
                                            </span>
                                        </div>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold shadow-sm {{ $columnTheme['count'] }}">
                                            {{ $column['steps']->count() }}
                                        </span>
                                    </div>
                                </header>

                                <div class="flex-1 space-y-1 py-1 px-2"
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
                                     :class="dragOverStatusId === {{ (int) $column['status_id'] }} ? '{{ $columnTheme['drop'] }}' : ''">
                                    @forelse ($column['steps'] as $step)
                                        <div class="overflow-hidden rounded-xl transition-all duration-150"
                                             @dragover.prevent="
                                                dragOverStatusId = {{ (int) $column['status_id'] }};
                                                dragOverInsertBeforeId = {{ $step->id }};
                                             "
                                             @drop.prevent="dropStepOnColumn({{ (int) $column['status_id'] }}, @js($columnStepIds), {{ $step->id }})"></div>
                                        <div class="overflow-hidden rounded-xl transition-all duration-150"
                                             :class="draggedStepId !== null && dragOverStatusId === {{ (int) $column['status_id'] }} && dragOverInsertBeforeId === {{ $step->id }} ? 'max-h-14 opacity-100 mb-2' : 'max-h-0 opacity-0'">
                                            <div class="flex items-center gap-2 rounded-xl border-2 border-dashed px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.2em] {{ $columnTheme['placeholder'] }}">
                                                <i class="fa-solid fa-down-long"></i>
                                                <span>Solte aqui</span>
                                            </div>
                                        </div>
                                        <article class="rounded-2xl border {{ $columnTheme['card'] }} bg-white p-4 shadow-sm transition hover:shadow-md cursor-grab active:cursor-grabbing"
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
                                                <button type="button" wire:click="openAsideTaskStep({{ $step->id }})" @click="openAsideTask = false; openAsideStep = true" class="min-w-0 text-left">
                                                    <p class="truncate text-xs font-semibold text-gray-900">
                                                        <span class="font-mono {{ $columnTheme['code'] }}">{{ $step->code }}</span>
                                                        <span class="text-gray-300">-</span>
                                                        <span>{{ $step->title }}</span>
                                                    </p>
                                                    <p class="mt-1 truncate text-[11px] text-gray-500">
                                                        {{ $step->task?->code ? $step->task->code.' - ' : '' }} {{ $step->task?->title ? $step->task->title.' - ' : '' }}
                                                    </p>
                                                </button>

                                                @if ($step->taskPriority)
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-medium {{ $step->taskPriority->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">
                                                        {{ $step->taskPriority->title }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-0.5 text-[11px] text-gray-500">                                                
                                                <div class="flex items-center gap-1">
                                                    <span class="text-[10px] font-semibold uppercase text-gray-400">Setor: </span>
                                                    <span class="min-w-0 flex-1 truncate font-medium text-gray-700">{{ $step->organization?->acronym ?? $step->organization?->title ?? 'Sem setor' }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-[10px] font-semibold uppercase text-gray-400">Responsável: </span>
                                                    <span class="min-w-0 flex-1 truncate font-medium text-gray-700">{{ $step->user?->name ?? 'Sem responsável' }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-[10px] font-semibold uppercase text-gray-400">Prazo: </span>
                                                    <span class="min-w-0 flex-1 truncate font-medium {{ $step->deadline_at && $step->deadline_at->isPast() && ! $step->finished_at ? 'text-rose-700' : 'text-gray-700' }}">
                                                        {{ $step->deadline_at?->format('d/m/Y') ?? 'Sem prazo' }}
                                                    </span>
                                                </div>
                                            </div>

                                        </article>
                                    @empty
                                        <div class="flex min-h-40 items-center justify-center rounded-2xl border border-dashed border-amber-200 bg-white/70 px-4 py-6 text-center text-sm text-gray-400">
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
                            <div class="rounded-3xl border border-dashed border-gray-200 bg-gray-50 px-6 py-10 text-center text-sm text-gray-400">
                                Nenhuma etapa encontrada para montar o kanban.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'sharing'" x-cloak>
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-gradient-to-r from-slate-700 via-slate-800 to-slate-900 px-6 py-4 text-white">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase">Compartilhamento do Ambiente</p>
                            <p class="mt-1 text-xs text-white/80">Gerencie quem pode acessar este ambiente de projeto</p>
                        </div>
                        <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold">
                            {{ $members->count() }} membros
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-[360px_1fr]">
                    <section class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Adicionar Membro</h3>
                            <p class="mt-1 text-xs text-gray-500">
                                @if ($canManageMembers)
                                    O proprietário do ambiente pode incluir novos participantes.
                                @else
                                    Apenas o proprietário do ambiente pode adicionar ou remover membros.
                                @endif
                            </p>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">
                                Usuário
                            </label>

                            <x-form.select-livewire
                                wire:model.defer="member_user_id"
                                name="member_user_id"
                                :collection="$availableMemberUsers"
                                valueField="id"
                                labelField="name"
                                placeholder="Selecione um usuário"
                                borderColor="gray"
                                :disabled="! $canManageMembers"
                            />

                            @error('member_user_id')
                                <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror

                            <x-button
                                type="button"
                                text="Adicionar ao Ambiente"
                                icon="fa-solid fa-user-plus"
                                wire:click="addMember"
                                class="w-full justify-center"
                                @disabled(! $canManageMembers)
                            />
                        </div>
                    </section>

                    <section class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Membros Atuais</h3>
                                <p class="mt-1 text-xs text-gray-500">Pessoas que podem acessar este ambiente.</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @forelse ($members as $member)
                                <div class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-4 md:flex-row md:items-center md:justify-between">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-gray-900">{{ $member->user?->name ?? 'Usuário' }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $member->user?->email ?? 'Sem e-mail' }}</p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        @if ((int) $member->user_id === $taskHubOwnerId)
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-emerald-700">
                                                Proprietário
                                            </span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-700">
                                                Membro
                                            </span>
                                        @endif

                                        @if ($canManageMembers && (int) $member->user_id !== $taskHubOwnerId)
                                            <x-button
                                                type="button"
                                                text="Remover"
                                                icon="fa-solid fa-user-minus"
                                                variant="gray_outline"
                                                wire:click="removeMember({{ $member->id }})"
                                            />
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-10 text-center text-sm text-gray-400">
                                    Nenhum membro vinculado a este ambiente.
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <div>
            <div x-show="openAsideTask" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" wire:click="closedAsideTask()" @click="openAsideTask = false" class="fixed inset-0 z-30 bg-black/50"></div>

            <div x-show="openAsideTask" x-transition:enter="transform transition duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed top-0 right-0 z-40 h-screen w-full overflow-hidden border-l border-gray-200 bg-white shadow-xl md:w-3/5">
                @if ($selectedTaskId)
                    <livewire:task.task-aside :taskId="$selectedTaskId" :key="'aside-task-' . $selectedTaskId" />
                @endif
            </div>

            <div x-show="openAsideStep" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" wire:click="closedAsideTaskStep()" @click="openAsideStep = false" class="fixed inset-0 z-30 bg-black/50"></div>

            <div x-show="openAsideStep" x-transition:enter="transform transition duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed top-0 right-0 z-40 h-screen w-full overflow-hidden border-l border-gray-200 bg-white shadow-xl md:w-3/5">
                @if ($selectedStepId)
                    <livewire:task.task-step-aside :stepId="$selectedStepId" :key="'aside-step-' . $selectedStepId" />
                @endif
            </div>
        </div>

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
            @elseif ($modalKey === 'modal-step-completion-move')
                <x-slot name="header">
                    {{
                        $pendingStepMoveReasonType === 'reopen'
                            ? 'Reabrir Etapa'
                            : ($pendingStepMoveReasonType === 'cancellation' ? 'Cancelar Etapa' : 'Concluir Etapa')
                    }}
                </x-slot>

                <div class="space-y-4">
                    <p class="text-sm text-gray-600">
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
                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
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
