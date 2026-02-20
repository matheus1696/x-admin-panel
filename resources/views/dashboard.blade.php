<x-app-layout>
    <div class="py-8 lg:py-2 space-y-8 mx-auto px-4 lg:px-6">
        
        <!-- Card de Boas-vindas Premium (mantido) -->
        <div class="group relative overflow-hidden rounded-2xl bg-emerald-800 text-white shadow-xl hover:shadow-2xl transition-all duration-500">
            
            <!-- Efeitos decorativos -->
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-black/10"></div>
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-white/50 via-white/20 to-white/50"></div>
            
            <!-- Padrão de fundo sutil -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-300 rounded-full blur-3xl"></div>
            </div>
            
            <!-- Conteúdo principal -->
            <div class="relative p-8">
                <div class="flex flex-col lg:flex-row items-center lg:items-start justify-between gap-6">
                    <div class="flex-1 flex items-center gap-4">
                        <!-- Ícone com glow -->
                        <div class="relative">
                            <div class="absolute inset-0 bg-white/30 rounded-xl blur-lg opacity-50 group-hover:opacity-70 transition-opacity duration-500"></div>
                            <div class="relative w-16 h-16 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500">
                                <i class="fa-solid fa-user text-2xl"></i>
                            </div>
                        </div>
                        
                        <div class="space-y-1">
                            <h1 class="text-3xl font-bold tracking-tight">
                                Olá, <span class="text-white">{{ Auth::user()->name }}</span>!
                            </h1>
                            <p class="text-emerald-100 text-sm flex items-center gap-2">
                                <i class="fas fa-circle-check text-xs animate-pulse"></i>
                                Bem-vindo de volta ao <span class="font-semibold">{{ config('app.name') }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Data e Hora Premium -->
                    <div class="hidden lg:flex lg:flex-col lg:items-end gap-4 lg:gap-2 text-emerald-100 lg:px-4 lg:pt-2"
                         x-data="{ date: '', time: '', 
                            init() {
                                this.update()
                                setInterval(() => this.update(), 1000)
                            },
                            update() {
                                const now = new Date()

                                this.date = now.toLocaleDateString('pt-BR')
                                this.time = now.toLocaleTimeString('pt-BR', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })
                            }
                        }"
                    >
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span class="font-medium" x-text="date"></span>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <i class="fa-solid fa-clock"></i>
                            <span class="font-medium" x-text="time"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            
            <!-- Coluna Principal (Ações Rápidas) -->
            <div class="lg:col-span-2 xl:col-span-3 space-y-6">
                
                <!-- Card de Ações Rápidas (estrutura manual) -->
                <div class="bg-white/90 backdrop-blur-sm border border-gray-200/80 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
                    
                    <!-- Header do Card -->
                    <div class="relative px-6 py-4 border-b border-gray-200/80 bg-gradient-to-r from-gray-50/50 via-white to-gray-50/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-6 bg-gradient-to-b from-emerald-800 to-emerald-800 rounded-full"></div>
                                <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider">
                                    Ações Rápidas
                                </h3>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-medium border border-emerald-200">
                                    5 atalhos
                                </span>
                            </div>
                            <i class="fas fa-bolt text-emerald-800 text-sm"></i>
                        </div>
                    </div>

                    <!-- Body do Card -->
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
                            <!-- Abrir Chamado -->
                            <a href="#" variant="gray_outline" class="!p-0 !border-0 !bg-transparent hover:!bg-transparent group/action">
                                <div class="w-full p-5 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200/80 hover:border-orange-300/80 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100/80 rounded-xl flex items-center justify-center mb-3 group-hover/action:scale-110 group-hover/action:-rotate-3 transition-all duration-300">
                                            <i class="fa-solid fa-ticket text-orange-600 text-xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 group-hover/action:text-orange-700 transition-colors">Abrir Chamado</p>
                                    </div>
                                </div>
                            </a>

                            <!-- Perfil -->
                            <a href="{{ route('profile.edit') }}" variant="gray_outline" class="!p-0 !border-0 !bg-transparent hover:!bg-transparent group/action">
                                <div class="w-full p-5 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200/80 hover:border-emerald-300/80 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100/80 rounded-xl flex items-center justify-center mb-3 group-hover/action:scale-110 group-hover/action:-rotate-3 transition-all duration-300">
                                            <i class="fa-solid fa-user text-emerald-700 text-xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 group-hover/action:text-emerald-800 transition-colors">Perfil</p>
                                    </div>
                                </div>
                            </a>

                            <!-- Lista Telefônica -->
                            <a href="{{ route('public.contacts.index') }}" variant="gray_outline" class="!p-0 !border-0 !bg-transparent hover:!bg-transparent group/action">
                                <div class="w-full p-5 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200/80 hover:border-emerald-300/80 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100/80 rounded-xl flex items-center justify-center mb-3 group-hover/action:scale-110 group-hover/action:-rotate-3 transition-all duration-300">
                                            <i class="fa-solid fa-phone text-emerald-700 text-xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 group-hover/action:text-emerald-800 transition-colors">Lista Telefônica</p>
                                    </div>
                                </div>
                            </a>

                            <!-- Organograma -->
                            <a href="{{ route('chart.index') }}" variant="gray_outline" class="!p-0 !border-0 !bg-transparent hover:!bg-transparent group/action">
                                <div class="w-full p-5 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200/80 hover:border-emerald-300/80 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100/80 rounded-xl flex items-center justify-center mb-3 group-hover/action:scale-110 group-hover/action:-rotate-3 transition-all duration-300">
                                            <i class="fa-solid fa-sitemap text-emerald-700 text-xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 group-hover/action:text-emerald-800 transition-colors">Organograma</p>
                                    </div>
                                </div>
                            </a>

                            <!-- Alterar Senha -->
                            <a href="{{ route('profile.password.edit') }}" variant="gray_outline" class="">
                                <div class="w-full p-5 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200/80 hover:border-yellow-300/80 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100/80 rounded-xl flex items-center justify-center mb-3 group-hover/action:scale-110 group-hover/action:-rotate-3 transition-all duration-300">
                                            <i class="fa-solid fa-key text-yellow-600 text-xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 group-hover/action:text-yellow-700 transition-colors">Alterar Senha</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Espaço para futuros widgets -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Placeholder Atividades -->
                    <div class="bg-white/90 backdrop-blur-sm border border-gray-200/80 rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200/80 bg-gradient-to-r from-gray-50/50 via-white to-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-5 bg-gradient-to-b from-gray-400 to-gray-500 rounded-full"></div>
                                <h4 class="text-sm font-semibold text-gray-700">Atividades Recentes</h4>
                            </div>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="h-8 bg-gray-100 rounded-lg animate-pulse"></div>
                            <div class="h-8 bg-gray-100 rounded-lg animate-pulse"></div>
                            <div class="h-8 bg-gray-100 rounded-lg animate-pulse"></div>
                        </div>
                    </div>

                    <!-- Placeholder Estatísticas -->
                    <div class="bg-white/90 backdrop-blur-sm border border-gray-200/80 rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200/80 bg-gradient-to-r from-gray-50/50 via-white to-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-5 bg-gradient-to-b from-gray-400 to-gray-500 rounded-full"></div>
                                <h4 class="text-sm font-semibold text-gray-700">Estatísticas</h4>
                            </div>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="h-8 bg-gray-100 rounded-lg animate-pulse"></div>
                            <div class="h-8 bg-gray-100 rounded-lg animate-pulse"></div>
                            <div class="h-8 bg-gray-100 rounded-lg animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar (Notificações) -->
            <div class="space-y-6">
                
                <!-- Card de Notificações -->
                <div class="bg-white/90 backdrop-blur-sm border border-gray-200/80 rounded-2xl shadow-lg overflow-hidden">
                    
                    <!-- Header -->
                    <div class="relative px-6 py-4 border-b border-gray-200/80 bg-gradient-to-r from-gray-50/50 via-white to-gray-50/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-6 bg-gradient-to-b from-emerald-800 to-emerald-800 rounded-full"></div>
                                <h3 class="text-base font-bold text-gray-900 uppercase tracking-wider">
                                    Notificações
                                </h3>
                            </div>
                            <div class="relative">
                                <i class="fas fa-bell text-emerald-800 text-sm"></i>
                                @if(Auth::user()->password_default)
                                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-3">
                        @if(Auth::user()->password_default)
                            <!-- Aviso de Senha Padrão -->
                            <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-yellow-50 to-yellow-100/80 border border-yellow-200/80 shadow-sm hover:shadow-md transition-all duration-300">
                                
                                <!-- Efeito decorativo -->
                                <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-yellow-500 to-amber-500"></div>
                                
                                <div class="p-5">
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                                                <i class="fa-solid fa-triangle-exclamation text-white text-sm"></i>
                                            </div>
                                            
                                            <div class="flex-1">
                                                <h4 class="text-sm font-bold text-yellow-800">Atenção à Segurança</h4>
                                            </div>
                                        </div>
                                        
                                        <div class="flex-1 space-y-3">
                                            
                                            <p class="text-xs text-yellow-700 leading-relaxed">
                                                Você ainda está usando a senha padrão do sistema. Por motivos de segurança, recomendamos que altere sua senha imediatamente.
                                            </p>
                                            
                                            <x-button href="{{ route('profile.password.edit') }}" 
                                                      text="Alterar Senha Agora" 
                                                      icon="fa-solid fa-key" 
                                                      variant="yellow_solid" 
                                                      fullWidth="true"
                                                      class="!py-2.5 !text-xs" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Sem notificações -->
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-3">
                                    <i class="fa-regular fa-bell-slash text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-700">Tudo tranquilo!</p>
                                <p class="text-xs text-gray-500 mt-1">Nenhuma notificação no momento</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-6 py-3 border-t border-gray-200/80 bg-gradient-to-r from-gray-50/50 to-white/50">
                        <x-button href="#" variant="gray_text" class="w-full !text-gray-500 hover:!text-emerald-700 !text-xs flex items-center justify-center gap-1">
                            <i class="fas fa-history text-[10px]"></i>
                            Ver histórico de notificações
                            <i class="fas fa-chevron-right text-[8px]"></i>
                        </x-button>
                    </div>
                </div>

                <!-- Card de Informações do Sistema -->
                <div class="bg-white/90 backdrop-blur-sm border border-gray-200/80 rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200/80 bg-gradient-to-r from-gray-50/50 via-white to-gray-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-1 h-5 bg-gradient-to-b from-emerald-800 to-emerald-800 rounded-full"></div>
                            <h4 class="text-sm font-semibold text-gray-700">Sistema</h4>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-3 text-xs">
                        <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Versão:</span>
                            <span class="font-medium text-gray-900 bg-gray-100/80 px-2 py-0.5 rounded-full">{{ config('app.version') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Último acesso:</span>
                            <span class="font-medium text-gray-900">{{ Auth::user()->last_login_at?->format('d/m/Y') ?? 'Primeiro acesso' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-gray-500">Ambiente:</span>
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[8px] font-medium">
                                @if (config('app.debug'))
                                    Desenvolvimento
                                @else
                                    Produção
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>