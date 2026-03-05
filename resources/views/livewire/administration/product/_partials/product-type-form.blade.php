<div class="grid grid-cols-1 gap-4 md:grid-cols-12">
    <div class="md:col-span-12">
        <x-form.label value="Tipo de produto" />
        <x-form.input type="text" wire:model.defer="title" placeholder="Ex: Equipamento, Medicamento, Escritorio" />
        <x-form.error for="title" />
    </div>

    <div class="md:col-span-12">
        <x-form.label value="Descricao" />
        <x-form.textarea wire:model.defer="description" rows="4" placeholder="Descricao opcional da categoria" />
        <x-form.error for="description" />
    </div>
</div>

