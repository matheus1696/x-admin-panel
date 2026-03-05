<div>
    <x-alert.flash />

    <x-page.header title="Fornecedores" subtitle="Gerencie o cadastro de fornecedores" icon="fa-solid fa-truck-field">
        <x-slot name="button">
            <x-button text="Novo Fornecedor" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>

    <x-page.table>
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Fornecedor" />
                <x-page.table-th class="hidden lg:table-cell" value="Documento" />
                <x-page.table-th class="hidden lg:table-cell" value="Nome Fantasia" />
                <x-page.table-th class="hidden xl:table-cell" value="Contato" />
                <x-page.table-th class="w-28 text-center" value="Status" />
                <x-page.table-th class="w-24 text-center" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($suppliers as $supplier)
                <tr>
                    <x-page.table-td :value="$supplier->title" />
                    <x-page.table-td class="hidden lg:table-cell" :value="$supplier->document ?: '-'" />
                    <x-page.table-td class="hidden lg:table-cell" :value="$supplier->trade_name ?: '-'" />
                    <x-page.table-td class="hidden xl:table-cell">
                        <div class="flex flex-col">
                            <span>{{ $supplier->email ?: '-' }}</span>
                            <span class="text-xs text-gray-500">{{ $supplier->phone ?: '-' }} | {{ $supplier->phone_secondary ?: '-' }}</span>
                            <span class="text-xs text-gray-500">{{ $supplier->city?->title ?: '-' }} / {{ $supplier->state?->title ?: '-' }}</span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="text-center">
                        <div class="text-xs font-medium rounded-full py-0.5 px-1 {{ $supplier->is_active ? 'bg-green-300 text-green-700' : 'bg-red-300 text-red-700' }}">
                            {{ $supplier->is_active ? 'Ativo' : 'Inativo' }}
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            <x-button wire:click="edit({{ $supplier->id }})" icon="fa-solid fa-pen" title="Editar fornecedor" variant="green_text" />
                        </div>
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <x-page.table-td colspan="6" class="text-center text-sm text-gray-500 py-10">
                        Nenhum fornecedor cadastrado.
                    </x-page.table-td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

    <x-modal :show="$showModal" wire:key="supplier-modal">
        @if ($modalKey === 'modal-form-create-supplier')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Fornecedor</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.administration.supplier._partials.supplier-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-form-edit-supplier')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Fornecedor</h2>
            </x-slot>

            <form wire:submit.prevent="update" class="space-y-4">
                @include('livewire.administration.supplier._partials.supplier-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
