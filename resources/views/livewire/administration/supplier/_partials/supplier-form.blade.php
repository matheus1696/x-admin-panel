<div class="grid grid-cols-1 gap-4 md:grid-cols-12">
    <div class="md:col-span-8">
        <x-form.label value="Fornecedor" />
        <x-form.input type="text" wire:model.defer="title" placeholder="Nome do fornecedor" />
        <x-form.error for="title" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="Nome Fantasia" />
        <x-form.input type="text" wire:model.defer="trade_name" placeholder="Nome fantasia" />
        <x-form.error for="trade_name" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="Documento" />
        <x-form.input type="text" wire:model.defer="document" placeholder="CPF ou CNPJ" data-mask="cpfCnpj" maxlength="18" />
        <x-form.error for="document" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="Telefone" />
        <x-form.input type="text" wire:model.defer="phone" placeholder="(00) 00000-0000" data-mask="phone" maxlength="15" />
        <x-form.error for="phone" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="Telefone 2" />
        <x-form.input type="text" wire:model.defer="phone_secondary" placeholder="(00) 00000-0000" data-mask="phone" maxlength="15" />
        <x-form.error for="phone_secondary" />
    </div>

    <div class="md:col-span-12">
        <x-form.label value="E-mail" />
        <x-form.input type="email" wire:model.defer="email" placeholder="fornecedor@empresa.com" />
        <x-form.error for="email" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="CEP" />
        <x-form.input type="text" wire:model.defer="address_zipcode" placeholder="00000-000" data-mask="cep" maxlength="9" />
        <x-form.error for="address_zipcode" />
    </div>

    <div class="md:col-span-2 flex items-end">
        <x-button
            type="button"
            text="Buscar CEP"
            icon="fa-solid fa-magnifying-glass-location"
            variant="blue_outline"
            class="w-full"
            wire:click="searchCep"
            wire:loading.attr="disabled"
            wire:target="searchCep"
        />
    </div>

    <div class="md:col-span-6">
        <x-form.label value="Logradouro" />
        <x-form.input type="text" wire:model.defer="address_street" placeholder="Rua, avenida, etc." />
        <x-form.error for="address_street" />
    </div>

    <div class="md:col-span-3">
        <x-form.label value="Numero" />
        <x-form.input type="text" wire:model.defer="address_number" placeholder="N" />
        <x-form.error for="address_number" />
    </div>

    <div class="md:col-span-3">
        <x-form.label value="Bairro" />
        <x-form.input type="text" wire:model.defer="address_district" placeholder="Bairro" />
        <x-form.error for="address_district" />
    </div>

    <div class="md:col-span-4">
        <x-form.label value="Estado" />
        <x-form.select-livewire
            wire:model.live="state_id"
            name="state_id"
            default="Selecione o estado"
            :options="collect($states)->map(fn ($state) => ['value' => $state->id, 'label' => $state->title])->values()->all()"
        />
        <x-form.error for="state_id" />
    </div>

    @if ($state_id)
        <div class="md:col-span-5" wire:key="supplier-city-select-{{ $state_id }}">
            <x-form.label value="Cidade" />
            <x-form.select-livewire
                wire:model.live="city_id"
                name="city_id"
                default="Selecione a cidade"
                :options="collect($cities)->map(fn ($city) => ['value' => $city->id, 'label' => $city->title])->values()->all()"
            />
            <x-form.error for="city_id" />
        </div>
    @else
        <div class="md:col-span-5">
            <x-form.label value="Cidade" />
            <x-form.input type="text" value="" placeholder="Selecione o estado primeiro" disabled />
        </div>
    @endif

    <div class="md:col-span-3">
        <x-form.label value="Status" />
        <x-form.select-livewire
            wire:model.defer="is_active"
            name="is_active"
            :options="[
                ['value' => true, 'label' => 'Ativo'],
                ['value' => false, 'label' => 'Inativo'],
            ]"
        />
        <x-form.error for="is_active" />
    </div>
</div>
