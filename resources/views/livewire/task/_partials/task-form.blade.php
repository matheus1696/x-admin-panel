<div class="grid grid-cols-1 items-center justify-center gap-4">
    <div>
        <x-form.label value="Ativiade" />
        <x-form.input wire:model.defer="title" placeholder="Descrição da Atividade" required/>
        <x-form.error :messages="$errors->get('title')" />
    </div>
    <div>
        <x-form.label value="Descrição" />
        <x-form.textarea wire:model.defer="description" placeholder="Descreva um pouco da atividade de forma geral (opcional)" rows="4"/>
        <x-form.error :messages="$errors->get('description')" />
    </div>
</div>


