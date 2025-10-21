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

<!-- 🔍 Filtros de Usuários -->
<div x-data="{ openAccordion: false }" class="mb-8 bg-white rounded-2xl shadow-sm border border-blue-200 overflow-hidden"
>
    <!-- Cabeçalho -->
    <div class="px-6 py-4 border-b border-blue-100 bg-gray-50/70 backdrop-blur-sm flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center shadow-inner">
                <i class="fa-solid fa-filter text-blue-600 text-lg"></i>
            </div>
            <div class="hidden lg:block">
                <h3 class="text-base font-semibold text-gray-900">Filtros de Usuários</h3>
                <p class="text-xs text-gray-600 mt-1">Filtre e refine sua busca conforme sua necessidade</p>
            </div>
        </div>
        <button @click="openAccordion = !openAccordion" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all duration-200 group">
            <i class="fa-solid fa-sliders text-xs transition-transform duration-300" 
               :class="{ 'rotate-90': openAccordion }"></i>
            <span x-text="openAccordion ? 'Ocultar Filtros' : 'Filtros Avançados'"></span>
        </button>
    </div>

    <!-- Conteúdo Principal -->
    <div class="p-6 space-y-6">
        <!-- Filtros principais -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Usuário -->
            <div>
                <x-form.label icon="fa-solid fa-user" value="Usuário" for="user_search"/>
                <x-form.input type="text" id="user_search" placeholder="Buscar por nome..." />
            </div>

            <!-- Email -->
            <div class="hidden lg:block">
                <x-form.label icon="fa-solid fa-envelope" value="Email" for="user_email"/>
                <x-form.input type="text" id="user_email" placeholder="Buscar por email..." />
            </div>

            <!-- Status -->
            <div>
                <x-form.label icon="fa-solid fa-circle-check" value="Status" for="user_status"/>
                <x-form.select id="user_status">
                    <option value="">Todos os status</option>
                    <option value="active">🟢 Ativo</option>
                    <option value="inactive">🔴 Inativo</option>
                    <option value="pending">🟡 Pendente</option>
                </x-form.select>
            </div>

            <!-- Botão Aplicar -->
            <div class="flex items-end">
                <x-button.btn icon="fa-solid fa-magnifying-glass" value="Aplicar Filtros" class="w-full"/>
            </div>
        </div>

        <!-- 🎛️ Filtros Avançados -->
        <div 
            x-show="openAccordion" 
            x-collapse 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="pt-6 border-t border-gray-100"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Data de Criação -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-calendar text-blue-600 text-sm"></i>
                        Data de Criação
                    </label>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">De</label>
                            <input type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Até</label>
                            <input type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                </div>

                <!-- Tipo de Usuário -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-users text-blue-600 text-sm"></i>
                        Tipo de Usuário
                    </label>
                    <select class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 cursor-pointer">
                        <option value="">Todos os tipos</option>
                        <option value="admin">Administrador</option>
                        <option value="manager">Gerente</option>
                        <option value="user">Usuário</option>
                    </select>
                </div>

                <!-- Ordenação -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-sort text-blue-600 text-sm"></i>
                        Ordenar por
                    </label>
                    <select class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 cursor-pointer">
                        <option value="name_asc">Nome (A-Z)</option>
                        <option value="name_desc">Nome (Z-A)</option>
                        <option value="newest">Mais recentes</option>
                        <option value="oldest">Mais antigos</option>
                    </select>
                </div>

                <!-- Itens por Página -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-list text-blue-600 text-sm"></i>
                        Itens por página
                    </label>
                    <select class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 cursor-pointer">
                        <option value="10">10 itens</option>
                        <option value="25">25 itens</option>
                        <option value="50">50 itens</option>
                        <option value="100">100 itens</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Tabela -->
        <div class="overflow-hidden">
            <!-- Tabela -->
            <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-blue-600/20 text-sm">
                <table class="w-full divide-y divide-gray-200 table-fixed overflow-x-auto">
                    <thead class="bg-blue-50/80 text-blue-800 text-left font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Usuário</th>
                            <th class="hidden lg:table-cell">Email</th>
                            <th class="w-36 text-center">Status</th>
                            <th class="w-32 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            <tr class="group hover:bg-blue-50/30 transition-all duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $user->name }}</span>
                                        @if ($user->id === auth()->id())
                                            <span
                                                class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full font-medium">Você</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                        <span class="text-sm font-medium text-green-700">Ativo</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer da Tabela -->
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="w-full flex items-center justify-center gap-2">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
