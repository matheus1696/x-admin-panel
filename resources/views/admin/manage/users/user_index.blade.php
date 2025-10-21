<x-app-layout>

    <x-page.header icon="fa-solid fa-users" title="Gerenciar Usuários" subtitle="Gerencie os usuários do sistema">
        <x-slot name="button">
            @can('create-users')
                <x-button.link-primary href="{{ route('users.create') }}" value="Novo Usuário">
                    <i class="fa-solid fa-plus"></i>
                    Novo Usuário
                </x-button.link-primary>
            @endcan
        </x-slot>
    </x-page.header>

    <div class="py-6 w-full overflow-x-auto">

        <x-page.filter title="Filtros de Usuários">
            {{-- Filtros Básicos --}}
            <x-slot name="showBasic">
                <!-- Nome -->
                <div class="md:col-span-4">
                    <x-form.label value="Nome" />
                    <x-form.input type="text" name="name" placeholder="Buscar por nome..." value="{{ request('name') }}"/>
                </div>

                <!-- E-mail -->
                <div class="md:col-span-4">
                    <x-form.label value="Email" />
                    <x-form.input type="email" name="email" placeholder="Buscar por e-mail..." value="{{ request('email') }}"/>
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <x-form.label value="Status" />
                    <x-form.select name="status">
                        <option value="">Todos</option>
                        <option value="active" @selected(request('status') === 'active')>Ativo</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inativo</option>
                    </x-form.select>
                </div>

                <!-- Itens por página -->
                <div class="md:col-span-2">
                    <x-form.label value="Itens por página" />
                    <x-form.select name="per_page">
                        <option value="10" @selected(request('per_page') == 10)>10</option>
                        <option value="25" @selected(request('per_page') == 25)>25</option>
                        <option value="50" @selected(request('per_page') == 50)>50</option>
                        <option value="100" @selected(request('per_page') == 100)>100</option>
                    </x-form.select>
                </div>
            </x-slot>

            {{-- Filtros Avançados --}}
            <x-slot name="showAdvanced">
                
                <!-- Ordenação -->
                <div class="md:col-span-2">
                    <x-form.label value="Ordenar por" />
                    <x-form.select name="sort">
                        <option value="name_asc" @selected(request('sort') === 'name_asc')>Nome (A–Z)</option>
                        <option value="name_desc" @selected(request('sort') === 'name_desc')>Nome (Z–A)</option>
                    </x-form.select>
                </div>

                <!-- Intervalo de datas -->
                <div class="md:col-span-4">
                    <x-form.label value="Criado entre" />
                    <div class="flex gap-2">
                        <x-form.input type="date" name="date_start" value="{{ request('date_start') }}" />
                        <x-form.input type="date" name="date_end" value="{{ request('date_end') }}" />
                    </div>
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
                                    class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $user->name }}</span>
                                @if ($user->id === auth()->id())
                                    <span
                                        class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full font-medium">Você</span>
                                @endif
                            </div>
                        </x-page.table-td>
                        <x-page.table-td class="hidden lg:table-cell" :value="$user->email" />
                        <x-page.table-td>
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-green-700">Ativo</span>
                            </div>
                        </x-page.table-td>
                        <x-page.table-td>
                            <div class="flex items-center justify-center gap-2">
                                @can('edit-users')
                                    <x-button.btn-table color="blue" title="Editar Usuário">
                                        <a href="{{ route('users.edit', $user) }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </x-button.btn-table>
                                @endcan

                                @can('delete-users')
                                    @if ($user->id !== auth()->id())
                                        <x-modal>
                                            {{-- Trigger button --}}
                                            <x-slot name="button">
                                                <x-button.btn-table color="red" title="Excluir Usuário">
                                                    <i class="fa-solid fa-trash"></i>
                                                </x-button.btn-table>
                                            </x-slot>

                                            {{-- Title --}}
                                            <x-slot name="title">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                                                        <i
                                                            class="fa-solid fa-triangle-exclamation text-red-600 text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-lg font-semibold text-gray-900">Exclusão de
                                                            Usuário</h3>
                                                        <p class="text-sm text-gray-500 mt-1">Ação irreversível</p>
                                                    </div>
                                                </div>
                                            </x-slot>

                                            {{-- Body --}}
                                            <x-slot name="body">
                                                <div class="py-1">
                                                    <div
                                                        class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                        <div
                                                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-semibold text-sm">
                                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <p class="font-semibold text-gray-900">
                                                                {{ $user->name }}</p>
                                                            <p class="text-sm text-gray-500">{{ $user->email }}
                                                            </p>
                                                            <div class="flex gap-1 mt-1">
                                                                @foreach ($user->roles as $role)
                                                                    <span
                                                                        class="px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-700">
                                                                        {{ $role->name }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </x-slot>

                                            {{-- Footer --}}
                                            <x-slot name="footer">
                                                <div class="flex items-center justify-between w-full">
                                                    <div class="text-sm text-gray-500 flex items-center gap-2">
                                                        <i class="fa-solid fa-shield-halved"></i>
                                                        <span>Ação requer confirmação</span>
                                                    </div>

                                                    <div class="flex items-center gap-3">

                                                        <form action="{{ route('users.destroy', $user) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                                Confirmar Exclusão
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </x-slot>
                                        </x-modal>
                                    @else
                                        <x-button.btn-table color="gray"
                                            title="Não é possível excluir sua própria conta" disabled>
                                            <i class="fa-solid fa-trash"></i>
                                        </x-button.btn-table>
                                    @endif
                                @endcan
                            </div>
                        </x-page.table-td>
                    </tr>
                @endforeach
            </x-slot>
        </x-page.table>
    </div>
</x-app-layout>
