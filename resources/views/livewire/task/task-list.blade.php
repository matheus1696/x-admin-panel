<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <div x-data="{ openSteps: false, openCreateStep: false, openAsideTask: false }" class="border-y border-gray-300 last:border-b-0 hover:bg-green-50/25 transition-colors duration-150">
        
        <!-- LINHA DA TASK -->
        <div class="grid grid-cols-5 md:grid-cols-12 px-5 py-2 items-center divide-x">
            
            <!-- TÍTULO -->
            <div class="col-span-3 flex items-center justify-center pr-2"> 
                <div class="flex-1 flex items-center gap-1">
                    <div class="w-4">
                        @if ($task->taskSteps->count() > 0)
                            <div>
                                <x-button @click="openSteps = !openSteps; openCreateStep = false" variant="green_text">
                                    <i class="fa-solid fa-chevron-right text-xs transition-transform duration-200" :class="{ 'rotate-90': openSteps }"></i>
                                </x-button>
                            </div>
                        @endif
                    </div>

                    <div>
                        <x-button variant="green_text" :text="$task->code" @click=" openAsideTask = true; openAsideTaskStep = false;
                        if (activeItem === {{ $task->id }})
                            activeItem = null; 
                        else 
                            activeItem = {{ $task->id }} "
                         />
                    </div> -
                    <span class="flex-1 font-medium text-gray-700 line-clamp-1 text-xs" title="{{ $task->title }}">
                        {{ $task->title }}
                    </span>
                </div>

                <div x-show="!openCreateStep" class="flex items-end justify-end gap-2">
                    @if ($task->taskSteps->count() < 1)
                        <x-button icon="fa-solid fa-copy" variant="gray_text" title="Copiar etapas de um fluxo" wire:click="openCopyWorkflowModal({{ $task->id }})"/>                                
                    @endif
                    <x-button icon="fa-solid fa-plus" variant="gray_text" @click="openCreateStep = true" wire:click="createStep()"/>
                </div>
            </div>

            <!-- RESPONSÁVEL -->
            <div class="col-span-2 px-2">
                <x-form.select-livewire wire:model.live="responsable_id" :collection="$users" valueField="id" labelField="name" :selected="$task->user_id" variant="pills" size="xs" />
            </div>


            <div class="col-span-5 hidden md:grid grid-cols-3 divide-x">
                <!-- CATEGORIA -->
                <div class="px-2">
                    <x-form.select-livewire wire:model.live="list_category_id" :collection="$taskCategories" valueField="id" labelField="title" :selected="$task->task_category_id" variant="pills" size="xs" />
                </div>

                <!-- PRIORIDADE -->
                <div class="px-2">
                    <x-form.select-livewire wire:model.live="list_priority_id" :collection="$taskPriorities" valueField="id" labelField="title" :selected="$task->task_priority_id" variant="pills" size="xs" borderColor="{{ $task->taskPriority?->color }}" />
                </div>

                <!-- STATUS -->
                <div class="px-2">
                    <x-form.select-livewire wire:model.live="list_status_id" :collection="$taskStatuses" valueField="id" labelField="title" :selected="$task->task_status_id" variant="pills" size="xs" borderColor="{{ $task->taskStatus?->color }}" />
                </div>
            </div>

            <!-- DATAS -->
            <!-- Iniciado em -->
            <div class="col-span-1 hidden md:block">
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
            <div class="col-span-1 hidden md:block">
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
        </div>

        <div x-show="openCreateStep" x-collapse class="border-t bg-white px-4 py-3" >
            <form wire:submit.prevent="storeStep()" class="space-y-4">
                @include('livewire.task._partials.task-step-form')
            </form>
        </div>

        <div x-show="openSteps">           

            <!-- HEADER DA GRID -->
            <div class="grid grid-cols-5 md:grid-cols-12 gap-4 px-5 py-2 text-xs font-semibold text-gray-600/30 border-y border-gray-600/30">
                <div class="col-span-3">Etapas</div>
                <div class="col-span-2 text-center">Setor Responsável</div>
                <div class="col-span-2 hidden md:grid text-center">Usuário Responsável</div>
                <div class="col-span-3 hidden md:grid grid-cols-2">
                    <div class="col-span-1 text-center">Prioridade</div>
                    <div class="col-span-1 text-center">Status</div>
                </div>
                <div class="col-span-1 hidden md:block text-center">Início</div>
                <div class="col-span-1 hidden md:block text-center">Prazo</div>
            </div>
            @foreach ($task->taskSteps as $step)
                <livewire:task.task-step-list :stepId="$step->id" :key="$step->id" />
            @endforeach
        </div>

        <!-- Overlay que escurece o resto da página -->
        <div 
            x-show="openAsideTask && activeItem === {{ $task->id }}"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="openAsideTask = false; activeItem = null"
            class="fixed inset-0 bg-black bg-opacity-70 z-30"
            aria-hidden="true"
        ></div>

        <div x-show="openAsideTask && activeItem === {{ $task->id }}"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 z-30 h-screen w-full md:w-2/4 bg-white text-gray-900 overflow-y-auto shadow-lg border-l-2 border-green-700">

            <livewire:task.task-aside :taskId="$task->id" :key="'aside-'.$task->id" />
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
                    <x-form.select-livewire
                        name="workflow_id"
                        wire:model="workflow_id"
                        :collection="$workflows"
                        value-field="id"
                        label-field="title"
                    />
                </div>

                <div class="flex justify-end gap-2">
                    <x-button variant="red" text="Cancelar" wire:click="closeModal" />
                    <x-button type="submit" text="Copiar etapas" />
                </div>
            </form>
        @endif
    </x-modal>

</div>
