<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header title="Gestão de Status" subtitle="Gerencie os status de execução dos fluxos e etapas" icon="fa-solid fa-diagram-project" />
       
    <div>
        <div class="flex items-center justify-between pl-3 mb-4">
            <h2 class="flex-1 text-sm font-semibold uppercase text-gray-600">Status do Processo</h2>
            <div>
                <x-button text="Novo Status do Processo" wire:click="createRunStatus" />
            </div>
        </div>

        <x-page.table>
            <x-slot name="thead">
                <tr>
                    <x-page.table-th class="text-center w-24" value="Apresentação" />
                    <x-page.table-th value="Título" />
                    <x-page.table-th class="text-center w-28" value="Ações" />
                </tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach ($workflowRunStatuses as $workflowRunStatus)
                    <tr>
                        <x-page.table-td class="text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium  {{ $workflowRunStatus->color }}">
                               {{ $workflowRunStatus->title }}
                            </span>
                        </x-page.table-td>

                        <x-page.table-td :value="$workflowRunStatus->title" />

                        <x-page.table-td class="text-center">
                            <div class="flex justify-center gap-2">
                                <x-button.btn-table wire:click="editRunStatus({{ $workflowRunStatus->id }})" title="Editar Status" >
                                    <i class="fa-solid fa-pen"></i>
                                </x-button.btn-table>
                            </div>
                        </x-page.table-td>
                    </tr>
                @endforeach
            </x-slot>
        </x-page.table>
    </div>

    <div class="my-10 h-0.5 bg-gray-300/30 rounded-full"></div>

    <div>
        <div class="flex items-center justify-between pl-4 mb-4">
            <h2 class="text-sm font-semibold uppercase text-gray-600">Status das Etapas</h2>
            <div>
                <x-button text="Novo Status da Etapa" wire:click="createRunStepStatus" />
            </div>
        </div>

        <x-page.table>
            <x-slot name="thead">
                <tr>
                    <x-page.table-th class="text-center w-24" value="Apresentação" />
                    <x-page.table-th value="Título" />
                    <x-page.table-th class="text-center w-28" value="Ações" />
                </tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach ($workflowRunStepStatuses as $workflowRunStepStatus)
                    <tr>
                        <x-page.table-td class="text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $workflowRunStepStatus->color }}">
                                {{ $workflowRunStepStatus->title }}
                            </span>
                        </x-page.table-td>
                        <x-page.table-td :value="$workflowRunStepStatus->title" />

                        <x-page.table-td class="text-center">
                            <div class="flex justify-center gap-2">
                                <x-button.btn-table wire:click="editRunStepStatus({{ $workflowRunStepStatus->id }})" title="Editar Status" >
                                    <i class="fa-solid fa-pen"></i>
                                </x-button.btn-table>
                            </div>
                        </x-page.table-td>
                    </tr>
                @endforeach
            </x-slot>
        </x-page.table>
    </div>


    <!-- Modal -->
    <x-modal :show="$showModal" wire:key="workflow-modal-run-status">
        @if ($modalKey === 'modal-form-create-workflow-run-status')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Novo Status do Processo</h2>
            </x-slot>

            <form wire:submit.prevent="storeRunStatus" class="space-y-4">
                @component('livewire.organization.workflow._partials.workflow-run-status-form') @endcomponent
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-edit-workflow-run-status')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Status do Processo</h2>
            </x-slot>

            <form wire:submit.prevent="updateRunStatus" class="space-y-4">
                @component('livewire.organization.workflow._partials.workflow-run-status-form') @endcomponent
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky"/>
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-create-workflow-run-step-status')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Status da Etapa</h2>
            </x-slot>

            <form wire:submit.prevent="storeRunStepStatus" class="space-y-4">
                @component('livewire.organization.workflow._partials.workflow-run-status-form') @endcomponent
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-edit-workflow-run-step-status')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Status da Etapa</h2>
            </x-slot>

            <form wire:submit.prevent="updateRunStepStatus" class="space-y-4">
                @component('livewire.organization.workflow._partials.workflow-run-status-form') @endcomponent
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky"/>
                </div>
            </form>
        @endif
    </x-modal>
</div>