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
                    :collection="$workflowRunStatuses" 
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
    <div class="space-y-4">
        @forelse ($workflowRuns as $workflowRun)
            <div 
                x-data="{ open: false }"
                class="bg-white border rounded-xl shadow-sm hover:shadow transition"
            >
                <!-- HEADER DO PROCESSO -->
                <div class="flex items-center justify-between gap-3 p-4 cursor-pointer" @click="open = !open" >
                    
                    <!-- ÍCONE COLLAPSE -->
                    <i class="fa-solid fa-chevron-right text-gray-400 transition" :class="{ 'rotate-90': open }"></i>
                    
                    <div class="flex-1 flex flex-col gap-1">
                        
                        <div class="flex items-center gap-3">
                            <h3 class="font-semibold text-gray-800 text-sm">
                                {{ $workflowRun->title }}
                            </h3>
                            
                            <!-- STATUS -->
                            <span class="text-xs font-medium px-3 py-1 rounded-full
                                {!! $workflowRun->workflowRunStatus?->color ?? 'bg-gray-200 text-gray-700' !!}">
                                {{ $workflowRun->workflowRunStatus?->title ?? 'Desconhecido' }}
                            </span>
                        </div>
                        

                        <div class="hidden md:flex items-center gap-2 text-xs text-gray-500">
                            <span>Etapa atual:</span>
                            <span class="font-medium text-gray-700 truncate">
                                {{ $workflowRun->currentWorkflowStep?->title ?? 'Não iniciada' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">

                        <!-- AÇÕES -->
                        <x-button wire:click.stop="info({{ $workflowRun->id }})" title="Detalhes" icon="fa-solid fa-eye" variant="gray_outline" />
                    </div>
                </div>

                <!-- CONTEÚDO EXPANDIDO -->
                <div x-show="open" x-collapse class="border-t bg-gray-50">
                    @include('livewire.organization.workflow._partials.workflow-run-steps', [
                        'workflowRun' => $workflowRun
                    ])
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-8">
                Nenhuma tarefa encontrada.
            </div>
        @endforelse
    </div>

    <!-- Modal de Criação -->
    <x-modal :show="$showModal" wire:key="workflow-modal">
        @if ($modalKey === 'modal-form-create-workflow-run')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Nova Tarefa</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @component('livewire.organization.workflow._partials.workflow-run-form', ['workflows' => $workflows]) @endcomponent
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
    </x-modal>

</div>
