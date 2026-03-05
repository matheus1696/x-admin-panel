<div>
    <x-alert.flash />

    <x-page.header title="Unidades de Medida" subtitle="Gerencie unidades para base de calculo dos produtos" icon="fa-solid fa-ruler-combined">
        <x-slot name="button">
            <x-button text="Nova Unidade" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>

    <x-page.table>
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="w-36" value="Sigla" />
                <x-page.table-th value="Titulo" />
                <x-page.table-th class="w-40 text-center" value="Base de calculo" />
                <x-page.table-th class="w-24 text-center" value="Acoes" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($measureUnits as $measureUnit)
                <tr>
                    <x-page.table-td :value="$measureUnit->acronym" />
                    <x-page.table-td :value="$measureUnit->title" />
                    <x-page.table-td class="text-center" :value="$measureUnit->base_quantity" />
                    <x-page.table-td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            <x-button wire:click="edit({{ $measureUnit->id }})" icon="fa-solid fa-pen" title="Editar unidade de medida" variant="green_text" />
                        </div>
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <x-page.table-td colspan="4" class="py-10 text-center text-sm text-gray-500">
                        Nenhuma unidade de medida cadastrada.
                    </x-page.table-td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

    <x-modal :show="$showModal" wire:key="product-measure-unit-modal">
        @if ($modalKey === 'modal-form-create-product-measure-unit')
            <x-slot name="header">
                <h2 class="text-sm font-semibold uppercase text-gray-700">Cadastrar Unidade de Medida</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.administration.product._partials.product-measure-unit-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-form-edit-product-measure-unit')
            <x-slot name="header">
                <h2 class="text-sm font-semibold uppercase text-gray-700">Editar Unidade de Medida</h2>
            </x-slot>

            <form wire:submit.prevent="update" class="space-y-4">
                @include('livewire.administration.product._partials.product-measure-unit-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky" />
                </div>
            </form>
        @endif
    </x-modal>
</div>

