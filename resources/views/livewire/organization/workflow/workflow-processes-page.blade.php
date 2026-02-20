<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header title="Fluxo de Trabalho" subtitle="Gerencie os fluxos de trabalho do sistema" icon="fa-solid fa-diagram-project">
        <x-slot name="button">
            <x-button text="Novo Fluxo" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>
       
    <!-- Filter -->
    <x-page.filter title="Filtros">
        {{-- Fluxo de Trabalho --}}
        <div class="col-span-12 md:col-span-6">
            <x-form.label value="Fluxo de Trabalho" />
            <x-form.input wire:model.live.debounce.500ms="filters.title" placeholder="Buscar por fluxo de trabalho..." />
        </div>

        {{-- Status --}}
        <div class="col-span-6 md:col-span-3">
            <x-form.label value="Status" />
            <x-form.select-livewire wire:model.live="filters.status" name="filters.status"
                :options="[
                    ['value' => 'all', 'label' => 'Todos'],
                    ['value' => 'true', 'label' => 'Ativo'],
                    ['value' => 'false', 'label' => 'Desativado'],
                ]"
            />
        </div>

        {{-- Itens por página --}}
        <div class="col-span-6 md:col-span-3">
            <x-form.label value="Itens por página" />
            <x-form.select-livewire wire:model.live="filters.perPage" name="filters.perPage"
                :options="[
                    ['value' => 10, 'label' => '10'],
                    ['value' => 25, 'label' => '25'],
                    ['value' => 50, 'label' => '50'],
                    ['value' => 100, 'label' => '100']
                ]"
            />
        </div>
    </x-page.filter>

    <!-- Table -->
    <x-page.table :pagination="$workflows">
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="w-40" value="Título" />
                <x-page.table-th class="hidden lg:table-cell" value="Descrição" />
                <x-page.table-th class="text-center w-16" value="Estimativa" />
                <x-page.table-th class="text-center w-28" value="Status" />
                <x-page.table-th class="text-center w-28" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($workflows as $workflow)
                <tr>
                    <x-page.table-td class="truncate" :value="$workflow->title" />
                    <x-page.table-td class="hidden lg:table-cell truncate" :value="$workflow->description ?? '-'" />
                    <x-page.table-td class="text-center" :value="$workflow->total_estimated_days. ' Dias' ?? '-'" />

                    <x-page.table-td class="text-center">
                        <div class="text-xs font-medium rounded-full py-0.5 px-1 {{ $workflow->is_active ? 'bg-green-300 text-green-700' : 'bg-red-300 text-red-700' }}">
                            {{ $workflow->is_active ? 'Ativo' : 'Desativado' }}
                        </div>
                    </x-page.table-td>

                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            <x-button wire:click="status({{ $workflow->id }})" icon="fa-solid fa-toggle-on" title="Alterar Status" variant="green_text"/>
                            <x-button wire:click="edit({{ $workflow->id }})" icon="fa-solid fa-pen" title="Editar Tipo de Tarefa" variant="green_text"/>
                            <x-button wire:click="workflowStep({{ $workflow->id }})" icon="fa-solid fa-eye" title="Editar Tipo de Tarefa" variant="green_text"/>
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>

    <!-- Modal -->
    <x-modal :show="$showModal" wire:key="workflow-modal">
        @if ($modalKey === 'modal-form-create-workflow')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Fluxo de Trabalho</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @component('livewire.organization.workflow._partials.workflow-processes-form') @endcomponent
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-edit-workflow')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Fluxo de Trabalho</h2>
            </x-slot>

            <form wire:submit.prevent="update" class="space-y-4">
                @component('livewire.organization.workflow._partials.workflow-processes-form') @endcomponent
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky"/>
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-workflow-steps')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Etapas do Processo</h2>
            </x-slot>

            <div>
                <livewire:organization.workflow.workflow-steps :workflowId="$workflowId" />
            </div>
        @endif
    </x-modal>
</div>