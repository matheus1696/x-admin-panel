<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header title="Cronograma de Atividades" subtitle="Visualize todas as atividades e os andamentos" icon="fa-solid fa-list-check" >
        <x-slot name="button">
            <x-button text="Nova Tarefa" icon="fa-solid fa-plus" wire:click="enableCreateTask()" />
        </x-slot>
    </x-page.header>
       
    <!-- Filtros -->
    <x-page.filter title="Filtros">
        {{-- Título da Tarefa --}}
        <div class="col-span-12 md:col-span-8">
            <x-form.label value="Título da Tarefa" />
            <x-form.input wire:model.defer="filters.title" placeholder="Buscar por título..." />
        </div>

        {{-- Status --}}
        <div class="col-span-6 md:col-span-2">
            <x-form.label value="Status da Tarefa" />
            <x-form.select-livewire 
                wire:model.defer="filters.workflow_run_status_id" 
                name="filters.workflow_run_status_id" 
                :collection="$taskStatuses" 
                value-field="id" 
                label-field="title" 
            />
        </div>

        {{-- Itens por página --}}
        <div class="col-span-6 md:col-span-2">
            <x-form.label value="Itens por página" />
            <x-form.select-livewire wire:model.defer="filters.perPage" name="filters.perPage"
                :options="[
                    ['value' => 10, 'label' => '10'],
                    ['value' => 25, 'label' => '25'],
                    ['value' => 50, 'label' => '50'],
                    ['value' => 100, 'label' => '100']
                ]"
            />
        </div>
    </x-page.filter>

