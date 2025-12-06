<div>
    <x-page.filter title="Filtros de Usuários">
        {{-- Filtros Básicos --}}
        <x-slot name="showBasic">
            <div class="md:col-span-4">
                <x-form.label value="Nome" />
                <x-form.input type="text" placeholder="Buscar por nome..." wire:model.live.debounce.500ms="name"/>
            </div>

            <div class="md:col-span-4">
                <x-form.label value="Email" />
                <x-form.input type="email" placeholder="Buscar por e-mail..." wire:model.live.debounce.500ms="email"/>
            </div>

            <div class="md:col-span-2">
                <x-form.label value="Status" />
                <x-form.select-livewire wire:model.live="status" name="status" default="Selecione o status"
                    :options="[
                        ['value' => '', 'label' => 'Todos'],
                        ['value' => 'true', 'label' => 'Ativo'],
                        ['value' => 'false', 'label' => 'Inativo'],
                    ]"
                />
            </div>

            <div class="md:col-span-2">
                <x-form.label value="Itens por página" />
                <x-form.select-livewire 
                    wire:model.live="perPage"
                    name="perPage"
                    :options="[
                        ['value' => 10, 'label' => '10'],
                        ['value' => 25, 'label' => '25'],
                        ['value' => 50, 'label' => '50'],
                        ['value' => 100, 'label' => '100']
                    ]"
                    default="Selecione a quantidade de itens"
                />
            </div>
        </x-slot>

        {{-- Filtros Avançados --}}
        <x-slot name="showAdvanced">
            <div class="md:col-span-2">
                <x-form.label value="Ordenar por" />                
                <x-form.select-livewire 
                    wire:model.live="sort"
                    name="sort"
                    :options="[
                        ['value' => 'name_asc', 'label' => 'Nome (A–Z)'],
                        ['value' => 'name_desc', 'label' => 'Nome (Z–A)'],
                        ['value' => 'email_asc', 'label' => 'Email (A–Z)'],
                        ['value' => 'email_desc', 'label' => 'Email (Z–A)'],
                    ]"
                />
            </div>
        </x-slot>
    </x-page.filter>

    <x-page.table :pagination="$users">
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Usuário" />
                <x-page.table-th class="hidden lg:table-cell" value="Email" />
                <x-page.table-th class="w-36 text-center" value="Status" />
                <x-page.table-th class="w-36 text-center" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($users as $user)
                <tr>
                    <x-page.table-td>
                        <div class="flex items-center gap-2">
                            <span
                                class="font-semibold text-gray-900 group-hover:text-green-700 transition-colors">{{ $user->name }}</span>
                            @if ($user->id === auth()->id())
                                <span
                                    class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full font-medium">Você</span>
                            @endif
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="hidden lg:table-cell" :value="$user->email" />
                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            @if ($user->status)
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-green-700">Ativo</span>
                            @else
                                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-red-700">Inativo</span>
                            @endif
                        </div>
                    </x-page.table-td>
                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            @can('edit-users')
                                <x-button.btn-table title="Editar Usuário">
                                    <a href="{{ route('users.edit', $user) }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </x-button.btn-table>
                            @endcan
                            @can('permission-users')                            
                                <x-button.btn-table title="Editar Usuário">
                                    <a href="{{ route('users.permission.edit', $user) }}">
                                        <i class="fa-solid fa-lock"></i>
                                    </a>
                                </x-button.btn-table>
                            @endcan                            
                            @can('password-users')
                                <x-modal title="Redefinir Senha">
                                    <x-slot name="button">
                                        <x-button.btn-table title="Redefinir Senha do Usuário">
                                            <i class="fa-solid fa-key"></i>
                                        </x-button.btn-table>
                                    </x-slot>

                                    <x-slot name="body">
                                        <!-- Mensagem de confirmação -->
                                        <div class="space-y-4 py-5">
                                            <h3 class="text-lg font-semibold text-gray-900"></h3>
                                            <p class="text-gray-600 text-sm text-center">
                                                Tem certeza que deseja redefinir a senha do usuário 
                                                <strong class="text-green-700">{{ $user->name }}</strong>?
                                            </p>
                                            <p class="text-xs text-center text-gray-500 bg-yellow-50 p-3 rounded border border-yellow-300">
                                                <i class="fa-solid fa-info-circle mr-1 text-yellow-500"></i>
                                                A nova senha será: <code class="font-mono bg-gray-100 px-1 rounded">Senha123</code>
                                            </p>
                                        </div>

                                        <!-- Botões de ação -->
                                        <div class="flex gap-3 justify-center">
                                            <button type="button" 
                                                    @click="isModalOpen = false"
                                                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                                Cancelar
                                            </button>
                                            <form action="{{route('users.password', $user) }}" method="post">
                                                @csrf @method('PATCH')
                                                <x-button.btn-submit value="Confirmar Alteração">
                                                    <i class="fa-solid fa-key mr-2"></i>
                                                    Confirmar Alteração
                                                </x-button.btn-submit>
                                            </form>                                            
                                        </div>
                                    </x-slot>
                                </x-modal>
                            @endcan
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
