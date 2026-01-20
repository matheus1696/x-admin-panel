<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Código --}}
    <div>
        <x-form.label value="Código" />
        <x-form.input wire:model.defer="code" placeholder="Código interno" />
        <x-form.error :messages="$errors->get('code')" />
    </div>

    {{-- Nome --}}
    <div>
        <x-form.label value="Nome do Estabelecimento" />
        <x-form.input wire:model.defer="title" placeholder="Nome do estabelecimento" required />
        <x-form.error :messages="$errors->get('title')" />
    </div>

    {{-- Apelido --}}
    <div class="col-span-2">
        <x-form.label value="Apelido" />
        <x-form.input wire:model.defer="surname" placeholder="Nome curto" />
        <x-form.error :messages="$errors->get('surname')" />
    </div>

    {{-- Endereço --}}
    <div class="md:col-span-2">
        <x-form.label value="Endereço" />
        <x-form.input wire:model.defer="address" placeholder="Rua, avenida, etc" required />
        <x-form.error :messages="$errors->get('address')" />
    </div>

    {{-- Número --}}
    <div>
        <x-form.label value="Número" />
        <x-form.input wire:model.defer="number" placeholder="Nº" required />
        <x-form.error :messages="$errors->get('number')" />
    </div>

    {{-- Bairro --}}
    <div>
        <x-form.label value="Bairro" />
        <x-form.input wire:model.defer="district" placeholder="Bairro" required />
        <x-form.error :messages="$errors->get('district')" />
    </div>

    {{-- Estado --}}
    <div>
        <x-form.label value="Estado" />
        <x-form.select-livewire
            wire:model.defer="state_id"
            name="state_id"
            :collection="$states"
            value-field="id"
            label-field="title"
            placeholder="Selecione o estado"
        />
        <x-form.error :messages="$errors->get('state_id')" />
    </div>

    {{-- Cidade --}}
    <div>
        <x-form.label value="Cidade" />
        <x-form.select-livewire
            wire:model.defer="city_id"
            name="city_id"
            :collection="$cities"
            value-field="id"
            label-field="title"
            placeholder="Selecione a cidade"
        />
        <x-form.error :messages="$errors->get('city_id')" />
    </div>

    {{-- Tipo de Estabelecimento --}}
    <div>
        <x-form.label value="Tipo de Estabelecimento" />
        <x-form.select-livewire
            wire:model.defer="type_establishment_id"
            name="type_establishment_id"
            :collection="$establishmentTypes"
            value-field="id"
            label-field="title"
            placeholder="Selecione o tipo"
        />
        <x-form.error :messages="$errors->get('type_establishment_id')" />
    </div>

    {{-- Bloco Financeiro --}}
    <div>
        <x-form.label value="Bloco Financeiro" />
        <x-form.select-livewire
            wire:model.defer="financial_block_id"
            name="financial_block_id"
            :collection="$financialBlocks"
            value-field="id"
            label-field="title"
            placeholder="Selecione o bloco"
        />
        <x-form.error :messages="$errors->get('financial_block_id')" />
    </div>

    {{-- Latitude --}}
    <div>
        <x-form.label value="Latitude" />
        <x-form.input wire:model.defer="latitude" placeholder="-23.550520" />
        <x-form.error :messages="$errors->get('latitude')" />
    </div>

    {{-- Longitude --}}
    <div>
        <x-form.label value="Longitude" />
        <x-form.input wire:model.defer="longitude" placeholder="-46.633308" />
        <x-form.error :messages="$errors->get('longitude')" />
    </div>

    {{-- Descrição --}}
    <div class="md:col-span-2">
        <x-form.label value="Descrição" />
        <x-form.textarea wire:model.defer="description" rows="3" placeholder="Descrição do estabelecimento" />
        <x-form.error :messages="$errors->get('description')" />
    </div>

</div>