<!-- Card Principal Premium -->
<div class="bg-white/90 border border-gray-200/80 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300"
     x-data="{ openAsideTask: false }">
    
    <!-- HEADER PREMIUM - Cabeçalho com gradiente e glassmorphism -->
    <div class="relative overflow-hidden">
        <!-- Efeito de brilho no topo -->
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400 via-green-500 to-emerald-400"></div>
        
        <!-- Cabeçalho das Tarefas - Premium -->
        <div class="grid grid-cols-5 md:grid-cols-12 gap-4 px-6 py-4 bg-gradient-to-r from-emerald-50/90 via-white/80 to-emerald-50/90 backdrop-blur-sm border-b border-emerald-100/50">
            <div class="col-span-3 flex items-center gap-2">
                <div class="w-1 h-4 bg-gradient-to-b from-emerald-500 to-green-500 rounded-full"></div>
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Título</span>
            </div>
            <div class="col-span-2 text-center">
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center justify-center gap-1.5">
                    <i class="fas fa-user-circle text-emerald-500 text-xs"></i>
                    Solicitante
                </span>
            </div>
            <div class="col-span-5 hidden md:grid grid-cols-3 gap-2">
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider text-center">Categoria</span>
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider text-center">Prioridade</span>
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider text-center">Status</span>
            </div>
            <div class="col-span-1 text-center hidden md:block">
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center justify-center gap-1.5">
                    <i class="fas fa-play text-emerald-500 text-xs"></i>
                    Início
                </span>
            </div>
            <div class="col-span-1 text-center hidden md:block">
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center justify-center gap-1.5">
                    <i class="fas fa-hourglass-end text-emerald-500 text-xs"></i>
                    Prazo
                </span>
            </div>
        </div>
    </div>

    <!-- Formulário de Criação de Tarefa - Premium -->
    @if ($isCreatingTask)
        <div class="border-t-2 border-emerald-500/30 bg-gradient-to-br from-emerald-50/30 via-white to-emerald-50/30 px-6 py-5 animate-fade-in-down">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-green-500 rounded-full"></div>
                <h3 class="text-sm font-semibold bg-gradient-to-r from-emerald-700 to-green-700 bg-clip-text text-transparent">
                    Nova Tarefa
                </h3>
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-medium border border-emerald-200">
                    Criando
                </span>
            </div>
            <form wire:submit.prevent="storeTask" class="space-y-4">
                @include('livewire.task._partials.task-form')
            </form>
        </div>
    @endif

    <!-- Lista de Tarefas - Premium -->
    <div class="divide-y divide-gray-200/80">
        @forelse ($tasks as $task)
            <div x-data="{ openSteps: false }" 
                 class="group/task transition-all duration-300 hover:bg-gradient-to-r hover:from-emerald-50/30 hover:to-transparent
                        {{ $task->finished_at ? 'bg-gradient-to-r from-emerald-50/50 to-green-50/30' : '' }}">
                
                <!-- Linha da Tarefa - Premium -->
                <div class="grid grid-cols-5 md:grid-cols-12 px-6 py-3 items-center relative">
                    
                    <!-- Indicador de tarefa finalizada -->
                    @if($task->finished_at)
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-500 to-green-500"></div>
                    @endif

                    <!-- Título Premium -->
                    <div class="col-span-3 flex items-center gap-2 pr-2"> 
                        <div class="flex items-center gap-1 flex-1">
                            @if ($task->taskSteps->count() > 0)
                                <div class="w-5">
                                    <button @click="openSteps = !openSteps" 
                                            class="w-5 h-5 flex items-center justify-center rounded-md hover:bg-emerald-100 transition-all duration-200 group/btn">
                                        <i class="fa-solid fa-chevron-right text-xs text-gray-500 transition-all duration-200 group-hover/btn:text-emerald-600"
                                           :class="{ 'rotate-90 text-emerald-600': openSteps }"></i>
                                    </button>
                                </div>
                            @endif

                            <!-- Código da Tarefa Premium -->
                            <button @click="openAsideTask = true; $wire.openAsideTask({{$task->id}})" 
                                    class="group/code inline-flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-emerald-100/50 transition-all duration-200">
                                <span class="text-xs font-mono font-bold bg-gradient-to-r from-emerald-700 to-green-700 bg-clip-text text-transparent">
                                    {{ $task->code }}
                                </span>
                                <i class="fas fa-external-link-alt text-[8px] text-gray-400 opacity-0 group-hover/code:opacity-100 transition-opacity"></i>
                            </button>
                            
                            <span class="text-gray-400 mx-0.5">•</span>
                            
                            <!-- Título com truncate -->
                            <span class="flex-1 text-xs font-medium text-gray-700 line-clamp-1 group-hover/task:text-emerald-700 transition-colors" 
                                  title="{{ $task->title }}">
                                {{ $task->title }}
                            </span>
                        </div>

                        <!-- Ações Premium -->
                        @if (!$task->finished_at)
                            <div class="flex items-center gap-1 opacity-0 group-hover/task:opacity-100 transition-opacity duration-200">
                                @if ($task->taskSteps->count() < 1)
                                    <button wire:click="openCopyWorkflowModal({{ $task->id }})"
                                            class="w-6 h-6 flex items-center justify-center rounded-md hover:bg-emerald-100 text-gray-500 hover:text-emerald-600 transition-all"
                                            title="Copiar etapas de um fluxo">
                                        <i class="fa-solid fa-copy text-xs"></i>
                                    </button>                                
                                @endif 
                                <button wire:click="enableCreateTaskStep({{$task->id}})"
                                        class="w-6 h-6 flex items-center justify-center rounded-md hover:bg-emerald-100 text-gray-500 hover:text-emerald-600 transition-all"
                                        title="Adicionar etapa">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Responsável Premium -->
                    <div class="col-span-2 text-center">
                        <span class="inline-flex items-center gap-1.5 text-xs px-3 py-1 bg-gray-100/80 rounded-full border border-gray-200/80">
                            <i class="fas fa-user-circle text-gray-500 text-[10px]"></i>
                            {{ $task->user?->name ?? '—' }}
                        </span>
                    </div>

                    <!-- Informações Premium -->
                    <div class="col-span-5 hidden md:grid grid-cols-3 gap-2 px-2">
                        
                        <!-- Categoria -->
                        <div class="flex items-center justify-center">
                            <span class="text-xs px-3 py-1 bg-white/80 rounded-full border border-gray-200/80 shadow-sm">
                                {{ $task->taskCategory?->title ?? '—' }}
                            </span>
                        </div>

                        <!-- Prioridade com cor do banco -->
                        <div class="flex items-center justify-center">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold rounded-full px-3 py-1 shadow-sm ring-1 ring-black/5
                                         {!! $task->taskPriority?->color_code_tailwind ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700' !!}">
                                <i class="fas fa-exclamation-circle text-[10px]"></i>
                                {{ $task->taskPriority?->title ?? '—' }}
                            </span>
                        </div>

                        <!-- Status com cor do banco -->
                        <div class="flex items-center justify-center">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold rounded-full px-3 py-1 shadow-sm ring-1 ring-black/5
                                         {!! $task->taskStatus?->color_code_tailwind ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700' !!}">
                                <i class="fas fa-play-circle text-[10px]"></i>
                                {{ $task->taskStatus?->title ?? '—' }}
                            </span>
                        </div>
                    </div>

                    <!-- Iniciado em Premium -->
                    <div class="col-span-1 hidden md:block">
                        <div class="flex flex-col items-center">
                            @if ($task->started_at)
                                <span class="text-xs font-medium text-gray-700 bg-white/80 px-2 py-1 rounded-lg border border-gray-200/80">
                                    <i class="far fa-calendar-alt text-emerald-500 mr-1 text-[9px]"></i>
                                    {{ $task->started_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic bg-gray-100/50 px-2 py-1 rounded-lg">—</span>
                            @endif
                        </div>
                    </div>

                    <!-- Prazo Premium -->
                    <div class="col-span-1 hidden md:block">
                        <div class="flex flex-col items-center">
                            @if ($task->deadline_at)
                                @if ($task->finished_at)
                                    <span class="text-xs font-medium text-gray-700 bg-white/80 px-2 py-1 rounded-lg border border-gray-200/80">
                                        <i class="far fa-calendar-check text-emerald-500 mr-1 text-[9px]"></i>
                                        {{ $task->deadline_at->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-xs font-medium px-2 py-1 rounded-lg flex items-center gap-1
                                                 {{ $task->deadline_at->isPast() 
                                                    ? 'bg-rose-100/80 text-rose-700 border border-rose-200/80' 
                                                    : 'bg-emerald-100/80 text-emerald-700 border border-emerald-200/80' }}">
                                        <i class="fas {{ $task->deadline_at->isPast() ? 'fa-exclamation-triangle' : 'far fa-calendar-check' }} text-[9px]"></i>
                                        {{ $task->deadline_at->format('d/m/Y') }}
                                        @if($task->deadline_at->isPast())
                                            <span class="ml-1 text-[8px] uppercase font-bold">Atrasado</span>
                                        @endif
                                    </span>
                                @endif
                            @else
                                <span class="text-xs text-gray-400 italic bg-gray-100/50 px-2 py-1 rounded-lg">—</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if (!$task->finished_at)
                    <!-- Formulário de Criação da Etapa - Premium -->
                    @if ($isCreatingTaskStep && $taskId == $task->id)
                        <div class="border-t-2 border-amber-500/30 bg-gradient-to-br from-amber-50/30 via-white to-amber-50/30 px-6 py-4 ml-12 animate-fade-in-down">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-1 h-5 bg-gradient-to-b from-amber-500 to-orange-500 rounded-full"></div>
                                <h4 class="text-xs font-semibold bg-gradient-to-r from-amber-700 to-orange-700 bg-clip-text text-transparent">
                                    Nova Etapa
                                </h4>
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-[8px] font-medium border border-amber-200">
                                    {{ $task->code }}
                                </span>
                            </div>
                            <form wire:submit.prevent="storeStep({{$task->id}})" class="space-y-4">
                                @include('livewire.task._partials.task-step-form')
                            </form>
                        </div>
                    @endif
                @endif

                <!-- Componentização das Etapas - Premium Collapsed -->
                <div x-show="openSteps" 
                     x-collapse
                     class="bg-gradient-to-br from-gray-50/50 via-white to-gray-50/30">
                    
                    <!-- Cabeçalho das Etapas - Premium -->
                    <div class="grid grid-cols-5 md:grid-cols-12 gap-4 px-6 py-2.5 bg-gradient-to-r from-amber-50/50 via-white/80 to-amber-50/50 border-y border-amber-100/50">
                        <div class="col-span-3 flex items-center gap-2">
                            <div class="w-1 h-3 bg-gradient-to-b from-amber-500 to-orange-500 rounded-full"></div>
                            <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wider">Etapas</span>
                        </div>
                        <div class="col-span-2 text-center">
                            <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wider flex items-center justify-center gap-1">
                                <i class="fas fa-building text-amber-500 text-[8px]"></i>
                                Setor
                            </span>
                        </div>
                        <div class="col-span-2 hidden md:grid text-center">
                            <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wider flex items-center justify-center gap-1">
                                <i class="fas fa-user-tie text-amber-500 text-[8px]"></i>
                                Responsável
                            </span>
                        </div>
                        <div class="col-span-3 hidden md:grid grid-cols-2">
                            <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wider text-center">Prioridade</span>
                            <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wider text-center">Status</span>
                        </div>
                        <div class="col-span-1 hidden md:block text-center">
                            <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wider">Início</span>
                        </div>
                        <div class="col-span-1 hidden md:block text-center">
                            <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wider">Prazo</span>
                        </div>
                    </div>

                    <!-- Lista das Etapas - Premium -->
                    <div class="divide-y divide-amber-100/50">
                        @foreach ($task->taskSteps as $step)
                            <!-- Linha das Etapas Premium -->
                            <div class="grid grid-cols-5 md:grid-cols-12 px-6 py-2 items-center hover:bg-amber-50/30 transition-colors duration-150 group/step">
                                
                                <!-- Título da Etapa -->
                                <div class="col-span-3 flex items-center gap-2">       
                                    <button @click="openAsideTask = true;" wire:click="openAsideTaskStep({{$step->id}})" class="group/code inline-flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-amber-100/50 transition-all">
                                        <span class="text-xs font-mono font-medium bg-gradient-to-r from-amber-700 to-orange-700 bg-clip-text text-transparent">
                                            {{ $step->code }}
                                        </span>
                                        <i class="fas fa-external-link-alt text-[8px] text-gray-400 opacity-0 group-hover/code:opacity-100 transition-opacity"></i>
                                    </button>
                                    
                                    <span class="text-gray-300">•</span>
                                    
                                    <span class="flex-1 text-xs text-gray-600 line-clamp-1 group-hover/step:text-amber-700 transition-colors">
                                        {{ $step->title }}
                                    </span>
                                </div>

                                <!-- Setor -->
                                <div class="col-span-2 text-center">
                                    <span class="text-xs px-2 py-1 bg-white/80 rounded-full border border-gray-200/80 inline-flex items-center gap-1">
                                        <i class="fas fa-building text-gray-500 text-[8px]"></i>
                                        {{ $step->organization?->title ?? '—' }}
                                    </span>
                                </div>

                                <!-- Responsável -->
                                <div class="col-span-2 text-center hidden md:block">
                                    <span class="text-xs px-2 py-1 bg-white/80 rounded-full border border-gray-200/80 inline-flex items-center gap-1">
                                        <i class="fas fa-user-circle text-gray-500 text-[8px]"></i>
                                        {{ $step->user?->name ?? '—' }}
                                    </span>
                                </div>

                                <!-- Informação Premium -->
                                <div class="col-span-3 hidden md:grid grid-cols-2 gap-2 px-2">
                                    <!-- Prioridade -->
                                    <div class="flex items-center justify-center">
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold rounded-full px-2 py-1 shadow-sm
                                                     {!! $step->taskPriority?->color_code_tailwind ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700' !!}">
                                            <i class="fas fa-exclamation-circle text-[8px]"></i>
                                            {{ $step->taskPriority?->title ?? '—' }}
                                        </span>
                                    </div>

                                    <!-- Status -->
                                    <div class="flex items-center justify-center">
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold rounded-full px-2 py-1 shadow-sm
                                                     {!! $step->taskStepStatus?->color_code_tailwind ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700' !!}">
                                            <i class="fas fa-play-circle text-[8px]"></i>
                                            {{ $step->taskStepStatus?->title ?? '—' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- DATAS Premium -->
                                <!-- Inicio -->
                                <div class="col-span-1 px-2 hidden md:block">
                                    <div class="flex flex-col items-center">
                                        @if ($step->started_at)
                                            <span class="text-[10px] font-medium text-gray-600 bg-white/80 px-2 py-1 rounded-lg border border-gray-200/80">
                                                {{ $step->started_at->format('d/m') }}
                                            </span>
                                        @else
                                            <span class="text-[10px] text-gray-400 italic">—</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Prazo -->
                                <div class="col-span-1 px-2 hidden md:block">
                                    <div class="flex flex-col items-center">
                                        @if ($step->deadline_at)
                                            <span class="text-[10px] font-medium px-2 py-1 rounded-lg
                                                         {{ $step->deadline_at->isPast() && !$step->finished_at 
                                                            ? 'bg-rose-100/80 text-rose-700 border border-rose-200/80' 
                                                            : 'bg-white/80 text-gray-600 border border-gray-200/80' }}">
                                                {{ $step->deadline_at->format('d/m') }}
                                            </span>
                                        @else
                                            <span class="text-[10px] text-gray-400 italic">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>  
        @empty
            <!-- Estado Vazio Premium -->
            <div class="flex flex-col items-center justify-center py-16 px-6 bg-gradient-to-br from-gray-50/50 to-white">
                <div class="relative mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-green-100 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fa-solid fa-list-check text-3xl text-emerald-600"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-white rounded-full border-2 border-emerald-200 flex items-center justify-center shadow-md">
                        <i class="fas fa-plus text-emerald-500 text-xs"></i>
                    </div>
                </div>
                <h3 class="text-base font-semibold text-gray-800 mb-2">Nenhuma tarefa encontrada</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-md text-center">
                    Comece criando sua primeira tarefa para gerenciar suas atividades
                </p>
                @if (!$isCreatingTask)
                    <button wire:click="$set('isCreatingTask', true)"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-xs font-medium rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
                        <i class="fas fa-plus-circle"></i>
                        Criar Primeira Tarefa
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Aside Premium com Informações (Mantido igual ao padrão) -->
    <div>
        <!-- Overlay premium -->
        <div x-show="openAsideTask"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            wire:click="closedAsideTask()"
            @click="openAsideTask = false"
            class="fixed inset-0 bg-black bg-opacity-70 z-30"
            aria-hidden="true"
        ></div>

        <!-- Container do Aside -->
        <div x-show="openAsideTask"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed top-0 right-0 z-40 h-screen w-full md:w-3/5 bg-white shadow-2xl border-l-2 border-emerald-200/50 overflow-hidden">

            @if ($selectedTaskId)
                <livewire:task.task-aside lazy :taskId="$selectedTaskId" :key="'aside-task-'.$selectedTaskId" />
            @endif

            @if ($selectedTaskStepId)
                <livewire:task.task-step-aside lazy :stepId="$selectedTaskStepId" :key="'aside-task-step'.$selectedTaskStepId" />
            @endif
        </div>
    </div>
</div>

    <!-- Modal -->
    <x-modal :show="$showModal" wire:key="workflow-modal">
        @if ($modalKey === 'modal-copy-workflow-steps')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    Copiar etapas de um fluxo
                </h2>
            </x-slot>

            <form wire:submit.prevent="copyWorkflowSteps" class="space-y-4">
                <div>
                    <x-form.label value="Fluxo de trabalho" />
                    <x-form.select-livewire name="workflow_id" wire:model="workflow_id" :collection="$workflows" value-field="id" label-field="title" />
                </div>
                
                <div class="flex justify-between gap-2">
                    <x-button variant="red" text="Cancelar" wire:click="closeModal" variant="gray_outline" />
                    <x-button type="submit" text="Copiar etapas" />
                </div>
            </form>
        @endif
    </x-modal>

</div>
