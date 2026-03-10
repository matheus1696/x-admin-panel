<div class="grid grid-cols-1 md:grid-cols-12 gap-4">
    <div class="md:col-span-12">
        <x-form.label value="Nome" />
        <x-form.input wire:model.defer="name" />
        <x-form.error for="name" />
    </div>

    <div class="md:col-span-6">
        <x-form.label value="Latitude" />
        <x-form.input type="number" step="0.0000001" wire:model.defer="latitude" />
        <x-form.error for="latitude" />
    </div>

    <div class="md:col-span-6">
        <x-form.label value="Longitude" />
        <x-form.input type="number" step="0.0000001" wire:model.defer="longitude" />
        <x-form.error for="longitude" />
    </div>

    <div class="md:col-span-6">
        <x-form.label value="Raio em metros" />
        <x-form.input type="number" wire:model.defer="radius_meters" />
        <x-form.error for="radius_meters" />
    </div>

    <div class="md:col-span-6">
        <x-form.label value="Ativo" />
        <x-form.select-livewire
            wire:model.live="active"
            name="active"
            :options="[['value' => 1, 'label' => 'Sim'], ['value' => 0, 'label' => 'Nao']]"
        />
        <x-form.error for="active" />
    </div>
</div>
