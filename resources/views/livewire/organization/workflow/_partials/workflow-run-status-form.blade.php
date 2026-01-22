<div class="grid grid-cols-3 gap-4">

    <div class="col-span-1">
        <x-form.label value="Título" />
        <x-form.input wire:model.defer="title" placeholder="Em andamento" required />
        <x-form.error :messages="$errors->get('title')" />
    </div>

    <div class="col-span-1">
        <x-form.label value="Código" />
        <x-form.input wire:model.defer="code" placeholder="running" required />
        <x-form.error :messages="$errors->get('code')" />
        <p class="text-xs text-gray-500 mt-1">
            Código interno (único, sem espaços)
        </p>
    </div>

    <div>
        <x-form.label value="Cor" />
        <x-form.select-livewire
            wire:model.defer="color"
            name="color"
            :collection="collect([
                ['value' => 'gray',   'label' => 'Cinza (Rascunho)'],
                ['value' => 'blue',   'label' => 'Azul (Em andamento)'],
                ['value' => 'green',  'label' => 'Verde (Concluído)'],
                ['value' => 'yellow', 'label' => 'Amarelo (Atenção)'],
                ['value' => 'red',    'label' => 'Vermelho (Bloqueado)'],
            ])"
            value-field="value"
            label-field="label"
        />
        <x-form.error :messages="$errors->get('color')" />
    </div>

</div>
