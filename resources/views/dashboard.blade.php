<x-app-layout>

    <div class="py-6 space-y-6">
        <!-- Card de Boas-vindas -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 text-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <i class="fa-solid fa-user text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Olá, {{ Auth::user()->name }}! 👋</h3>
                                <p class="text-blue-100 text-sm mt-1">
                                    Bem-vindo de volta ao <strong>{{ config('app.name') }}</strong>
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-4 mt-4 text-blue-100 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>Hoje é {{ now()->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-clock"></i>
                                <span>{{ now()->format('H:i') }} • {{ now()->format('T') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="col-span-2">
                <!-- Ações Rápidas -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-3 ml-2">
                        <i class="fa-solid fa-bolt text-blue-600"></i>
                        Ações Rápidas
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="#" class="p-4 bg-gray-50 hover:bg-blue-50 rounded-xl border border-gray-200 hover:border-blue-300 transition-all duration-200 group text-center">
                            <i class="fa-solid fa-user-plus text-blue-600 text-xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <p class="text-sm font-medium text-gray-700">Novo Usuário</p>
                        </a>
                        <a href="#" class="p-4 bg-gray-50 hover:bg-green-50 rounded-xl border border-gray-200 hover:border-green-300 transition-all duration-200 group text-center">
                            <i class="fa-solid fa-ticket text-green-600 text-xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <p class="text-sm font-medium text-gray-700">Abrir Chamado</p>
                        </a>
                        <a href="#" class="p-4 bg-gray-50 hover:bg-purple-50 rounded-xl border border-gray-200 hover:border-purple-300 transition-all duration-200 group text-center">
                            <i class="fa-solid fa-chart-bar text-purple-600 text-xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <p class="text-sm font-medium text-gray-700">Relatórios</p>
                        </a>
                        <a href="#" class="p-4 bg-gray-50 hover:bg-orange-50 rounded-xl border border-gray-200 hover:border-orange-300 transition-all duration-200 group text-center">
                            <i class="fa-solid fa-cog text-orange-600 text-xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <p class="text-sm font-medium text-gray-700">Configurações</p>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Notificações -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-3 ml-2">
                    <i class="fa-solid fa-bell text-blue-600"></i>
                    Notificações
                </h3>
                @if(Auth::user()->password_default)
                    <!-- Aviso de Senha Padrão -->
                    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl shadow-sm p-4">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="size-8 rounded-xl bg-yellow-500 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation text-white text-xs"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-yellow-800">Atenção à Segurança</h3>
                            </div>
                            <div class="px-2 py-1 bg-yellow-500 text-white text-xs rounded-full font-medium">Importante</div>
                        </div>
                        <div>
                            <p class="text-yellow-700 text-xs leading-relaxed">
                                Você ainda está usando a senha padrão do sistema. Por motivos de segurança, recomendamos que altere sua senha imediatamente.
                            </p>
                            <div class="w-full mt-4">
                                <x-button.btn-link href="#" color="yellow" value="Alterar Senha" icon="fa-solid fa-key" />
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        
        </div>
    </div>
</x-app-layout>