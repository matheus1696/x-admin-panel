<div x-data="taskAside({{ $task->id }})" class="h-full flex flex-col bg-gradient-to-br from-white via-green-50/30 to-white" x-ref="asideContainer"
>

    <!-- HEADER - Sticky com efeito blur -->
    <header class="sticky top-0 z-30 p-5 border-b border-green-100/50 bg-white/90 backdrop-blur-md supports-backdrop-blur:bg-white/60 shadow-sm glow" >
        <div class="flex items-start justify-between gap-4">
            <!-- Conteúdo Principal -->
            <div class="flex-1 min-w-0 space-y-3">
                <!-- Badges Interativas -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Badge Prioridade -->
                    <span 
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-full {!! $task->taskPriority->color_code_tailwind ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700' !!} shadow-sm"
                    >
                        <i class="fas fa-exclamation-circle text-xs bounce-subtle"></i>
                        {{ $task->taskPriority->title ?? 'Sem prioridade' }}
                    </span>

                    <!-- Badge Status -->
                    <span 
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-full {!! $task->taskStatus->color_code_tailwind ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700' !!} shadow-sm"
                    >
                        <i class="fas fa-play-circle text-xs"></i>
                        {{ $task->taskStatus->title ?? 'Sem status' }}
                    </span>
                </div>

                <!-- Título com efeito gradiente -->
                <h1 class="text-xl font-bold text-gray-900 leading-tight">
                    <span class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                        {{ $task->code }}
                    </span>
                    <span class="text-gray-700">-</span>
                    <span class="text-gray-800">{{ $task->title }}</span>
                </h1>

                <!-- Timeline Interativa -->
                <div class="flex items-center gap-3 text-sm">                    
                    <!-- Data de criação -->
                    <div>
                        <span class="text-gray-600 flex items-center gap-1.5">
                            <i class="far fa-calendar text-xs"></i>
                            {{ $task->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    
                    <span class="text-gray-300">•</span>
                    
                    <!-- Atualização recente -->
                    <div class="flex items-center gap-1.5 text-gray-600" >
                        <i class="far fa-clock text-xs"></i>
                        <span>{{ $task->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Botão Fechar com Animação -->
            <button
                @click="activeItem = null"
                class="text-gray-400 hover:text-gray-600"
                aria-label="Fechar painel de detalhes"
            >
                <!-- Ícone com rotação -->
                <div class="hover:bg-gray-200 px-4 py-2 rounded-full text-center  transition-all duration-300 hover:rotate-90">
                    <i class="fas fa-times text-lg transition-transform duration-300 group-hover:rotate-90"></i>
                </div>
            </button>
        </div>
    </header>

    <!-- CONTENT - Scroll suave com efeitos -->
    <div class="flex-1 overflow-y-auto" >
        <!-- RESPONSÁVEIS - Cards interativos -->
        <section class="p-6 border-b border-gray-100">
            <div class="grid grid-cols-1 gap-6">
                <!-- Card Responsável -->
                <div class="grid grid-cols-2 items-center justify-between gap-2 bg-gradient-to-br from-white to-blue-50 rounded-xl border border-blue-100 p-5 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="px-4 py-2 bg-blue-100 rounded-lg">
                                <i class="fas fa-user-tie text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Solicitante
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">Solicitante da Demanda</p>
                            </div>
                        </div>
                    </div>
                    
                    @if ($task->responsable)
                        <!-- Avatar e Informações -->
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    {{ substr($task->responsable->name ?? '', 0, 1) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                                    <i class="fas fa-check text-white text-[8px]"></i>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-gray-900 line-clamp-1" title="{{ $task->responsable->name ?? '' }}">
                                    {{ $task->responsable->name ?? '' }}
                                </div>
                                <div class="text-xs text-gray-500 line-clamp-1" title="{{ $task->responsable->occupation->title ?? 'Sem cargo' }}">
                                    {{ $task->responsable->occupation->title ?? 'Sem cargo' }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-5 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 mb-3">
                                <i class="fas fa-user-plus text-blue-400"></i>
                            </div>
                            <p class="text-sm text-gray-500 mb-3">Nenhum responsável atribuído</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- DESCRIÇÃO - Editor avançado -->
        <section class="p-6 border-b border-gray-100" x-data="{ chars: {{ strlen(trim($task->description ?? '')) }} }">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-file-alt text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Descrição da Tarefa</h3>
                        <p class="text-xs text-gray-500">Detalhes e especificações importantes</p>
                    </div>
                </div>
                
                <!-- Contador de Caracteres com Animação -->
                <div 
                    class="flex items-center"
                >
                    <span class="text-xs" :class="{ 'text-green-600': chars <= 800, 'text-yellow-600': chars > 800 && chars <= 950, 'text-red-600': chars > 950 }" x-text="chars"></span>
                    <span class="text-xs text-gray-500">/1000</span>
                </div>
            </div>
            
            <!-- Editor/Visualizador -->
            @if($isEditingDescription)
                <!-- Modo Edição Avançado -->
                <div class="space-y-4" wire:key="description-edit-{{ $task->id }}">
                    
                    <!-- Textarea com auto-expand -->
                    <div class="relative">
                        <x-form.textarea
                            wire:model.defer="description"
                            id="task-description-{{ $task->id }}"
                            placeholder="Descreva detalhadamente esta tarefa..."
                            class="h-52"
                            @keydown.ctrl.enter="$wire.saveDescription()"
                            x-init="chars = $el.value.length"
                            @input="chars = $el.value.length"
                        ></x-form.textarea>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-2 justify-center mt-2 ">
                        <i class="fas fa-lightbulb"></i>
                        <span>Dica: Descreva objetivos, requisitos e observações importantes</span>
                    </div>

                    <div class="flex justify-between items-center gap-2">
                            
                        <!-- Botão cancelar -->
                        <x-button type="button" wire:click="cancelDescriptionEdit" icon="fas fa-times" text="Cancelar" variant="red_solid" fullWidth="true" />
                        
                        <!-- Botão salvar com animação -->
                        <x-button type="button" wire:click="saveDescription" icon="fas fa-check" text="Salvar" fullWidth="true" />
                    </div>
                </div>
            @else
                <!-- Modo Visualização com Markdown -->
                <div 
                    class="space-y-3 cursor-pointer group"
                    wire:click="enableDescriptionEdit"
                    wire:key="description-view-{{ $task->id }}"
                >
                    @if(trim($task->description))
                        <div class="relative prose prose-sm max-w-none p-4 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 group-hover:border-green-300 transition-all duration-300 h-52 overflow-y-auto">
                            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{!! nl2br(e(trim($task->description))) !!}</div>
                            
                            <!-- Overlay de edição -->
                            <div class="absolute inset-0 bg-gradient-to-t from-white/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl flex items-center justify-center h-52">
                                <span class="inline-flex items-center gap-2 px-4 py-2 bg-white text-green-700 text-xs font-medium rounded-lg shadow-lg border border-green-200">
                                    <i class="fas fa-edit"></i>
                                    Clique para editar descrição
                                </span>
                            </div>
                        </div>
                    @else
                        <!-- Placeholder interativo -->
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400 group rounded-2xl border-2 border-dashed border-gray-300 hover:border-green-400 hover:bg-green-50/50 transition-all duration-300 h-52 cursor-pointer">
                            <div class="relative mb-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <i class="far fa-file-alt text-2xl text-green-500 group-hover:text-green-600"></i>
                                </div>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-white rounded-full border-2 border-green-300 flex items-center justify-center">
                                    <i class="fas fa-plus text-green-500 text-xs"></i>
                                </div>
                            </div>
                            <p class="text-lg font-medium text-gray-600 group-hover:text-green-700 transition-colors mb-2">
                                Adicionar descrição detalhada
                            </p>
                            <p class="text-xs text-center text-gray-500 max-w-md mb-4">
                                Descreva objetivos, requisitos, entregáveis e observações importantes desta tarefa
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </section>

        <!-- PRAZOS E DATAS - Timeline vertical -->
        <section class="p-6 border-b border-gray-100" x-data="{ showDatePicker: false }">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 rounded-lg">
                        <i class="fas fa-calendar-alt text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Prazos e Datas</h3>
                        <p class="text-xs text-gray-500">Cronograma da tarefa</p>
                    </div>
                </div>
                <button 
                    @click="showDatePicker = !showDatePicker"
                    class="px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-lg transition-colors duration-200 flex items-center gap-2"
                >
                    <i class="fas fa-calendar-plus"></i>
                    Agendar
                </button>
            </div>
            
            <!-- Timeline Vertical -->
            <div class="relative pl-8">
                <!-- Linha vertical -->
                <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-gradient-to-b from-green-300 via-amber-300 to-red-300"></div>
                
                <!-- Item Timeline - Início -->
                <div class="relative mb-6">
                    <div class="absolute -left-8 top-0 w-6 h-6 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                        <i class="fas fa-play text-white text-xs"></i>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-green-100 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900">Data de Início</span>
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                Planejado
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="far fa-calendar text-sm"></i>
                            <span class="text-sm">{{ $task->start_date?->format('d/m/Y') ?? 'Não definido' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Item Timeline - Prazo -->
                <div class="relative mb-6">
                    <div class="absolute -left-8 top-0 w-6 h-6 bg-amber-500 rounded-full border-4 border-white flex items-center justify-center">
                        <i class="fas fa-flag text-white text-xs"></i>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-amber-100 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900">Prazo Final</span>
                            <span class="px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">
                                {{ $task->deadline?->isPast() ? 'Atrasado' : 'Em dia' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="far fa-calendar text-sm"></i>
                            <span class="text-sm">{{ $task->deadline?->format('d/m/Y') ?? 'Não definido' }}</span>
                        </div>
                        @if($task->deadline)
                            <div class="mt-2">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                    <span>Tempo restante</span>
                                    <span>{{ now()->diffInDays($task->deadline, false) }} dias</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full rounded-full transition-all duration-1000"
                                        :class="{
                                            'bg-green-500': {{ now()->diffInDays($task->deadline, false) }} > 7,
                                            'bg-amber-500': {{ now()->diffInDays($task->deadline, false) }} > 0 && {{ now()->diffInDays($task->deadline, false) }} <= 7,
                                            'bg-red-500': {{ now()->diffInDays($task->deadline, false) }} <= 0
                                        }"
                                        :style="{ width: Math.min(100, Math.max(0, ({{ now()->diffInDays($task->created_at, false) }} / {{ $task->created_at->diffInDays($task->deadline, false) }}) * 100)) + '%' }"
                                    ></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- FOOTER - Ações rápidas -->
    <footer class="sticky bottom-0 border-t border-gray-200 bg-white/95 backdrop-blur-sm p-4 shadow-lg">
        <div>
            <div class="flex justify-between gap-2">
                <!-- Botão de fechar com confirmação -->
                <x-button @click="activeItem = null" variant="gray_solid" icon="fas fa-times" text="Fechar" />
                
                <!-- Botão principal -->
                <x-button icon="fas fa-check" text="Marcar como Concluído" />
            </div>
        </div>
    </footer>
</div>