<div>
    <x-alert.flash />

    <x-page.header title="Tipos de Produto" subtitle="Gerencie as categorias de produto" icon="fa-solid fa-tags">
        <x-slot name="button">
            <x-button text="Novo Tipo" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>

    <x-page.table>
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Tipo" />
                <x-page.table-th class="hidden lg:table-cell" value="Descricao" />
                <x-page.table-th class="w-24 text-center" value="Acoes" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($productTypes as $productType)
                <tr>
                    <x-page.table-td :value="$productType->title" />
                    <x-page.table-td class="hidden lg:table-cell" :value="$productType->description ?: '-'" />
                    <x-page.table-td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            <x-button wire:click="edit({{ $productType->id }})" icon="fa-solid fa-pen" title="Editar tipo de produto" variant="green_text" />
                        </div>
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <x-page.table-td colspan="3" class="text-center text-sm text-gray-500 py-10">
                        Nenhum tipo de produto cadastrado.
                    </x-page.table-td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

    <x-modal :show="$showModal" wire:key="product-type-modal">
        @if ($modalKey === 'modal-form-create-product-type')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Tipo de Produto</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.administration.product._partials.product-type-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-form-edit-product-type')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Tipo de Produto</h2>
            </x-slot>

            <form wire:submit.prevent="update" class="space-y-4">
                @include('livewire.administration.product._partials.product-type-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky" />
                </div>
            </form>
        @endif
    </x-modal>
</div>

