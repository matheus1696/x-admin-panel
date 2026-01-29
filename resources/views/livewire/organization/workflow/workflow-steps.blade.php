<div>

    <!-- Flash Message -->
    <x-alert.flash />

    {{-- Formulário --}}
    <form wire:submit.prevent="{{ $workflowStepId ? 'update' : 'store' }}" class="pb-5 border-b mb-5">
        <div class="grid grid-cols-12 gap-3 items-end">

            <div class="col-span-12 md:col-span-9">
                <x-form.label value="Nome da Etapa" />
                <x-form.input wire:model.defer="title" placeholder="Digite a etapa do processo" autofocus/>
            </div>

            <div class="col-span-4 md:col-span-3">
                <x-form.label value="Prazo (dias)" />
                <x-form.input type="number" min="0" wire:model.defer="deadline_days" placeholder="Tempo em dias para conclusão" />
            </div>

            <div class="col-span-4 md:col-span-4">
                <x-form.label value="Setor" />
                <x-form.select-livewire
                    wire:model.defer="organization_id"
                    name="organization_id"
                    :collection="$organizations"
                    :selected="$organization_id"
                    value-field="id"
                    label-acronym="acronym"
                    label-field="title"
                />
                <x-form.error :messages="$errors->get('organization_id')" />
            </div>

            <div class="col-span-4 md:col-span-4">
                <x-form.label value="Obrigatória" />
                <x-form.select-livewire
                    wire:model.defer="required"
                    name="required"
                    :collection="collect([
                        ['value' => true, 'label' => 'Sim'],
                        ['value' => false, 'label' => 'Não'],
                    ])"
                    value-field="value"
                    label-field="label"
                />
                <x-form.error :messages="$errors->get('required')" />
            </div>

            <div class="col-span-4 md:col-span-4">
                <x-form.label value="Paralelo?" />
                <x-form.select-livewire
                    wire:model.defer="allow_parallel"
                    name="allow_parallel"
                    :collection="collect([
                        ['value' => true, 'label' => 'Sim'],
                        ['value' => false, 'label' => 'Não'],
                    ])"
                    value-field="value"
                    label-field="label"
                />
                <x-form.error :messages="$errors->get('allow_parallel')" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button type="submit" text="{{ $workflowStepId ? 'Atualizar Etapa' : 'Adicionar Etapa' }}" variant="{{ $workflowStepId ? 'sky' : '' }}"/>
        </div>
    </form>

    {{-- Lista de atividades --}}
    <x-page.table>
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="text-center">Ordem</x-page.table-th>
                <x-page.table-th>Atividade</x-page.table-th>
                <x-page.table-th class="text-center">Setor</x-page.table-th>
                <x-page.table-th class="text-center">Prazo</x-page.table-th>
                <x-page.table-th class="text-center">Obrigatório</x-page.table-th>
                <x-page.table-th class="text-center">Paralelo?</x-page.table-th>
                <x-page.table-th class="text-center">Ações</x-page.table-th>
            </tr>
        </x-slot>
        <x-slot name="tbody">
            @forelse ($workflowSteps as $workflowStep)
                <tr class="hover:bg-gray-50">
                    <x-page.table-td class="text-center">{{ $workflowStep->step_order }}</x-page.table-td>
                    <x-page.table-td>{{ $workflowStep->title }}</x-page.table-td>
                    <x-page.table-td>{{ $workflowStep->organization?->acronym }}</x-page.table-td>
                    <x-page.table-td class="text-center">{{ $workflowStep->deadline_days }}</x-page.table-td>
                    <x-page.table-status :condition="$workflowStep->required" />
                    <x-page.table-status :condition="$workflowStep->allow_parallel" />
                    <x-page.table-td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            <x-button.btn-table wire:click="edit({{ $workflowStep->id }})" title="Editar Atividade">
                                <i class="fa-solid fa-pen"></i>
                            </x-button.btn-table>
                            @if ( $workflowStep->step_order != 1)
                                <x-button.btn-table wire:click="orderUp({{ $workflowStep->id }})" title="Subir Atividade">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </x-button.btn-table>
                            @endif
                        </div>                        
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                        Sem atividades adicionadas.
                    </td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

</div>
