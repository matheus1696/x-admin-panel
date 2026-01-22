<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header 
        title="Tarefas de Fluxo de Trabalho" 
        subtitle="Gerencie todos os fluxos de trabalho do sistema" 
        icon="fa-solid fa-diagram-project"
    >
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

    <!-- Tabela de Tarefas -->
    <x-page.table :pagination="$workflowRuns">
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Título" />
                <x-page.table-th class="text-center w-40" value="Etapa Atual" />
                <x-page.table-th class="text-center w-28" value="Status" />
                <x-page.table-th class="text-center w-28" value="Ações" />
            </tr>
        </x-slot>

        

        <x-slot name="tbody">
            @forelse ($workflowRuns as $workflowRun)
                <tr class="hover:bg-gray-50">
                    <x-page.table-td class="truncate" :value="$workflowRun->title" />
                    <x-page.table-td class="text-center" :value="$workflowRun->currentWorkflowStep->title" ></x-page.table-td>

                    <x-page.table-td class="text-center">
                        <div class="text-xs font-medium rounded-full py-0.5 px-2 
                            {!! $workflowRun->workflowRunStatus?->color ?? 'bg-gray-300 text-gray-700' !!}">
                            {{ $workflowRun->workflowRunStatus?->title ?? 'Desconhecido' }}
                        </div>
                    </x-page.table-td>

                    <x-page.table-td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            <x-button.btn-table wire:click="info({{ $workflowRun->id }})" title="Visualizar Detalhes">
                                <i class="fa-solid fa-eye"></i>
                            </x-button.btn-table>
                        </div>
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                        Nenhuma tarefa encontrada.
                    </td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

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
