<div class="grid grid-cols-1 gap-4 md:grid-cols-12">
    <div class="md:col-span-4">
        <x-form.label value="Sigla" />
        <x-form.input type="text" wire:model.defer="acronym" placeholder="Ex: PCT/6" />
        <x-form.error for="acronym" />
    </div>

    <div class="md:col-span-5">
        <x-form.label value="Titulo" />
        <x-form.input type="text" wire:model.defer="title" placeholder="Ex: Pacote com 6" />
        <x-form.error for="title" />
    </div>

    <div class="md:col-span-3">
        <x-form.label value="Base de calculo" />
        <x-form.input type="number" min="1" wire:model.defer="base_quantity" />
        <x-form.error for="base_quantity" />
    </div>
</div>

