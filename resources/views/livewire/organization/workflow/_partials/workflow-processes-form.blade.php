<div>
    <x-form.label value="Fluxo de Trabalho" />
    <x-form.input wire:model.defer="title" placeholder="Processo Licitatório" required/>
    <x-form.error for="title" />
</div>
<div>
    <x-form.label value="Descrição" />
    <x-form.input wire:model.defer="description" placeholder="Descreva quando este fluxo deve ser utilizado (opcional)" rows="4"/>
    <x-form.error for="description" />
</div>