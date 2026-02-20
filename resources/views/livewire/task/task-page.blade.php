<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header Padronizado -->
    <x-page.header 
        title="Cronograma de Atividades" 
        subtitle="Visualize todas as atividades e os andamentos" 
        icon="fas fa-list-check"
    >
        <x-slot name="button">
            <x-button text="Nova Tarefa" icon="fas fa-plus" wire:click="enableCreateTask()" />
        </x-slot>
    </x-page.header>
       
    <!-- Filtros Padronizados -->
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

    <!-- Card Principal -->
    <div x-data="{ openAsideTask: false }">
        
        <!-- Cabeçalho da Tabela -->
        <div class="relative overflow-hidden border border-gray-200 rounded-t-xl">
            <div class="grid grid-cols-5 md:grid-cols-12 gap-4 px-6 py-3 bg-gradient-to-r from-emerald-700 to-emerald-800 text-xs font-semibold text-white uppercase tracking-wider">
                <div class="col-span-3 md:col-span-4 flex items-center gap-2">
                    <div class="w-1 h-4 bg-white/50 rounded-full"></div>
                    <span>Título</span>
                </div>
                <div class="col-span-2 text-center">
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

        <!-- Formulário de Criação -->
        @if ($isCreatingTask)
            <div class="border border-gray-200 border-t-0 p-4">
                <form wire:submit.prevent="storeTask" class="space-y-4">
                    @include('livewire.task._partials.task-form')
                </form>
            </div>
        @endif

        <!-- Lista de Tarefas -->
        <div class="divide-y divide-gray-200 border border-gray-200 border-t-0 rounded-b-xl">
            @forelse ($tasks as $task)
                <div x-data="{ openSteps: false }" class="group/task transition-all duration-200 {{ $task->finished_at ? 'bg-emerald-50/30' : 'hover:bg-emerald-50/20' }}">
                    
                    <!-- Linha da Tarefa -->
                    <div class="grid grid-cols-5 md:grid-cols-12 px-3 py-3 items-center relative">
                        
                        <!-- Indicador de tarefa finalizada -->
                        @if($task->finished_at)
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500"></div>
                        @endif

                        <!-- Título e Código -->
                        <div class="col-span-3 md:col-span-4 flex items-center pr-2"> 
                            <div class="flex items-center gap-1 flex-1">
                                <div class="w-5">
                                    @if ($task->taskSteps->count() > 0)
                                        <button @click="openSteps = !openSteps" class="w-5 h-5 flex items-center justify-center">
                                            <i class="fas fa-chevron-right text-xs text-gray-500 transition-transform duration-200"
                                               :class="{ 'rotate-90': openSteps }"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Código da Tarefa -->
                                <button @click="openAsideTask = true; $wire.openAsideTask({{$task->id}})" 
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded hover:bg-emerald-100/50 transition-colors">
                                    <span class="text-xs font-mono font-medium text-emerald-700">
                                        {{ $task->code }}
                                    </span>
                                    <i class="fas fa-external-link-alt text-[8px] text-gray-400"></i>
                                </button>
                                
                                <span class="text-gray-300 mx-0.5">•</span>
                                
                                <!-- Título -->
                                <span class="flex-1 text-xs text-gray-700 truncate" title="{{ $task->title }}">
                                    {{ $task->title }}
                                </span>
                            </div>

                            <!-- Ações -->
                            @if (!$task->finished_at)
                                <div class="flex items-center gap-1 opacity-0 group-hover/task:opacity-100 transition-opacity">
                                    @if ($task->taskSteps->count() < 1)
                                        <button wire:click="openCopyWorkflowModal({{ $task->id }})"
                                                class="w-6 h-6 flex items-center justify-center rounded hover:bg-emerald-100 text-gray-500 hover:text-emerald-600"
                                                title="Copiar etapas de um fluxo">
                                            <i class="fas fa-copy text-xs"></i>
                                        </button>                                
                                    @endif 
                                    <button wire:click="enableCreateTaskStep({{$task->id}})" @click="openSteps = true"
                                            class="w-6 h-6 flex items-center justify-center rounded hover:bg-emerald-100 text-gray-500 hover:text-emerald-600"
                                            title="Adicionar etapa">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Responsável -->
                        <div class="col-span-2 px-2 text-center">
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
                                @if($task->taskPriority)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
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
                                @if($task->taskStatus)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
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
                                <span class="text-xs {{ $task->deadline_at->isPast() && !$task->finished_at ? 'text-red-600 font-medium' : 'text-gray-600' }}">
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
                        <div class="grid grid-cols-5 md:grid-cols-12 items-center gap-4 px-6 py-2 bg-gray-100/50 border-y border-gray-200 text-[10px] font-semibold text-gray-600 uppercase tracking-wider">
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
                                <form wire:submit.prevent="storeStep({{$task->id}})" class="space-y-3">
                                    @include('livewire.task._partials.task-step-form')
                                </form>
                            </div>
                        @endif

                        <!-- Lista das Etapas -->
                        <div class="divide-y divide-gray-200">
                            @foreach ($task->taskSteps as $step)
                                <div class="grid grid-cols-5 md:grid-cols-12 px-4 py-2 items-center hover:bg-amber-50/30 transition-colors group/step">
                                    
                                    <!-- Título da Etapa -->
                                    <div class="col-span-4 flex items-center gap-2">       
                                        <button @click="openAsideTask = true;" wire:click="openAsideTaskStep({{$step->id}})" 
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
                                            @if($step->taskPriority)
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                                             {{ $step->taskPriority->color_code_tailwind ?? 'bg-gray-100 text-gray-700' }}">
                                                    <i class="fas fa-exclamation-circle text-[8px]"></i>
                                                    {{ $step->taskPriority->title }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </div>
                                        <div class="text-center">
                                            @if($step->taskStepStatus)
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
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
                                            <span class="text-xs {{ $step->deadline_at->isPast() && !$step->finished_at ? 'text-red-600' : 'text-gray-600' }}">
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
                    @if (!$isCreatingTask)
                        <x-button 
                            text="Criar Primeira Tarefa"
                            icon="fas fa-plus"
                            wire:click="$set('isCreatingTask', true)"
                            size="sm"
                        />
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Paginação -->
        @if($tasks->hasPages())
            <div class="mt-4">
                {{ $tasks->links('components.pagination') }}
            </div>
        @endif

        <!-- Aside -->
        <div>
            <div x-show="openAsideTask"
                 x-transition:enter="transition-opacity duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 wire:click="closedAsideTask()"
                 @click="openAsideTask = false"
                 class="fixed inset-0 bg-black/50 z-30"
            ></div>

            <div x-show="openAsideTask"
                 x-transition:enter="transform transition duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="fixed top-0 right-0 z-40 h-screen w-full md:w-3/5 bg-white shadow-xl border-l border-gray-200 overflow-hidden">

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
    <x-modal :show="$showModal" maxWidth="max-w-lg">
        @if ($modalKey === 'modal-copy-workflow-steps')
            <x-slot name="header">
                Copiar etapas de um fluxo
            </x-slot>

            <form wire:submit.prevent="copyWorkflowSteps" class="space-y-4">
                <div>
                    <x-form.label value="Fluxo de trabalho" />
                    <x-form.select-livewire 
                        name="workflow_id" 
                        wire:model="workflow_id" 
                        :collection="$workflows" 
                        value-field="id" 
                        label-field="title" 
                    />
                </div>
                
                <div class="flex justify-end gap-2">
                    <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" />
                    <x-button type="submit" text="Copiar etapas" />
                </div>
            </form>
        @endif
    </x-modal>

</div>