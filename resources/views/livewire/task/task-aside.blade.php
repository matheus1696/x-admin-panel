<div>
    <!-- HEADER -->
    <header class="p-6 border-b border-gray-200 flex justify-between items-start bg-green-50">
        <div class="flex-1 pr-4">
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-700 flex items-center gap-1.5">
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
                    Atualizado {{ $task->updated_at->diffForHumans() }}
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

        <!-- DESCRIÇÃO -->
        <section class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="flex-1 text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                    <i class="far fa-file-alt text-xs"></i>
                    Descrição
                </h3>
                
                <div>
                    @if(!$isEditingDescription)
                        <x-button wire:click="enableDescriptionEdit" variant="green_outline"  icon="fas fa-edit" />
                    @else
                        <!-- Botões de ação -->
                        <div class="flex justify-end gap-3 pt-2">
                            <x-button wire:click="cancelDescriptionEdit" variant="red_outline" icon="fas fa-times" :disabled="$savingDescription" />
                            
                            <x-button wire:click="saveDescription" variant="green_outline" icon="fas fa-check" :disabled="$savingDescription" />
                        </div>
                    @endif
                </div>
            </div>
            
            @if($isEditingDescription)
                <!-- Modo Edição -->
                <div class="space-y-4" wire:key="description-edit-{{ $task->id }}">
                    <div>
                        <x-form.textarea 
                            wire:model.defer="description"
                            id="task-description-{{ $task->id }}" 
                            rows="6"
                            placeholder="Descreva detalhadamente esta tarefa..."
                            :autofocus="true"
                            wire:keydown.enter.prevent="saveDescription"
                        ></x-form.textarea>
                        
                        <!-- Contador de caracteres -->
                        <div class="flex justify-end mt-2">
                            <span class="text-xs {{ strlen($description ?? '') > 1000 ? 'text-red-500' : 'text-gray-500' }}">
                                {{ strlen($description ?? '') }}/1000 caracteres
                            </span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Modo Visualização -->
                <div class="space-y-3" wire:key="description-view-{{ $task->id }}">
                    <div>
                        
                        @if(trim($task->description))
                            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap text-xs">{{ trim($task->description) }}</p>
                        @else
                            <div class="flex items-center justify-center h-full min-h-[80px] text-gray-400 group">
                                <div class="text-center">
                                    <i class="far fa-file-alt text-2xl mb-2 group-hover:text-green-700 transition-colors"></i>
                                    <p class="text-sm group-hover:text-green-700 transition-colors">
                                        Clique para adicionar uma descrição
                                    </p>
                                    <p class="text-xs mt-1 text-gray-500">
                                        Descreva os detalhes, objetivos e observações desta tarefa
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </section>

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
                                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-700 to-green-700 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                    {{ Str::substr(Auth::user()->name, 0, 2) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-400 rounded-full border-2 border-white flex items-center justify-center">
                                    <i class="fas fa-check text-xs text-white"></i>
                                </div>
                            </div>
                            <div>
                                <span class="block font-semibold text-gray-900">{{ $task->user->name ?? ''}}</span>
                                <span class="text-sm text-gray-500 line-clamp-1">{{ $task->user->occupation->title ?? ''}}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-green-700 hover:text-green-800 text-sm flex items-center gap-1">
                                        <i class="far fa-envelope text-xs"></i>
                                        {{ $task->user->email ?? ''}}
                                    </span>
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
                                <span class="text-gray-600">{{ $task->taskStepsFinished->count() > 0 ? round(($task->taskStepsFinished->count() / $task->taskSteps->count()) * 100, 2) : 0 }}% concluído</span>
                                <span class="font-medium">{{ $task->taskStepsFinished->count() }}/{{ $task->taskSteps->count() }} etapas</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-700 h-2 rounded-full" style="width: {{ $task->taskStepsFinished->count() > 0 ? round(($task->taskStepsFinished->count() / $task->taskSteps->count()) * 100, 2) : 0 }}%"></div>
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

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-700">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div>
                                    <span class="block text-sm text-gray-500">Início</span>
                                    <span class="font-semibold text-gray-900">{{ $task->created_at->format('d/m/Y') ?? '-' }}</span>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">{{ $task->taskStatus->name ?? '-' }}</span>
                        </div>

                        
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-700">
                                    <i class="far fa-calendar-check"></i>
                                </div>
                                <div>
                                    <span class="block text-sm text-gray-500">Prazo final</span>
                                    <span class="font-semibold text-gray-900">{{ $task->deadline_at->format('d/m/Y') ?? '-' }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                <i class="fas fa-clock mr-1"></i>
                                {{ (int) now()->diffInDays($task->deadline_at) }} dias
                            </span>
                        </div>
                    </div>
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
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700">
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
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700">
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
</div>
