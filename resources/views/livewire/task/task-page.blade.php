<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header Padronizado -->
    <x-page.header title="Cronograma de Atividades" subtitle="Visualize todas as atividades e os andamentos"
        icon="fas fa-list-check">
        <x-slot name="button">
            <x-button text="Nova Tarefa" icon="fas fa-plus" wire:click="enableCreateTask" />
        </x-slot>
    </x-page.header>

    <!-- Card Principal -->
    <div x-data="{ openAsideTask: false, tab: 'dashboard' }">
        <div class="bg-white border border-gray-200 rounded-xl p-2 mb-4">
            <div class="flex items-center gap-2">
                <button type="button" class="px-4 py-2 text-xs font-medium rounded-lg transition-colors"
                    :class="tab === 'dashboard' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    @click="tab = 'dashboard'">
                    Dashboard
                </button>
                <button type="button" class="px-4 py-2 text-xs font-medium rounded-lg transition-colors"
                    :class="tab === 'list' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    @click="tab = 'list'">
                    Lista
                </button>
                <button type="button" class="px-4 py-2 text-xs font-medium rounded-lg transition-colors"
                    :class="tab === 'kanban' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    @click="tab = 'kanban'">
                    Kanban
                </button>
                <button type="button" class="px-4 py-2 text-xs font-medium rounded-lg transition-colors"
                    :class="tab === 'steps' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    @click="tab = 'steps'">
                    Etapas
                </button>
                <button type="button" class="px-4 py-2 text-xs font-medium rounded-lg transition-colors"
                    :class="tab === 'members' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    @click="tab = 'members'">
                    Membros
                </button>
            </div>
        </div>

        <div x-show="tab === 'dashboard'" x-cloak class="space-y-8">
            @php
                $activeStatusMap = collect($dashboard['tasks_by_status_active'] ?? [])->mapWithKeys(
                    fn($item) => [mb_strtolower((string) ($item['label'] ?? '')) => (int) ($item['total'] ?? 0)],
                );
                $draftCount = (int) ($activeStatusMap['rascunho'] ?? 0);
                $pausedCount = (int) ($activeStatusMap['pausado'] ?? 0);
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <p class="text-3xl font-semibold text-gray-900">{{ $dashboard['total'] }}</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mt-2">Total de tarefas</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <p class="text-3xl font-semibold text-slate-600">{{ $draftCount }}</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mt-2">Rascunho</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <p class="text-3xl font-semibold text-blue-600">{{ $dashboard['in_progress'] }}</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mt-2">Em andamento</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <p class="text-3xl font-semibold text-amber-600">{{ $pausedCount }}</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mt-2">Pausado</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <p class="text-3xl font-semibold text-emerald-600">{{ $dashboard['completed'] }}</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mt-2">Concluídas</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <p class="text-3xl font-semibold text-amber-600">{{ $dashboard['cancelled'] }}</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mt-2">Canceladas</p>
                </div>
            </div>

            @php
                $taskColors = [
                    'gray' => '#9CA3AF',
                    'blue' => '#3B82F6',
                    'green' => '#10B981',
                    'yellow' => '#F59E0B',
                    'red' => '#EF4444',
                    'purple' => '#8B5CF6',
                ];
                $circle = 100;

                $taskActiveItems = collect($dashboard['tasks_by_status_active'] ?? [])
                    ->sortByDesc('total')
                    ->values();
                $taskTop = $taskActiveItems->take(5);
                $taskOthersTotal = $taskActiveItems->slice(5)->sum('total');
                if ($taskOthersTotal > 0) {
                    $taskTop = $taskTop->push(['label' => 'Demais', 'total' => $taskOthersTotal, 'color' => null]);
                }
                $taskTotal = (int) $taskTop->sum('total');

                $stepActiveItems = collect($dashboard['steps_by_status_active'] ?? [])
                    ->sortByDesc('total')
                    ->values();
                $stepTop = $stepActiveItems->take(5);
                $stepOthersTotal = $stepActiveItems->slice(5)->sum('total');
                if ($stepOthersTotal > 0) {
                    $stepTop = $stepTop->push(['label' => 'Demais', 'total' => $stepOthersTotal, 'color' => null]);
                }
                $stepTotal = (int) $stepTop->sum('total');
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Distribuição de status
                            das tarefas ativas</p>
                        <span class="text-[11px] text-gray-400">Somente ativas</span>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="relative size-44">
                            <svg viewBox="0 0 42 42" class="size-44">
                                <circle cx="21" cy="21" r="15.9155" fill="transparent" stroke="#F3F4F6"
                                    stroke-width="6"></circle>
                                @php
                                    $taskOffset = 25;
                                @endphp
                                @foreach ($taskTop as $item)
                                    @php
                                        $value = (int) $item['total'];
                                        $percent = $taskTotal > 0 ? ($value / $taskTotal) * $circle : 0;
                                        $stroke = $taskColors[$item['color'] ?? ''] ?? '#6B7280';
                                    @endphp
                                    @if ($percent > 0)
                                        <circle cx="21" cy="21" r="15.9155" fill="transparent"
                                            stroke="{{ $stroke }}" stroke-width="6"
                                            stroke-dasharray="{{ $percent }} {{ $circle - $percent }}"
                                            stroke-dashoffset="{{ $taskOffset }}"></circle>
                                        @php
                                            $taskOffset -= $percent;
                                        @endphp
                                    @endif
                                @endforeach
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-xl font-semibold text-gray-900">{{ $taskTotal }}</span>
                                <span class="text-[11px] text-gray-500">Tarefas ativas</span>
                            </div>
                        </div>
                        <div class="flex-1 space-y-2">
                            @forelse ($taskTop as $item)
                                <div class="flex items-center justify-between text-xs text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-2 w-2 rounded-full"
                                            style="background-color: {{ $taskColors[$item['color'] ?? ''] ?? '#6B7280' }}"></span>
                                        <span class="truncate">{{ $item['label'] }}</span>
                                    </div>
                                    <span class="font-medium text-gray-800">{{ $item['total'] }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400">Sem registros</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Distribuição de status
                            das etapas ativas</p>
                        <span class="text-[11px] text-gray-400">Somente ativas</span>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="relative size-44">
                            <svg viewBox="0 0 42 42" class="size-44">
                                <circle cx="21" cy="21" r="15.9155" fill="transparent" stroke="#F3F4F6"
                                    stroke-width="6"></circle>
                                @php
                                    $stepOffset = 25;
                                @endphp
                                @foreach ($stepTop as $item)
                                    @php
                                        $value = (int) $item['total'];
                                        $percent = $stepTotal > 0 ? ($value / $stepTotal) * $circle : 0;
                                        $stroke = $taskColors[$item['color'] ?? ''] ?? '#6B7280';
                                    @endphp
                                    @if ($percent > 0)
                                        <circle cx="21" cy="21" r="15.9155" fill="transparent"
                                            stroke="{{ $stroke }}" stroke-width="6"
                                            stroke-dasharray="{{ $percent }} {{ $circle - $percent }}"
                                            stroke-dashoffset="{{ $stepOffset }}"></circle>
                                        @php
                                            $stepOffset -= $percent;
                                        @endphp
                                    @endif
                                @endforeach
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-xl font-semibold text-gray-900">{{ $stepTotal }}</span>
                                <span class="text-[11px] text-gray-500">Etapas ativas</span>
                            </div>
                        </div>
                        <div class="flex-1 space-y-2">
                            @forelse ($stepTop as $item)
                                <div class="flex items-center justify-between text-xs text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-2 w-2 rounded-full"
                                            style="background-color: {{ $taskColors[$item['color'] ?? ''] ?? '#6B7280' }}"></span>
                                        <span class="truncate">{{ $item['label'] }}</span>
                                    </div>
                                    <span class="font-medium text-gray-800">{{ $item['total'] }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400">Sem registros</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            @php
                $taskAssigneeItems = collect($dashboard['tasks_by_responsible'] ?? [])
                    ->sortByDesc('total')
                    ->values();
                $taskAssigneeTop = $taskAssigneeItems->take(10);
                $taskAssigneeOthersTotal = $taskAssigneeItems->slice(10)->sum('total');
                if ($taskAssigneeOthersTotal > 0) {
                    $taskAssigneeTop = $taskAssigneeTop->push([
                        'label' => 'Demais',
                        'total' => $taskAssigneeOthersTotal,
                    ]);
                }
                $taskAssigneeMax = (int) $taskAssigneeTop->max('total') ?: 1;

                $stepAssigneeItems = collect($dashboard['steps_by_responsible'] ?? [])
                    ->sortByDesc('total')
                    ->values();
                $stepAssigneeTop = $stepAssigneeItems->take(10);
                $stepAssigneeOthersTotal = $stepAssigneeItems->slice(10)->sum('total');
                if ($stepAssigneeOthersTotal > 0) {
                    $stepAssigneeTop = $stepAssigneeTop->push([
                        'label' => 'Demais',
                        'total' => $stepAssigneeOthersTotal,
                    ]);
                }
                $stepAssigneeMax = (int) $stepAssigneeTop->max('total') ?: 1;
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Tarefas ativas por
                            responsável</p>
                        <span class="text-[11px] text-gray-400">Top 10 + demais</span>
                    </div>
                    <div class="space-y-3">
                        @forelse ($taskAssigneeTop as $item)
                            @php
                                $percent = $item['total'] > 0 ? ($item['total'] / $taskAssigneeMax) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-36 text-xs text-gray-600 truncate" title="{{ $item['label'] }}">
                                    {{ $item['label'] }}
                                </div>
                                <div class="flex-1">
                                    <div class="h-2 rounded-full bg-gray-100">
                                        <div class="h-2 rounded-full bg-emerald-500"
                                            style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                                <div class="w-10 text-right text-xs font-semibold text-gray-800">
                                    {{ $item['total'] }}
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Sem registros</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Etapas ativas por
                            responsável</p>
                        <span class="text-[11px] text-gray-400">Top 10 + demais</span>
                    </div>
                    <div class="space-y-3">
                        @forelse ($stepAssigneeTop as $item)
                            @php
                                $percent = $item['total'] > 0 ? ($item['total'] / $stepAssigneeMax) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-36 text-xs text-gray-600 truncate" title="{{ $item['label'] }}">
                                    {{ $item['label'] }}
                                </div>
                                <div class="flex-1">
                                    <div class="h-2 rounded-full bg-gray-100">
                                        <div class="h-2 rounded-full bg-blue-500"
                                            style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                                <div class="w-10 text-right text-xs font-semibold text-gray-800">
                                    {{ $item['total'] }}
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Sem registros</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @php
                $overdueTasks = collect($tasks->items())
                    ->filter(
                        fn($task) => $task->deadline_at &&
                            $task->deadline_at->isPast() &&
                            !$task->deadline_at->isToday(),
                    )
                    ->sortBy(fn($task) => $task->deadline_at)
                    ->take(10)
                    ->values();
            @endphp

            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Tarefas atrasadas</p>
                    <span class="text-[11px] text-gray-400">Somente atrasadas</span>
                </div>
                <div class="space-y-3">
                    @forelse ($overdueTasks as $task)
                        <button type="button" wire:click="openAsideTask({{ $task->id }})"
                            @click="openAsideTask = true"
                            class="w-full text-left rounded-lg border border-gray-100 bg-red-50/40 px-4 py-3 transition hover:shadow-sm hover:border-red-200">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate"
                                        title="{{ $task->title }}">
                                        <span class="text-[11px] font-mono text-red-600">{{ $task->code }}</span>
                                        <span class="text-gray-300 font-light mx-1">/</span>
                                        <span>{{ $task->title }}</span>
                                    </p>
                                    <div class="flex flex-wrap items-center gap-3 text-[11px] text-gray-500 mt-1">
                                        <span>Projeto: {{ $task->taskCategory?->title ?? 'Sem projeto' }}</span>
                                        <span>Responsável: {{ $task->user?->name ?? 'Sem responsável' }}</span>
                                        <span>Status: {{ $task->taskStatus?->title ?? 'Sem status' }}</span>
                                        <span>Vencimento:
                                            {{ $task->deadline_at?->format('d/m/Y') ?? 'Sem data' }}</span>
                                    </div>
                                </div>
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-semibold bg-red-100 text-red-700">
                                    Atrasada
                                </span>
                            </div>
                        </button>
                    @empty
                        <div
                            class="rounded-lg border border-dashed border-gray-200 bg-gray-50/40 p-4 text-xs text-gray-400 text-center">
                            Nenhuma tarefa atrasada no momento.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div x-show="tab === 'list'" x-cloak>
            <!-- Cabeçalho da Tabela -->
            <div class="relative overflow-hidden border border-gray-200 rounded-t-xl">
                <div
                    class="grid grid-cols-5 md:grid-cols-12 gap-4 px-6 py-3 bg-gradient-to-r from-emerald-700 to-emerald-800 text-xs font-semibold text-white uppercase tracking-wider">
                    <div class="col-span-5 md:col-span-4 flex items-center gap-2">
                        <div class="w-1 h-4 bg-white/50 rounded-full"></div>
                        <span>Título</span>
                    </div>
                    <div class="col-span-2 hidden md:block text-center">
                        <span class="flex items-center justify-center gap-1.5">
                            <i class="fas fa-user-circle text-white/80 text-xs"></i>
                            Solicitante
                        </span>
                    </div>
                    <div class="col-span-4 hidden md:grid grid-cols-3 gap-2">
                        <span class="text-center">Categoria</span>
                        <span class="text-center">Prioridade</span>
                        <span class="text-center">Status</span>
                    </div>
                    <div class="col-span-1 text-center hidden md:block">Início</div>
                    <div class="col-span-1 text-center hidden md:block">Prazo</div>
                </div>
            </div>

            <!-- Lista de Tarefas -->
            <div class="divide-y divide-gray-200 border border-gray-200 border-t-0 rounded-b-xl">
                @forelse ($tasks as $task)
                    <div x-data="{ openSteps: false }"
                        class="group/task transition-all duration-200 {{ $task->finished_at ? 'bg-emerald-50/30' : 'hover:bg-emerald-50/20' }}">

                        <!-- Linha da Tarefa -->
                        <div class="grid grid-cols-5 md:grid-cols-12 px-2 py-2.5 items-center relative">

                            <!-- Indicador de tarefa finalizada -->
                            @if ($task->finished_at)
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500"></div>
                            @endif

                            <!-- Título e Código -->
                            <div class="col-span-5 md:col-span-4 flex items-center pr-1">
                                <div class="flex-1 flex items-center gap-1 line-clamp-1">
                                    <div class="w-3">
                                        @if ($task->taskSteps->count() > 0)
                                            <button @click="openSteps = !openSteps"
                                                class="w-5 h-5 flex items-center justify-center">
                                                <i class="fas fa-chevron-right text-xs text-gray-500 transition-transform duration-200"
                                                    :class="{ 'rotate-90': openSteps }"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Código da Tarefa -->
                                    <button @click="openAsideTask = true; $wire.openAsideTask({{ $task->id }})"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded hover:bg-emerald-100/50 transition-colors">
                                        <span class="text-xs font-mono font-medium text-emerald-700">
                                            {{ $task->code }}
                                        </span>
                                        <i class="fas fa-external-link-alt text-[8px] text-gray-400"></i>
                                    </button>

                                    <div class="text-gray-300 mx-0.5">•</div>

                                    <!-- Título -->
                                    <span class="flex-1 text-xs text-gray-700 truncate" title="{{ $task->title }}">
                                        {{ $task->title }}
                                    </span>
                                </div>

                                <!-- Ações -->
                                @if (!$task->finished_at)
                                    <div
                                        class="flex items-center gap-1 opacity-0 group-hover/task:opacity-100 transition-opacity">
                                        @if ($task->taskSteps->count() < 1)
                                            <button wire:click="openCopyWorkflowModal({{ $task->id }})"
                                                class="w-6 h-6 flex items-center justify-center rounded hover:bg-emerald-100 text-gray-500 hover:text-emerald-600"
                                                title="Copiar etapas de um fluxo">
                                                <i class="fas fa-copy text-xs"></i>
                                            </button>
                                        @endif
                                        <button wire:click="enableCreateTaskStep({{ $task->id }})"
                                            @click="openSteps = true"
                                            class="w-6 h-6 flex items-center justify-center rounded hover:bg-emerald-100 text-gray-500 hover:text-emerald-600"
                                            title="Adicionar etapa">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Responsável -->
                            <div class="col-span-2 hidden md:block px-2 text-center">
                                <span class="text-xs text-gray-600 truncate block">
                                    {{ $task->user?->name ?? '—' }}
                                </span>
                            </div>

                            <!-- Categoria, Prioridade, Status -->
                            <div class="col-span-4 hidden md:grid grid-cols-3 gap-2 px-2 text-xs">
                                <!-- Categoria -->
                                <div class="text-center truncate">
                                    {{ $task->taskCategory?->title ?? '—' }}
                                </div>

                                <!-- Prioridade -->
                                <div class="text-center">
                                    @if ($task->taskPriority)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                                 {{ $task->taskPriority->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">
                                            <i class="fas fa-exclamation-circle text-[10px]"></i>
                                            {{ $task->taskPriority->title }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </div>

                                <!-- Status -->
                                <div class="text-center">
                                    @if ($task->taskStatus)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                                 {{ $task->taskStatus->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">
                                            <i class="fas fa-play-circle text-[10px]"></i>
                                            {{ $task->taskStatus->title }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Datas -->
                            <div class="col-span-1 hidden md:block text-center">
                                <span class="text-xs text-gray-600">
                                    {{ $task->started_at?->format('d/m') ?? '—' }}
                                </span>
                            </div>
                            <div class="col-span-1 hidden md:block text-center">
                                @if ($task->deadline_at)
                                    <span
                                        class="text-xs {{ $task->deadline_at->isPast() && !$task->finished_at ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                        {{ $task->deadline_at->format('d/m') }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </div>
                        </div>

                        <!-- Etapas da Tarefa -->
                        <div x-show="openSteps" x-collapse class="bg-gray-50/50">

                            <!-- Cabeçalho das Etapas -->
                            <div
                                class="grid grid-cols-5 md:grid-cols-12 items-center gap-4 px-6 py-2 bg-gray-100/50 border-y border-gray-200 text-[10px] font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="col-span-4 flex items-center gap-2">
                                    <div class="w-1 h-3 bg-amber-500 rounded-full"></div>
                                    <span>Etapas</span>
                                </div>
                                <div class="col-span-3 md:grid grid-cols-2 text-center">
                                    <div class="text-center">Setor</div>
                                    <div class="hidden md:block text-center">Responsável</div>
                                </div>
                                <div class="col-span-3 hidden md:grid grid-cols-2">
                                    <span class="text-center">Prioridade</span>
                                    <span class="text-center">Status</span>
                                </div>
                                <div class="col-span-1 hidden md:block text-center">Início</div>
                                <div class="col-span-1 hidden md:block text-center">Prazo</div>
                            </div>

                            <!-- Formulário de Criação da Etapa -->
                            @if (!$task->finished_at && $isCreatingTaskStep && $taskId == $task->id)
                                <div class="border-b border-gray-200 bg-amber-50/30 px-4 py-3">
                                    <form
                                        wire:submit.prevent="storeStep({{ $task->id }}, {{ $task->task_hub_id }})"
                                        class="space-y-3">
                                        @include('livewire.task._partials.task-step-form')
                                    </form>
                                </div>
                            @endif

                            <!-- Lista das Etapas -->
                            <div class="divide-y divide-gray-200">
                                @foreach ($task->taskSteps as $step)
                                    @if ($task->finished_at && $step->finished_at)
                                        @continue
                                    @endif
                                    <div
                                        class="grid grid-cols-5 md:grid-cols-12 px-4 py-2 items-center hover:bg-amber-50/30 transition-colors group/step">

                                        <!-- Título da Etapa -->
                                        <div class="col-span-4 flex items-center gap-2">
                                            <button @click="openAsideTask = true;"
                                                wire:click="openAsideTaskStep({{ $step->id }})"
                                                class="inline-flex items-center gap-1 py-0.5 rounded hover:bg-amber-100/50 transition-colors">
                                                <span class="text-xs font-mono text-amber-700">
                                                    {{ $step->code }}
                                                </span>
                                                <i class="fas fa-external-link-alt text-[8px] text-gray-400"></i>
                                            </button>

                                            <span class="text-gray-300">•</span>

                                            <span class="flex-1 text-xs text-gray-600 truncate">
                                                {{ $step->title }}
                                            </span>
                                        </div>

                                        <div class="col-span-3 md:grid grid-cols-2">
                                            <!-- Setor -->
                                            <div class="col-span-2 md:col-span-1 text-center">
                                                <span class="text-xs text-gray-600 truncate block">
                                                    {{ $step->organization?->acronym ?? '—' }}
                                                </span>
                                            </div>

                                            <!-- Responsável -->
                                            <div class="text-center hidden md:block">
                                                <span class="text-xs text-gray-600 truncate block">
                                                    {{ $step->user?->name ?? '—' }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Prioridade e Status -->
                                        <div class="col-span-3 hidden md:grid grid-cols-2 gap-2 px-2">
                                            <div class="text-center">
                                                @if ($step->taskPriority)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                                             {{ $step->taskPriority->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">
                                                        <i class="fas fa-exclamation-circle text-[8px]"></i>
                                                        {{ $step->taskPriority->title }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </div>
                                            <div class="text-center">
                                                @if ($step->taskStepStatus)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                                             {{ $step->taskStepStatus->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">
                                                        <i class="fas fa-play-circle text-[8px]"></i>
                                                        {{ $step->taskStepStatus->title }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Datas -->
                                        <div class="col-span-1 hidden md:block text-center">
                                            <span class="text-xs text-gray-600">
                                                {{ $step->started_at?->format('d/m') ?? '—' }}
                                            </span>
                                        </div>
                                        <div class="col-span-1 hidden md:block text-center">
                                            @if ($step->deadline_at)
                                                <span
                                                    class="text-xs {{ $step->deadline_at->isPast() && !$step->finished_at ? 'text-red-600' : 'text-gray-600' }}">
                                                    {{ $step->deadline_at->format('d/m') }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Estado Vazio -->
                    <div class="flex flex-col items-center justify-center py-12 px-4">
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-tasks text-2xl text-emerald-600"></i>
                        </div>
                        <h3 class="text-base font-medium text-gray-900 mb-1">Nenhuma tarefa encontrada</h3>
                        <p class="text-sm text-gray-500 mb-4 text-center max-w-sm">
                            Comece criando sua primeira tarefa para gerenciar suas atividades
                        </p>
                        <x-button text="Criar Primeira Tarefa" icon="fas fa-plus" wire:click="enableCreateTask"
                            size="sm" />
                    </div>
                @endforelse
            </div>

            <!-- Paginação -->
            @if ($tasks->hasPages())
                <div class="mt-4">
                    {{ $tasks->links('components.pagination') }}
                </div>
            @endif
        </div>

        <div x-show="tab === 'kanban'" x-cloak>
            <div x-data="{
                draggingId: null,
                fromStatusId: null,
                handleDragStart(event, taskId, statusId) {
                    this.draggingId = taskId;
                    this.fromStatusId = statusId;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', taskId);
                },
                handleDragEnd() {
                    this.draggingId = null;
                    this.fromStatusId = null;
                },
                handleDrop(event, statusId, beforeTaskId = null) {
                    event.preventDefault();
                    if (this.draggingId === null) {
                        return;
                    }
            
                    const taskId = this.draggingId;
                    const targetColumn = event.currentTarget.closest('[data-status-column]');
                    const targetList = targetColumn ? targetColumn.querySelector('[data-task-list]') : null;
                    const draggedEl = document.getElementById(`kanban-task-${taskId}`);
            
                    if (!targetList || !draggedEl) {
                        return;
                    }
            
                    if (beforeTaskId) {
                        const beforeEl = document.getElementById(`kanban-task-${beforeTaskId}`);
                        if (beforeEl && beforeEl.parentElement === targetList) {
                            targetList.insertBefore(draggedEl, beforeEl);
                        } else {
                            targetList.appendChild(draggedEl);
                        }
                    } else {
                        targetList.appendChild(draggedEl);
                    }
            
                    const sourceList = document.querySelector(`[data-task-list][data-status-id='${this.fromStatusId}']`);
                    const targetListCurrent = document.querySelector(`[data-task-list][data-status-id='${statusId}']`);
            
                    const sourceOrder = sourceList ?
                        Array.from(sourceList.children)
                        .map((el) => Number(el.dataset.taskId))
                        .filter((id) => Number.isFinite(id) && id > 0) : [];
                    const targetOrder = targetListCurrent ?
                        Array.from(targetListCurrent.children)
                        .map((el) => Number(el.dataset.taskId))
                        .filter((id) => Number.isFinite(id) && id > 0) : [];
            
                    this.$wire.requestKanbanMove(taskId, this.fromStatusId, statusId, sourceOrder, targetOrder);
                    this.draggingId = null;
                    this.fromStatusId = null;
                },
            }" class="flex gap-4 overflow-x-auto pb-4">
                
            @php($statusBg = [
                'gray' => 'bg-gray-50',
                'blue' => 'bg-blue-50',
                'green' => 'bg-green-50',
                'yellow' => 'bg-yellow-50',
                'red' => 'bg-red-50',
                'purple' => 'bg-purple-50',
            ])

                @foreach ($kanbanColumns as $column)
                    <div class="w-80 shrink-0 rounded-xl border border-gray-200 p-3 {{ $statusBg[$column['color'] ?? ''] ?? 'bg-gray-50' }}"
                        data-status-column data-status-id="{{ $column['status_id'] }}"
                        wire:key="kanban-status-{{ $column['status_id'] }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex h-2 w-2 rounded-full {{ $column['color_code_tailwind'] ?? 'bg-gray-300' }}"></span>
                                <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                    {{ $column['title'] }}
                                </h3>
                            </div>
                            <span class="text-[11px] font-medium text-gray-500">
                                {{ $column['tasks']->count() }}
                            </span>
                        </div>

                        <div class="mt-3 flex flex-col gap-2 min-h-[120px]" data-task-list
                            data-status-id="{{ $column['status_id'] }}" @dragover.prevent
                            @drop="handleDrop($event, {{ $column['status_id'] }})">
                            @forelse ($column['tasks'] as $task)
                                <div id="kanban-task-{{ $task->id }}" data-task-id="{{ $task->id }}"
                                    draggable="true"
                                    @dragstart="handleDragStart($event, {{ $task->id }}, {{ $column['status_id'] }})"
                                    @dragend="handleDragEnd()" @dragover.prevent
                                    @drop="handleDrop($event, {{ $column['status_id'] }}, {{ $task->id }})"
                                    class="group rounded-lg border border-gray-200 bg-white/90 p-3 shadow-sm hover:shadow transition"
                                    wire:key="kanban-task-{{ $task->id }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <button
                                            @click="openAsideTask = true; $wire.openAsideTask({{ $task->id }})"
                                            class="flex-1 text-left">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-mono text-emerald-700">
                                                    {{ $task->code }}
                                                </span>
                                                <span class="text-xs font-medium text-gray-800 line-clamp-2">
                                                    {{ $task->title }}
                                                </span>
                                            </div>
                                        </button>
                                        <div class="text-gray-300 text-xs cursor-grab select-none">⋮⋮</div>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between text-[11px] text-gray-500">
                                        <span class="truncate">
                                            {{ $task->user?->name ?? 'Sem responsável' }}
                                        </span>
                                        <span
                                            class="{{ $task->deadline_at && $task->deadline_at->isPast() && !$task->finished_at ? 'text-red-600' : 'text-gray-500' }}">
                                            {{ $task->deadline_at?->format('d/m') ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="rounded-lg border border-dashed border-gray-200 bg-gray-50/40 p-4 text-xs text-gray-400 text-center">
                                    Sem tarefas
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div x-show="tab === 'steps'" x-cloak>
            <div x-data="{
                draggingId: null,
                fromStatusId: null,
                handleDragStart(event, stepId, statusId) {
                    this.draggingId = stepId;
                    this.fromStatusId = statusId;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', stepId);
                },
                handleDragEnd() {
                    this.draggingId = null;
                    this.fromStatusId = null;
                },
                handleDrop(event, statusId, beforeStepId = null) {
                    event.preventDefault();
                    if (this.draggingId === null) {
                        return;
                    }
            
                    const stepId = this.draggingId;
                    const targetColumn = event.currentTarget.closest('[data-step-status-column]');
                    const targetList = targetColumn ? targetColumn.querySelector('[data-step-list]') : null;
                    const draggedEl = document.getElementById(`kanban-step-${stepId}`);
            
                    if (!targetList || !draggedEl) {
                        return;
                    }
            
                    if (beforeStepId) {
                        const beforeEl = document.getElementById(`kanban-step-${beforeStepId}`);
                        if (beforeEl && beforeEl.parentElement === targetList) {
                            targetList.insertBefore(draggedEl, beforeEl);
                        } else {
                            targetList.appendChild(draggedEl);
                        }
                    } else {
                        targetList.appendChild(draggedEl);
                    }
            
                    const sourceList = document.querySelector(`[data-step-list][data-status-id='${this.fromStatusId}']`);
                    const targetListCurrent = document.querySelector(`[data-step-list][data-status-id='${statusId}']`);
            
                    const sourceOrder = sourceList ?
                        Array.from(sourceList.children)
                        .map((el) => Number(el.dataset.stepId))
                        .filter((id) => Number.isFinite(id) && id > 0) : [];
                    const targetOrder = targetListCurrent ?
                        Array.from(targetListCurrent.children)
                        .map((el) => Number(el.dataset.stepId))
                        .filter((id) => Number.isFinite(id) && id > 0) : [];
            
                    this.$wire.requestKanbanStepMove(stepId, this.fromStatusId, statusId, sourceOrder, targetOrder);
                    this.draggingId = null;
                    this.fromStatusId = null;
                },
            }" class="flex gap-4 overflow-x-auto pb-4">
                @php($stepStatusBg = [
                    'gray' => 'bg-gray-50',
                    'blue' => 'bg-blue-50',
                    'green' => 'bg-green-50',
                    'yellow' => 'bg-yellow-50',
                    'red' => 'bg-red-50',
                    'purple' => 'bg-purple-50',
                ])
                @foreach ($kanbanStepColumns as $column)
                    <div class="w-80 shrink-0 rounded-xl border border-gray-200 p-3 {{ $stepStatusBg[$column['color'] ?? ''] ?? 'bg-gray-50' }}"
                        data-step-status-column data-status-id="{{ $column['status_id'] }}"
                        wire:key="kanban-step-status-{{ $column['status_id'] }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex h-2 w-2 rounded-full {{ $column['color_code_tailwind'] ?? 'bg-gray-300' }}"></span>
                                <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                    {{ $column['title'] }}
                                </h3>
                            </div>
                            <span class="text-[11px] font-medium text-gray-500">
                                {{ $column['steps']->count() }}
                            </span>
                        </div>

                        <div class="mt-3 flex flex-col gap-2 min-h-[120px]" data-step-list
                            data-status-id="{{ $column['status_id'] }}" @dragover.prevent
                            @drop="handleDrop($event, {{ $column['status_id'] }})">
                            @forelse ($column['steps'] as $step)
                                <div id="kanban-step-{{ $step->id }}" data-step-id="{{ $step->id }}"
                                    draggable="true"
                                    @dragstart="handleDragStart($event, {{ $step->id }}, {{ $column['status_id'] }})"
                                    @dragend="handleDragEnd()" @dragover.prevent
                                    @drop="handleDrop($event, {{ $column['status_id'] }}, {{ $step->id }})"
                                    class="group rounded-lg border border-gray-200 bg-white/90 p-3 shadow-sm hover:shadow transition"
                                    wire:key="kanban-step-{{ $step->id }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <button
                                            @click="openAsideTask = true; $wire.openAsideTaskStep({{ $step->id }})"
                                            class="flex-1 text-left">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-mono text-amber-700">
                                                    {{ $step->code }}
                                                </span>
                                                <span class="text-xs font-medium text-gray-800 line-clamp-2">
                                                    {{ $step->title }}
                                                </span>
                                            </div>
                                            <div class="mt-1 text-[11px] text-gray-500 truncate">
                                                {{ $step->task?->code }} - {{ $step->task?->title }}
                                            </div>
                                        </button>
                                        <div class="text-gray-300 text-xs cursor-grab select-none">⋮⋮</div>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between text-[11px] text-gray-500">
                                        <span class="truncate">
                                            {{ $step->organization?->acronym ?? 'Sem setor' }}
                                        </span>
                                        <span
                                            class="{{ $step->deadline_at && $step->deadline_at->isPast() && !$step->finished_at ? 'text-red-600' : 'text-gray-500' }}">
                                            {{ $step->deadline_at?->format('d/m') ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="rounded-lg border border-dashed border-gray-200 bg-gray-50/40 p-4 text-xs text-gray-400 text-center">
                                    Sem etapas
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div x-show="tab === 'members'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-1 rounded-xl border border-gray-200 bg-white p-4">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">Adicionar usuário
                    </h3>
                    @if ($taskHub->owner_id === auth()->id())
                        <form wire:submit.prevent="addMember" class="space-y-3">
                            <div>
                                <x-form.label value="Usuário" />
                                <x-form.select-livewire name="member_user_id" wire:model="member_user_id"
                                    :collection="$users" labelField="name" placeholder="Selecione um usuário" />
                                <x-form.error for="member_user_id" />
                            </div>
                            <div class="flex justify-end">
                                <x-button type="submit" text="Adicionar" icon="fas fa-user-plus" />
                            </div>
                        </form>
                    @else
                        <p class="text-xs text-gray-500">Apenas o proprietário pode gerenciar membros.</p>
                    @endif
                </div>

                <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Membros do ambiente
                        </h3>
                        <span class="text-[11px] text-gray-500">{{ $taskHub->members->count() }}</span>
                    </div>
                    <div class="space-y-2">
                        @forelse ($taskHub->members as $member)
                            <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2">
                                <div class="flex items-center gap-2">
                                    @if ($member->user->avatar)
                                        <img src="{{ asset('storage/' . $member->user->avatar) }}"
                                            alt="{{ $member->user->name }}" class="size-7 rounded-full object-cover"
                                            loading="lazy">
                                    @else
                                        <div
                                            class="size-7 rounded-full bg-emerald-600 text-white text-[9px] font-medium flex items-center justify-center uppercase">
                                            {{ \Illuminate\Support\Str::substr($member->user->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span
                                            class="text-xs font-medium text-gray-700">{{ $member->user->name }}</span>
                                        <span class="text-[11px] text-gray-400">{{ $member->user->email }}</span>
                                    </div>
                                </div>

                                @if ($taskHub->owner_id === auth()->id() && $member->user_id !== $taskHub->owner_id)
                                    <x-button variant="red_text" icon="fas fa-user-minus" title="Remover"
                                        wire:click="removeMember({{ $member->id }})" />
                                @endif
                            </div>
                        @empty
                            <div class="text-xs text-gray-400">Nenhum membro encontrado.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Aside -->
        <div>
            <div x-show="openAsideTask" x-transition:enter="transition-opacity duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" wire:click="closedAsideTask()" @click="openAsideTask = false"
                class="fixed inset-0 bg-black/50 z-30"></div>

            <div x-show="openAsideTask" x-transition:enter="transform transition duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition duration-300" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed top-0 right-0 z-40 h-screen w-full md:w-3/5 bg-white shadow-xl border-l border-gray-200 overflow-hidden">

                @if ($selectedTaskId)
                    <livewire:task.task-aside :taskId="$selectedTaskId" :key="'aside-task-' . $selectedTaskId" />
                @endif

                @if ($selectedTaskStepId)
                    <livewire:task.task-step-aside lazy :stepId="$selectedTaskStepId" :key="'aside-task-step' . $selectedTaskStepId" />
                @endif
            </div>
        </div>
    </div>

    <!-- Modal -->
    <x-modal :show="$showModal" maxWidth="max-w-lg">
        @if ($modalKey === 'modal-copy-workflow-steps')
            <x-slot name="header">
                Copiar etapas de um fluxo
            </x-slot>

            <form wire:submit.prevent="copyWorkflowSteps" class="space-y-4">
                <div>
                    <x-form.label value="Fluxo de trabalho" />
                    <x-form.select-livewire name="workflow_id" wire:model="workflow_id" :collection="$workflows"
                        value-field="id" label-field="title" />
                </div>

                <div class="flex justify-end gap-2">
                    <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" />
                    <x-button type="submit" text="Copiar etapas" />
                </div>
            </form>
        @endif

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

        @if ($modalKey === 'modal-kanban-reason')
            <x-slot name="header">
                {{ $kanbanReasonTitle }}
            </x-slot>

            <form wire:submit.prevent="confirmKanbanMove" class="space-y-4">
                <div>
                    <x-form.label value="Motivo" />
                    <x-form.textarea name="kanbanReason" rows="4" wire:model="kanbanReason" />
                    @error('kanbanReason')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if ($pendingKanbanReasonType === 'completion')
                    <div>
                        <x-form.label value="Comentário da conclusão" />
                        <x-form.textarea name="kanbanCompletionComment" rows="4"
                            wire:model="kanbanCompletionComment" />
                        @error('kanbanCompletionComment')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="flex justify-end gap-2">
                    <x-button text="Cancelar" variant="gray_outline" wire:click="cancelKanbanMove" type="button" />
                    <x-button type="submit" text="Confirmar" />
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-kanban-step-reason')
            <x-slot name="header">
                {{ $kanbanStepReasonTitle }}
            </x-slot>

            <form wire:submit.prevent="confirmKanbanStepMove" class="space-y-4">
                <div>
                    <x-form.label value="Motivo" />
                    <x-form.textarea name="kanbanStepReason" rows="4" wire:model="kanbanStepReason" />
                    @error('kanbanStepReason')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if ($pendingKanbanStepReasonType === 'completion')
                    <div>
                        <x-form.label value="Comentário da conclusão" />
                        <x-form.textarea name="kanbanStepCompletionComment" rows="4"
                            wire:model="kanbanStepCompletionComment" />
                        @error('kanbanStepCompletionComment')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="flex justify-end gap-2">
                    <x-button text="Cancelar" variant="gray_outline" wire:click="cancelKanbanStepMove"
                        type="button" />
                    <x-button type="submit" text="Confirmar" />
                </div>
            </form>
        @endif

    </x-modal>

</div>