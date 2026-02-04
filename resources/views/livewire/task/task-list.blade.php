<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <div x-data="{ openSteps: false, openCreateStep: false }" 
        class="border-y border-gray-300 last:border-b-0 hover:bg-green-50/50 transition-colors duration-150">
        
        <!-- LINHA DA TASK -->
        <div class="grid grid-cols-12 gap-2 px-5 items-center divide-x">
            
            <!-- TÍTULO -->
            <div class="col-span-4 flex items-center gap-2">                        
                <div class="flex-1 flex items-center gap-2">
                    @if ($task->taskSteps->count() > 0)
                        <div>
                            <x-button @click="openSteps = !openSteps; openCreateStep = false" variant="gray_outline">
                                <i class="fa-solid fa-chevron-right text-xs transition-transform duration-200" :class="{ 'rotate-90': openSteps }"></i>
                            </x-button>
                        </div>
                    @endif

                    <span class="flex-1 font-medium text-gray-700 truncate text-xs">
                        {{ $task->code }} - {{ $task->title }}
                    </span>
                </div>

                <div x-show="!openCreateStep" class="flex items-end justify-end gap-2">
                    @if ($task->taskSteps->count() < 1)
                        <x-button icon="fa-solid fa-copy" variant="gray_outline" title="Copiar etapas de um fluxo" wire:click="openCopyWorkflowModal({{ $task->id }})"/>                                
                    @endif
                    <x-button icon="fa-solid fa-plus" variant="gray_outline" @click="openCreateStep = true" wire:click="createStep()"/>
                </div>
            </div>

            <!-- RESPONSÁVEL -->
            <div class="col-span-2">
                <div class="px-2">
                    <x-form.select-livewire wire:model.live="responsable_id" :collection="$users" valueField="id" labelField="name" :selected="$task->user_id" variant="inline" />
                </div>
            </div>


            <!-- CATEGORIA -->
            <div class="col-span-1">
                <div class="flex justify-center">
                    @if($task->category)
                        <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full truncate max-w-full">
                            {{ $task->category->name }}
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

        <div x-show="openCreateStep" x-collapse class="border-t bg-white px-4 py-3" >
            <form wire:submit.prevent="storeStep()" class="space-y-4">
                @include('livewire.task._partials.task-step-form')
            </form>
        </div>

        <div x-show="openSteps">           

            <!-- HEADER DA GRID -->
            <div class="grid grid-cols-12 gap-4 px-5 py-2 text-xs font-semibold text-gray-600/30 border-y border-gray-600/30">
                <div class="col-span-4 text-center">Etapas</div>
                <div class="col-span-2 text-center">Setor Responsável</div>
                <div class="col-span-2 text-center">Usuário Responsável</div>
                <div class="col-span-1 text-center">Prioridade</div>
                <div class="col-span-1 text-center">Status</div>
                <div class="col-span-1 text-center">Prazo</div>
                <div class="col-span-1 text-center">Finalizado</div>
            </div>
            @foreach ($task->taskSteps as $step)
                <livewire:task.task-step-list :stepId="$step->id" :key="$step->id" />
            @endforeach
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
