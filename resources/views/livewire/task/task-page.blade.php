<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header title="Cronograma de Atividades" subtitle="Visualize todas as atividades e os andamentos" icon="fa-solid fa-list-check" >
        <x-slot name="button">
            <x-button text="Nova Tarefa" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>
       
    <!-- Filtros -->
    <x-page.filter title="Filtros">
        <x-slot name="showBasic">

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

        </x-slot>
    </x-page.filter>

    <!-- Tarefas -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        
        <!-- HEADER DA GRID -->
        <div class="grid grid-cols-12 gap-4 px-5 py-3 bg-green-50/80 border-b border-gray-200 text-xs font-semibold text-gray-600">
            <div class="col-span-3 pl-10">Título</div>
            <div class="col-span-1 text-center">Responsável</div>
            <div class="col-span-1 text-center">Categoria</div>
            <div class="col-span-1 text-center">Prioridade</div>
            <div class="col-span-1 text-center">Status</div>
            <div class="col-span-1 text-center">Criado</div>
            <div class="col-span-1 text-center">Atualizado</div>
            <div class="col-span-1 text-center">Início</div>
            <div class="col-span-1 text-center">Prazo</div>
            <div class="col-span-1 text-center">Finalizado</div>
        </div>

        @forelse ($tasks as $task)
            <div x-data="{ openSteps: false, openCreateStep: false }" 
                class="border-b border-gray-300 last:border-b-0 hover:bg-green-50/50 transition-colors duration-150">
                
                <!-- LINHA DA TASK -->
                <div class="grid grid-cols-12 gap-2 px-5 py-3 items-center divide-x">
                    
                    <!-- TÍTULO -->
                    <div class="col-span-3 flex items-center gap-2">                        
                        <div class="w-full flex items-center gap-2">
                            @if ($task->taskSteps->count() > 0)
                                <div>
                                    <x-button @click="openSteps = !openSteps; openCreateStep = false" variant="gray_outline">
                                        <i class="fa-solid fa-chevron-right text-xs transition-transform duration-200" :class="{ 'rotate-90': openSteps }"></i>
                                    </x-button>
                                </div>
                            @endif

                            <span class="flex-1 font-medium text-gray-800 truncate text-sm">
                                {{ $task->code }} - {{ $task->title }}
                            </span>
                        </div>

                        <div x-show="!openCreateStep" class="flex items-center justify-center gap-2">
                            <x-button icon="fa-solid fa-copy" variant="gray_outline" title="Copiar etapas de um fluxo" wire:click="openCopyWorkflowModal({{ $task->id }})"/>
                            <x-button icon="fa-solid fa-plus" variant="gray_outline" @click="openCreateStep = true; openSteps = true" />
                        </div>
                    </div>

                    <!-- RESPONSÁVEL -->
                    <div class="col-span-1">
                        <div class="relative flex items-center gap-2 px-2" >
                            <!-- AVATAR / PLACEHOLDER -->
                            <div>
                                @if($task->responsible)
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-700 to-green-800 
                                                flex items-center justify-center text-white text-sm font-semibold shadow-sm">
                                        {{ substr($task->responsible->name, 0, 1) }}
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fa-solid fa-user-plus text-gray-400 text-xs"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- NOME -->
                            <span class="text-xs text-gray-700 truncate mt-1 text-center">
                                {{ $task->responsible?->name }}
                            </span>
                        </div>
                    </div>

                    <!-- CATEGORIA -->
                    <div class="col-span-1">
                        <div class="flex justify-center">
                            @if($task->category)
                                <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full truncate max-w-full">
                                    {{ $task->category }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">—</span>
                            @endif
                        </div>
                    </div>

                    <!-- PRIORIDADE -->
                    <div class="col-span-1">
                        <div class="flex justify-center">
                            @if($task->priority)
                                <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full truncate max-w-full">
                                    {{ $task->priority->title }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">—</span>
                            @endif
                        </div>
                    </div>

                    <!-- STATUS -->
                    <div class="col-span-1">
                        <div class="flex justify-center">
                            @if($task->taskStatus)
                                <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full truncate max-w-full">
                                    {{ $task->taskStatus->title }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">—</span>
                            @endif
                        </div>
                    </div>

                    <!-- DATAS -->
                    <!-- Criado em -->
                    <div class="col-span-1">
                        <div class="flex flex-col items-center">
                            <div class="text-xs text-gray-700 font-medium">
                                {{ $task->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Atualizado em -->
                    <div class="col-span-1">
                        <div class="flex flex-col items-center">
                            <div class="text-xs text-gray-700 font-medium">
                                {{ $task->updated_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Iniciado em -->
                    <div class="col-span-1">
                        <div class="flex flex-col items-center">
                            @if ($task->started_at)
                                <div class="text-xs text-gray-700 font-medium">
                                    {{ $task->started_at->format('d/m/Y') }}
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">—</span>
                            @endif
                        </div>
                    </div>

                    <!-- Prazo -->
                    <div class="col-span-1">
                        <div class="flex flex-col items-center">
                            @if ($task->deadline_at)
                                <div class="text-xs text-gray-700 font-medium">
                                    {{ $task->deadline_at->format('d/m/Y') }}
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">—</span>
                            @endif
                        </div>
                    </div>

                    <!-- Finalizado em -->
                    <div class="col-span-1">
                        <div class="flex flex-col items-center">
                            @if ($task->finished_at)
                                <div class="text-xs text-green-600 font-medium">
                                    {{ $task->finished_at->format('d/m/Y') }}
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">—</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ETAPAS (Collapsible) -->
                <div class="bg-gray-50/30 border-t border-gray-100">
                    <div>
                        @include('livewire.task._partials.task-steps', ['task' => $task])
                    </div>
                </div>
            </div>
        @empty
            <!-- Estado Vazio -->
            <div class="flex flex-col items-center justify-center py-12 px-4">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-list-check text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-base font-medium text-gray-700 mb-2">Nenhuma tarefa encontrada</h3>
                <p class="text-sm text-gray-500">Comece criando sua primeira tarefa</p>
            </div>
        @endforelse
    </div>

    <!-- Modal -->
    <x-modal :show="$showModal" wire:key="workflow-modal">
        @if ($modalKey === 'modal-form-create-task')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Nova Tarefa</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.task._partials.task-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-create-task-step')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Adicionar Etapa</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.task._partials.task-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-copy-workflow-steps')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    Copiar etapas de um fluxo
                </h2>
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

                <div class="flex justify-end gap-2 pt-4">
                    <x-button variant="red" text="Cancelar" wire:click="closeModal" />
                    <x-button type="submit" text="Copiar etapas" />
                </div>
            </form>
        @endif

    </x-modal>

</div>
