<div>
    
    <!-- Flash Message -->
    <x-alert.flash />

    <x-page.header title="Gerenciar Usuários" subtitle="Gerencie os usuários do sistema" icon="fa-solid fa-users">
        <x-slot name="button">
            <x-button text="Novo Usuário" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>
    
    <x-page.filter title="Filtros de Usuários">
        <div class="md:col-span-6">
            <x-form.label value="Nome" />
            <x-form.input type="text" placeholder="Buscar por nome..." wire:model.live.debounce.500ms.debounce.500ms="filters.name"/>
        </div>

        <div class="md:col-span-6">
            <x-form.label value="Email" />
            <x-form.input type="email" placeholder="Buscar por e-mail..." wire:model.live.debounce.500ms.debounce.500ms="filters.email"/>
        </div>

        <div class="md:col-span-4">
            <x-form.label value="Status" />
            <x-form.select-livewire wire:model.live.debounce.500ms="filters.status" name="status" default="Selecione o status"
                :options="[
                    ['value' => 'all', 'label' => 'Todos'],
                    ['value' => true, 'label' => 'Ativo'],
                    ['value' => false, 'label' => 'Inativo'],
                ]"
            />
        </div>

        <div class="md:col-span-4">
            <x-form.label value="Itens por página" />
            <x-form.select-livewire 
                wire:model.live.debounce.500ms="filters.perPage"
                name="filters.perPage"
                :options="[
                    ['value' => 10, 'label' => '10'],
                    ['value' => 25, 'label' => '25'],
                    ['value' => 50, 'label' => '50'],
                    ['value' => 100, 'label' => '100']
                ]"
                default="Selecione a quantidade de itens"
            />
        </div>
    
        <div class="md:col-span-4">
            <x-form.label value="Ordenar por" />                
            <x-form.select-livewire 
                wire:model.live.debounce.500ms="filters.sort"
                name="sort"
                :options="[
                    ['value' => 'name_asc', 'label' => 'Nome (A–Z)'],
                    ['value' => 'name_desc', 'label' => 'Nome (Z–A)'],
                    ['value' => 'email_asc', 'label' => 'Email (A–Z)'],
                    ['value' => 'email_desc', 'label' => 'Email (Z–A)'],
                ]"
            />
        </div>
    </x-page.filter>

    <!-- Table -->
    <x-page.table :pagination="$users">
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Usuário" />
                <x-page.table-th class="hidden lg:table-cell" value="Email" />
                <x-page.table-th class="w-28 text-center" value="Status" />
                <x-page.table-th class="w-28 text-center" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($users as $user)
                <tr>
                    <x-page.table-td>
                        <div class="flex items-center gap-2">
                            <span
                                class="transition-colors" title="{{ $user->name }}">{{ $user->name }}</span>
                            @if ($user->id === auth()->id())
                                <span
                                    class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full font-medium">Você</span>
                            @endif
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="hidden lg:table-cell" :value="$user->email" />
                    <x-page.table-td class="text-center">
                        <div class="text-xs font-medium rounded-full py-0.5 px-1 {{ $user->is_active ? 'bg-green-300 text-green-700' : 'bg-red-300 text-red-700' }}">
                            {{ $user->is_active ? 'Ativo' : 'Desativado' }}
                        </div>
                    </x-page.table-td>
                    <x-page.table-td>
                        @if ($user->id !== auth()->id())
                            <div class="flex items-center justify-center gap-2">
                                <x-button wire:click="status({{ $user->id }})" icon="fa-solid fa-toggle-on" title="Alterar Status" variant="green_text"/>
                                <x-button wire:click="edit({{ $user->id }})" icon="fa-solid fa-pen" title="Editar Usuário" variant="green_text" />
                                @can('user.permissions')
                                    <x-button wire:click="permission({{ $user->id }})" icon="fa-solid fa-key" title="Editar Permissões do Usuário" variant="green_text" />
                                @endcan
                            </div>
                        @endif
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>

    <!-- Modal -->
    <x-modal :show="$showModal" wire:key="user-modal">
        @if ($modalKey === 'modal-form-create-user')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Usuário</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.administration.user._partials.user-form')
                
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-edit-user')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Usuário</h2>
            </x-slot>

            <form wire:submit.prevent="update" class="space-y-4">
                @include('livewire.administration.user._partials.user-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky"/>
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-user-permission')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Usuário</h2>
            </x-slot>

            <form wire:submit.prevent="permissionUpdate" class="space-y-4">
                @include('livewire.administration.user._partials.user-permission')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky"/>
                </div>
            </form>
        @endif
    </x-modal>
</div>