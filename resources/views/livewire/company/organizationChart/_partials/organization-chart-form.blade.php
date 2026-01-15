<div>
    <x-form.label value="Sigla" />
    <x-form.input wire:model.defer="acronym" placeholder="Sigla" required/>
    <x-form.error :messages="$errors->get('acronym')" />
</div>
<div>
    <x-form.label value="Setor" />
    <x-form.input wire:model.defer="name" placeholder="Nome do Setor" required/>
    <x-form.error :messages="$errors->get('name')" />
</div>
<div>
    <x-form.label value="Setor Pai" />
    <x-form.select-livewire wire:model.live="parent_id" name="parent_id" :collection="$formOrganizationCharts" value-field="id" label-field="name" />
    <x-form.error :messages="$errors->get('name')" />
</div>
