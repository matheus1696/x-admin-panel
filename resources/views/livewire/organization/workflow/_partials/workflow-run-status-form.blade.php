<div class="grid grid-cols-1 gap-4">

    <div>
        <x-form.label value="Título" />
        <x-form.input wire:model.defer="title" placeholder="Em andamento" required />
        <x-form.error for="title" />
    </div>

    <div>
        <x-form.label value="Cor" />
        <x-form.select-livewire
            wire:model.defer="color"
            name="color"
            :collection="collect([
                [
                    'value' => 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                    'label' => 'Cinza (Rascunho)',
                ],
                [
                    'value' => 'bg-blue-100 text-blue-700 hover:bg-blue-200',
                    'label' => 'Azul (Em andamento)',
                ],
                [
                    'value' => 'bg-green-100 text-green-700 hover:bg-green-200',
                    'label' => 'Verde (Concluído)',
                ],
                [
                    'value' => 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
                    'label' => 'Amarelo (Atenção)',
                ],
                [
                    'value' => 'bg-red-100 text-red-700 hover:bg-red-200',
                    'label' => 'Vermelho (Bloqueado)',
                ],
            ])"
            value-field="value"
            label-field="label"
        />
        <x-form.error for="color" />
    </div>

</div>
