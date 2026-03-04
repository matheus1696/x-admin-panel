<x-app-layout>
    @php
        $taskOverview = $taskOverview ?? [
            'hubs_total' => 0,
            'total' => 0,
            'overdue' => 0,
            'statuses' => [],
        ];

        $statusTotal = collect($taskOverview['statuses'])->sum('total');
        $statusOffset = 0;
        $statusSegments = [];

        foreach ($taskOverview['statuses'] as $status) {
            if ($statusTotal === 0 || $status['total'] === 0) {
                continue;
            }

            $slice = round(($status['total'] / $statusTotal) * 100, 2);
            $end = min(100, $statusOffset + $slice);
            $statusSegments[] = "{$status['color']} {$statusOffset}% {$end}%";
            $statusOffset = $end;
        }

        $statusChartStyle = $statusSegments !== []
            ? 'background: conic-gradient(' . implode(', ', $statusSegments) . ');'
            : 'background: #e5e7eb;';

    @endphp

    <div class="py-8 lg:py-2 space-y-8 mx-auto px-4 lg:px-6">
        <div class="group relative overflow-hidden rounded-2xl bg-emerald-800 text-white shadow-xl hover:shadow-2xl transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-black/10"></div>
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-white/50 via-white/20 to-white/50"></div>

            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-300 rounded-full blur-3xl"></div>
            </div>

            <div class="relative p-8">
                <div class="flex flex-col lg:flex-row items-center lg:items-start justify-between gap-6">
                    <div class="flex-1 flex items-center gap-4">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div class="lg:col-span-2 xl:col-span-3 space-y-6">
                <div class="bg-white/90 backdrop-blur-sm border border-gray-200/80 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
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

                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
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

                            <a href="{{ route('tasks.index') }}" variant="gray_outline" class="!p-0 !border-0 !bg-transparent hover:!bg-transparent group/action">
                                <div class="w-full p-5 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200/80 hover:border-emerald-300/80 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100/80 rounded-xl flex items-center justify-center mb-3 group-hover/action:scale-110 group-hover/action:-rotate-3 transition-all duration-300">
                                            <i class="fas fa-list-check text-emerald-700 text-xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 group-hover/action:text-emerald-800 transition-colors">Tarefas</p>
                                    </div>
                                </div>
                            </a>

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

                <div>
                    <div class="bg-white/90 backdrop-blur-sm border border-gray-200/80 rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200/80 bg-gradient-to-r from-gray-50/50 via-white to-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-5 bg-gradient-to-b from-emerald-700 to-emerald-900 rounded-full"></div>
                                <h4 class="text-sm font-semibold text-gray-700">Status das Tarefas</h4>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            <div class="flex justify-center">
                                <div class="relative h-44 w-44 rounded-full" style="{{ $statusChartStyle }}">
                                    <div class="absolute inset-6 rounded-full bg-white shadow-inner"></div>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                        <span class="text-3xl font-bold text-gray-900">{{ $taskOverview['total'] }}</span>
                                        <span class="text-xs font-medium uppercase tracking-[0.2em] text-gray-500">Tarefas</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3 text-center">
                                <div class="rounded-xl bg-gray-50 px-3 py-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-gray-500">Hubs</p>
                                    <p class="mt-1 text-lg font-bold text-gray-900">{{ $taskOverview['hubs_total'] }}</p>
                                </div>
                                <div class="rounded-xl bg-yellow-50 px-3 py-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-yellow-700">Atrasadas</p>
                                    <p class="mt-1 text-lg font-bold text-yellow-800">{{ $taskOverview['overdue'] }}</p>
                                </div>
                                <div class="rounded-xl bg-emerald-50 px-3 py-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-emerald-700">Em Dia</p>
                                    <p class="mt-1 text-lg font-bold text-emerald-800">{{ max($taskOverview['total'] - $taskOverview['overdue'], 0) }}</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @forelse($taskOverview['statuses'] as $status)
                                    <div class="flex items-center justify-between gap-3 text-sm">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $status['color'] }}"></span>
                                            <span class="truncate text-gray-600">{{ $status['label'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="font-semibold text-gray-900">{{ $status['total'] }}</span>
                                            <span class="w-10 text-right text-xs text-gray-500">
                                                {{ $statusTotal > 0 ? number_format(($status['total'] / $statusTotal) * 100, 0) : 0 }}%
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-500">
                                        Nenhuma tarefa encontrada nos hubs com acesso.
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="space-y-6">
                <div class="bg-white/90 backdrop-blur-sm border border-gray-200/80 rounded-2xl shadow-lg overflow-hidden">
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

                    <div class="p-3">
                        @if(Auth::user()->password_default)
                            <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-yellow-50 to-yellow-100/80 border border-yellow-200/80 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-yellow-500 to-amber-500"></div>

                                <div class="px-4 py-2">
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
                                                      fullWidth="true" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-3">
                                    <i class="fa-regular fa-bell-slash text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-700">Tudo tranquilo!</p>
                                <p class="text-xs text-gray-500 mt-1">Nenhuma notificação no momento</p>
                            </div>
                        @endif
                    </div>

                    <div class="px-6 py-3 border-t border-gray-200/80 bg-gradient-to-r from-gray-50/50 to-white/50">
                        <x-button href="#" variant="gray_text" class="w-full !text-gray-500 hover:!text-emerald-700 !text-xs flex items-center justify-center gap-1">
                            <i class="fas fa-history text-[10px]"></i>
                            Ver histórico de notificações
                            <i class="fas fa-chevron-right text-[8px]"></i>
                        </x-button>
                    </div>
                </div>

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
