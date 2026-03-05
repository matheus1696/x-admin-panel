<div>
    <x-alert.flash />

    <x-page.header title="Produtos" subtitle="Gerencie o cadastro de produtos/equipamentos" icon="fa-solid fa-box-open">
        <x-slot name="button">
            <x-button text="Novo Produto" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>

    <x-page.table>
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="w-36" value="Codigo" />
                <x-page.table-th class="w-40" value="SKU" />
                <x-page.table-th value="Titulo" />
                <x-page.table-th class="w-48" value="Tipo" />
                <x-page.table-th class="w-44" value="Area" />
                <x-page.table-th class="w-40" value="Natureza" />
                <x-page.table-th class="hidden lg:table-cell" value="Descricao" />
                <x-page.table-th class="w-24 text-center" value="Acoes" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($products as $product)
                <tr>
                    <x-page.table-td :value="$product->code ?: '-'" />
                    <x-page.table-td :value="$product->sku ?: '-'" />
                    <x-page.table-td :value="$product->title" />
                    <x-page.table-td :value="$product->type?->title ?: '-'" />
                    <x-page.table-td :value="$product->department?->name ?: '-'" />
                    <x-page.table-td :value="$product->nature === 'SUPPLY' ? 'Suprimento' : 'Ativo'" />
                    <x-page.table-td class="hidden lg:table-cell" :value="$product->description ?: '-'" />
                    <x-page.table-td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            <x-button wire:click="edit({{ $product->id }})" icon="fa-solid fa-pen" title="Editar produto" variant="green_text" />
                        </div>
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <x-page.table-td colspan="8" class="text-center text-sm text-gray-500 py-10">
                        Nenhum produto cadastrado.
                    </x-page.table-td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

    <x-modal :show="$showModal" wire:key="product-modal">
        @if ($modalKey === 'modal-form-create-product')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Produto</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.administration.product._partials.product-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-form-edit-product')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Produto</h2>
            </x-slot>

            <form wire:submit.prevent="update" class="space-y-4">
                @include('livewire.administration.product._partials.product-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
