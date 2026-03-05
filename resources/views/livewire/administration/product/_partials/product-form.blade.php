<div class="grid grid-cols-1 gap-4 md:grid-cols-12">
    <div class="md:col-span-4">
        <x-form.label value="Codigo" />
        <x-form.input type="text" wire:model.defer="code" placeholder="Codigo interno opcional" />
        <x-form.error for="code" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="SKU" />
        <x-form.input type="text" wire:model.defer="sku" placeholder="Ex: NOTE-DEL-14-I5" />
        <x-form.error for="sku" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="Natureza do item" />
        <x-form.select-livewire
            wire:model.defer="nature"
            name="nature"
            :options="[
                ['value' => 'ASSET', 'label' => 'Ativo (patrimonio)'],
                ['value' => 'SUPPLY', 'label' => 'Suprimento (consumo)'],
            ]"
        />
        <x-form.error for="nature" />
    </div>

    <div class="md:col-span-8">
        <x-form.label value="Titulo" />
        <x-form.input type="text" wire:model.defer="title" placeholder="Nome do equipamento/produto" />
        <x-form.error for="title" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="Tipo de produto" />
        <x-form.select-livewire
            wire:model.defer="product_type_id"
            name="product_type_id"
            default="Selecione o tipo"
            :options="collect($productTypes)->map(fn ($type) => ['value' => $type->id, 'label' => $type->title])->values()->all()"
        />
        <x-form.error for="product_type_id" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="Area de gestao padrao" />
        <x-form.select-livewire
            wire:model.defer="product_department_id"
            name="product_department_id"
            default="Selecione a area"
            :options="collect($productDepartments)->map(fn ($department) => ['value' => $department->id, 'label' => $department->name])->values()->all()"
        />
        <x-form.error for="product_department_id" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="Unidade padrao" />
        <x-form.select-livewire
            wire:model.defer="default_measure_unit_id"
            name="default_measure_unit_id"
            default="Opcional"
            :options="collect($measureUnits)->map(fn ($unit) => ['value' => $unit->id, 'label' => $unit->acronym.' - '.$unit->title])->values()->all()"
        />
        <x-form.error for="default_measure_unit_id" />
    </div>

    <div class="md:col-span-12">
        <x-form.label value="Descricao" />
        <x-form.textarea wire:model.defer="description" rows="4" placeholder="Descricao ampla do equipamento/produto" />
        <x-form.error for="description" />
    </div>
</div>
