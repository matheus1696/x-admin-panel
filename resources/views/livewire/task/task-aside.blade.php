<div>
    <!-- HEADER -->
<header class="p-6 border-b border-gray-200 flex justify-between items-start bg-green-50">
    <div class="flex-1 pr-4">
        <div class="flex items-center gap-3 mb-3">
            <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 flex items-center gap-1.5">
                <i class="fas fa-tag text-xs"></i>
                {{ $task->category->title ?? 'Sem categoria' }}
            </span>
            <span class="px-3 py-1.5 text-xs font-semibold rounded-full flex items-center gap-1.5 {!! $task->priority->color ?? 'bg-gray-100 text-gray-700' !!}">
                <i class="fas fa-exclamation-circle text-xs"></i>
                {{ $task->priority->title ?? 'Sem prioridade' }}
            </span>
            <span class="px-3 py-1.5 text-xs font-semibold rounded-full flex items-center gap-1.5 {!! $task->taskStatus->color ?? 'bg-gray-100 text-gray-700' !!}">
                <i class="fas fa-play-circle text-xs"></i>
                {{ $task->taskStatus->title ?? 'Sem status' }}
            </span>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-3">
            {{ $task->code }} - {{ $task->title }}
        </h1>
        
        <div class="flex items-center gap-3 text-sm text-gray-500">
            <span class="flex items-center gap-1.5">
                <i class="fas fa-hashtag text-xs"></i>
                {{ $task->code }}
            </span>
            <span class="text-gray-300">•</span>
            <span class="flex items-center gap-1.5">
                <i class="far fa-calendar-plus text-xs"></i>
                Criado em {{ $task->created_at->format('d/m/Y') }}
            </span>
            <span class="text-gray-300">•</span>
            <span class="flex items-center gap-1.5">
                <i class="far fa-clock text-xs"></i>
                @php
                    $diffInDays = $task->updated_at->diffInDays(now());
                    $diffInHours = $task->updated_at->diffInHours(now());
                @endphp

                @if($diffInHours < 1)
                    Atualizado agora
                @elseif($diffInHours < 24)
                    Atualizado há {{ (int) $diffInHours }} {{ Str::plural('hora', $diffInHours) }}
                @else
                    Atualizado há {{ (int) $diffInDays }} {{ Str::plural('dia', $diffInDays) }}
                @endif
            </span>
        </div>
    </div>

    <button @click="openAsideTask = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full px-4 py-2 transition-colors"
        aria-label="Fechar detalhes"
    >
        <i class="fas fa-times text-lg"></i>
    </button>
</header>

<!-- CONTENT -->
<div class="flex-1 overflow-y-auto">

    <!-- RESPONSÁVEL E DATAS PRINCIPAIS -->
    <section class="p-6 border-b border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Responsável -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-user text-xs"></i>
                        Responsável
                    </h3>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                JS
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-400 rounded-full border-2 border-white flex items-center justify-center">
                                <i class="fas fa-check text-xs text-white"></i>
                            </div>
                        </div>
                        <div>
                            <span class="block font-semibold text-gray-900">João Silva</span>
                            <span class="text-sm text-gray-500">Desenvolvedor Sênior</span>
                            <div class="flex items-center gap-2 mt-1">
                                <a href="mailto:joao@empresa.com" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                    <i class="far fa-envelope text-xs"></i>
                                    Email
                                </a>
                                <span class="text-gray-300">•</span>
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                    <i class="far fa-comment text-xs"></i>
                                    Mensagem
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progresso -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <i class="fas fa-chart-line text-xs"></i>
                        Progresso
                    </h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">75% concluído</span>
                            <span class="font-medium">3/4 etapas</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datas importantes -->
            <div class="space-y-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="far fa-calendar-alt text-xs"></i>
                    Datas importantes
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="far fa-calendar-check"></i>
                            </div>
                            <div>
                                <span class="block text-sm text-gray-500">Prazo final</span>
                                <span class="font-semibold text-gray-900">25/01/2026</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                            <i class="fas fa-clock mr-1"></i>
                            5 dias
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fas fa-play"></i>
                            </div>
                            <div>
                                <span class="block text-sm text-gray-500">Início</span>
                                <span class="font-semibold text-gray-900">20/01/2026</span>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500">Em andamento</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DESCRIÇÃO -->
    <section class="p-6 border-b border-gray-100">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="far fa-file-alt text-xs"></i>
            Descrição
        </h3>
        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
            <p class="text-gray-700 leading-relaxed">
                Descrição detalhada da tarefa, explicando contexto,
                objetivos e observações importantes. Pode incluir
                instruções específicas, links relevantes ou qualquer
                informação necessária para a execução da tarefa.
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm flex items-center gap-2">
                    <i class="fas fa-link text-xs"></i>
                    Documentação técnica
                </span>
                <span class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-sm flex items-center gap-2">
                    <i class="fas fa-code-branch text-xs"></i>
                    Branch: feature/bugfix-123
                </span>
            </div>
        </div>
    </section>

    <!-- DATAS DETALHADAS -->
    <section class="p-6">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="far fa-clock text-xs"></i>
            Linha do tempo
        </h3>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                        <i class="fas fa-plus text-xs"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-900">Tarefa criada</span>
                        <span class="text-xs text-gray-500">Por: Sistema</span>
                    </div>
                </div>
                <span class="text-sm text-gray-500">10/01/2026 • 14:30</span>
            </div>

            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <i class="fas fa-user-check text-xs"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-900">Atribuída a João Silva</span>
                        <span class="text-xs text-gray-500">Por: Maria Souza</span>
                    </div>
                </div>
                <span class="text-sm text-gray-500">12/01/2026 • 09:15</span>
            </div>

            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                        <i class="fas fa-play text-xs"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-900">Status: Em andamento</span>
                        <span class="text-xs text-gray-500">Por: João Silva</span>
                    </div>
                </div>
                <span class="text-sm text-gray-500">20/01/2026 • 08:00</span>
            </div>

            <div class="flex justify-between items-center py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600">
                        <i class="far fa-clock text-xs"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-900">Última atualização</span>
                        <span class="text-xs text-gray-500">Status alterado</span>
                    </div>
                </div>
                <span class="text-sm text-gray-500">15/01/2026 • 16:45</span>
            </div>
        </div>
    </section>

</div>

<!-- FOOTER -->
<footer class="p-4 border-t border-gray-200 bg-gray-50">
    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-500">
            <span class="flex items-center gap-2">
                <i class="far fa-edit text-xs"></i>
                Última edição: 15/01/2026
            </span>
        </div>
        <div class="flex gap-3">
            <button class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg flex items-center gap-2">
                <i class="far fa-edit"></i>
                Editar
            </button>
            <button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg flex items-center gap-2">
                <i class="fas fa-check"></i>
                Concluir
            </button>
        </div>
    </div>
</footer>
</div>
