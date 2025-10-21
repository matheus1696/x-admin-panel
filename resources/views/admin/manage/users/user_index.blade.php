<x-app-layout>
    <div>
        <div class="flex items-center justify-between px-5">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fa-solid fa-users mr-2 text-blue-600"></i>
                    Gerenciar Usuários
                </h2>
                <p class="text-sm text-gray-600 mt-1">Gerencie os usuários do sistema</p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Search Bar -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           placeholder="Buscar usuários..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 w-64">
                </div>
                
                @can('create-users')
                    <a href="{{ route('users.create') }}" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fa-solid fa-plus"></i>
                        Novo Usuário
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="py-6">
        <!-- Tabela -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header da Tabela -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Lista de Usuários</h3>
                </div>
            </div>

            <!-- Tabela -->
            <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-indigo-600/20 text-xs">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <span>Usuário</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Contato
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Permissões
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                        <tr class="group hover:bg-blue-50/30 transition-all duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-semibold text-sm shadow-lg">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        @if($user->id === auth()->id())
                                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">
                                                {{ $user->name }}
                                            </p>
                                            @if($user->id === auth()->id())
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full font-medium">Você</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500">ID: {{ $user->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $user->email }}</div>
                                <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                    <i class="fa-solid fa-calendar"></i>
                                    {{ $user->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($user->roles as $role)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium 
                                        {{ $role->name === 'admin' ? 'bg-red-100 text-red-700 border border-red-200' : 
                                            ($role->name === 'manager' ? 'bg-orange-100 text-orange-700 border border-orange-200' : 
                                            'bg-green-100 text-green-700 border border-green-200') }}">
                                        @if($role->name === 'admin')
                                        <i class="fa-solid fa-crown text-xs"></i>
                                        @elseif($role->name === 'manager')
                                        <i class="fa-solid fa-user-shield text-xs"></i>
                                        @else
                                        <i class="fa-solid fa-user text-xs"></i>
                                        @endif
                                        {{ $role->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-sm font-medium text-green-700">Ativo</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Desde {{ $user->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @can('edit-users')
                                    <a href="{{ route('users.edit', $user) }}" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all duration-200 group"
                                        title="Editar Usuário">
                                        <i class="fa-solid fa-pen-to-square text-xs group-hover:scale-110 transition-transform"></i>
                                        Editar
                                    </a>
                                    @endcan
                                    
                                    @can('delete-users')
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" 
                                                class="inline"
                                                x-data="{ confirmDelete() { if(confirm('Tem certeza que deseja excluir {{ $user->name }}?')) { $el.submit(); } } }">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    @click="confirmDelete()"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition-all duration-200 group"
                                                    title="Excluir Usuário">
                                                <i class="fa-solid fa-trash text-xs group-hover:scale-110 transition-transform"></i>
                                                Excluir
                                            </button>
                                        </form>
                                        @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 border border-gray-200 cursor-not-allowed"
                                                title="Não é possível excluir sua própria conta">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                            Excluir
                                        </span>
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
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} resultados
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>