<div class="grid grid-cols-1 md:grid-cols-12 gap-3">
    <div class="md:col-span-12">
        <x-form.label value="Titulo do Processo" />
        <x-form.input wire:model.defer="title" name="title" placeholder="Ex: Contratacao de servico especializado" />
        <x-form.error for="title" />
    </div>

    <div class="md:col-span-12">
        <x-form.label value="Descricao" />
        <x-form.textarea wire:model.defer="description" name="description" rows="3" placeholder="Detalhes do processo" />
        <x-form.error for="description" />
    </div>

    <div class="md:col-span-12">
        <x-form.label value="Fluxo" />
        <x-form.select-livewire
            wire:model.live="workflow_id"
            name="workflow_id"
            :options="$workflows->map(fn($workflow) => ['value' => $workflow->id, 'label' => $workflow->title])->values()->all()"
            placeholder="Selecione um fluxo"
        />
        <x-form.error for="workflow_id" />
    </div>
</div>
