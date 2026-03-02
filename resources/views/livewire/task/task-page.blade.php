<div>
    <x-alert.flash />

    <x-page.header title="Cronograma de Atividades" subtitle="Visualize o resumo e a lista de tarefas" icon="fas fa-list-check" >
        <x-slot name="button">
            <x-button text="Nova Tarefa" icon="fas fa-plus" wire:click="enableCreateTask" />
        </x-slot>
    </x-page.header>

    <div x-data="{ openAsideTask: false, openAsideStep: false, tab: 'dashboard', expandedTaskId: null, stepFormTaskId: null }">

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
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

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
                                        <span class="text-[11px] text-gray-400">Tarefas ativas</span>
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

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

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
                                        <span class="text-[11px] text-gray-400">Etapas ativas</span>
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
                                    <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50 px-4 py-4 text-center text-sm text-emerald-700">Nenhuma etapa atrasada.</div>
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
            @endif
        </x-modal>
    </div>
</div>
