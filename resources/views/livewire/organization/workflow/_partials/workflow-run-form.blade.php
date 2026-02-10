<div class="grid grid-cols-12 items-center justify-center gap-4">
    <div class="col-span-12 md:col-span-6">
        <x-form.label value="Tipo de Processo" />
        <x-form.select-livewire 
            wire:model.defer="workflow_id" 
            name="workflow_id" 
            :collection="$workflows" 
            value-field="id" 
            label-field="title" 
        />
    </div>

    <div class="col-span-12 md:col-span-6">
        <x-form.label value="Fluxo de Trabalho" />
        <x-form.input wire:model.defer="title" placeholder="Descrição da Atividade" required/>
        <x-form.error for="title" />
    </div>
</div>
<div>
    <x-form.label value="Descrição" />
    <x-form.textarea wire:model.defer="description" placeholder="Descreva quando este fluxo deve ser utilizado (opcional)" rows="4"/>
    <x-form.error for="description" />
</div>


