<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Departamento --}}
    <div class="md:col-span-2">
        <x-form.label value="Departamento" />
        <x-form.input
            wire:model.defer="title"
            name="title"
            placeholder="Nome do departamento"
            required
        />
        <x-form.error for="title" />
    </div>

    {{-- Contato --}}
    <div>
        <x-form.label value="Contato" for="contact" />
        <x-form.input type="text" wire:model.defer="contact" id="contact" value="{{ old('contact', $contact ?? '') }}" placeholder="(00) 00000-0000" data-mask="phone" maxlength="15"/>
        <x-form.error for="contact" />
    </div>

    {{-- Ramal --}}
    <div>
        <x-form.label value="Ramal" />
        <x-form.input
            wire:model.defer="extension"
            name="extension"
            placeholder="Ramal"
            maxlength="4"
        />
        <x-form.error for="extension" />
    </div>

    {{-- Tipo de Contato --}}
    <div class="col-span-2">
        <x-form.label value="Tipo de Contato" />
        <x-form.select-livewire
            wire:model.defer="type_contact"
            name="type_contact"
            :collection="collect([
                ['value' => 'Without', 'label' => 'Sem definição'],
                ['value' => 'Internal', 'label' => 'Interno'],
                ['value' => 'Main', 'label' => 'Principal'],
            ])"
            value-field="value"
            label-field="label"
        />
        <x-form.error for="type_contact" />
    </div>

</div>
