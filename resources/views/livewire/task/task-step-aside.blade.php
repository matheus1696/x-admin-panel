@push('styles')
<style>
    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #10b981, #059669);
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #059669, #047857);
        width: 8px;
    }
    
    /* Shimmer effect */
    @keyframes shimmer {
        0% { background-position: -200px 0; }
        100% { background-position: calc(200px + 100%) 0; }
    }
    
    .shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200px 100%;
        animation: shimmer 1.5s infinite;
    }
    
    /* Floating label animation */
    .floating-label {
        transform-origin: left top;
        transition: all 0.2s ease-out;
    }
    
    /* Glow effect */
    .glow {
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.1);
        transition: box-shadow 0.3s ease;
    }
    
    .glow:hover {
        box-shadow: 0 0 30px rgba(16, 185, 129, 0.2);
    }
    
    /* Bounce animation */
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px); }
    }
    
    .bounce-subtle {
        animation: bounce-subtle 2s infinite;
    }
    
    /* Gradient text */
    .gradient-text {
        background: linear-gradient(135deg, #059669, #10b981);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Card hover effect */
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

<div 
    x-data="taskStepAside({{ $step->id }})"
    x-init="init()"
    class="h-full flex flex-col bg-gradient-to-br from-white via-green-50/30 to-white"
    @keydown.escape.window="closeAside()"
    x-ref="asideContainer"
>
    <!-- Loading Overlay -->
    <div 
        x-show="isLoading"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 z-50 flex items-center justify-center bg-white/80 backdrop-blur-sm"
    >
        <div class="text-center">
            <div class="relative inline-block">
                <div class="w-16 h-16 border-4 border-green-200 rounded-full"></div>
                <div class="absolute top-0 left-0 w-16 h-16 border-4 border-green-500 rounded-full border-t-transparent animate-spin"></div>
                <i class="fas fa-tasks absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-green-500 text-xl"></i>
            </div>
            <p class="mt-4 text-sm font-medium text-gray-600 animate-pulse">Carregando detalhes...</p>
        </div>
    </div>

    <!-- HEADER - Sticky com efeito blur -->
    <header 
        class="sticky top-0 z-30 p-5 border-b border-green-100/50 bg-white/90 backdrop-blur-md supports-backdrop-blur:bg-white/60 shadow-sm glow"
        x-bind:class="{ 'shadow-lg': scrolled }"
        x-on:scroll.window="scrolled = window.scrollY > 10"
    >
        <div class="flex items-start justify-between gap-4">
            <!-- Conteúdo Principal -->
            <div class="flex-1 min-w-0 space-y-3">
                <!-- Badges Interativas -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Badge Prioridade -->
                    <button
                        x-data="{ showPriorityMenu: false }"
                        @click.stop="showPriorityMenu = !showPriorityMenu"
                        @click.outside="showPriorityMenu = false"
                        class="group relative"
                    >
                        <span 
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-full transition-all duration-300 transform hover:scale-105 {!! $step->priority->color ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700' !!} shadow-sm hover:shadow-md"
                            :class="{ 'ring-2 ring-offset-2 ring-green-300': showPriorityMenu }"
                        >
                            <i class="fas fa-exclamation-circle text-xs bounce-subtle"></i>
                            {{ $step->priority->title ?? 'Sem prioridade' }}
                            <i class="fas fa-chevron-down text-xs ml-1 transition-transform duration-200" 
                               :class="{ 'rotate-180': showPriorityMenu }"></i>
                        </span>
                        
                        <!-- Menu de Prioridade -->
                        <div 
                            x-show="showPriorityMenu"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute z-40 mt-2 w-48 rounded-lg bg-white py-1 shadow-xl ring-1 ring-black ring-opacity-5"
                        >
                            <!-- Opções de prioridade -->
                        </div>
                    </button>

                    <!-- Badge Status -->
                    <span 
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-full transition-all duration-300 {!! $step->taskStepStatus->color ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700' !!} shadow-sm"
                    >
                        <i class="fas fa-play-circle text-xs"></i>
                        {{ $step->taskStepStatus->title ?? 'Sem status' }}
                    </span>
                    
                    <!-- Indicador de Progresso -->
                    <div class="relative group" x-data="{ showProgress: false }">
                        <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div 
                                class="h-full bg-gradient-to-r from-green-400 to-green-500 rounded-full transition-all duration-1000 ease-out"
                                :style="{ width: progress + '%' }"
                                x-init="
                                    setTimeout(() => progress = {{ $step->progress ?? 0 }}, 300)
                                "
                            ></div>
                        </div>
                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            {{ $step->progress ?? 0 }}% concluído
                        </div>
                    </div>
                </div>

                <!-- Título com efeito gradiente -->
                <h1 class="text-xl font-bold text-gray-900 leading-tight">
                    <span class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                        {{ $step->code }}
                    </span>
                    <span class="text-gray-700">-</span>
                    <span class="text-gray-800">{{ $step->title }}</span>
                </h1>

                <!-- Timeline Interativa -->
                <div class="flex items-center gap-3 text-sm">
                    <!-- Timeline Dot -->
                    <div class="relative">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-100 to-green-50 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-plus text-green-600 text-xs"></i>
                        </div>
                        <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-xs text-gray-500 whitespace-nowrap">
                            Criado
                        </div>
                    </div>
                    
                    <!-- Linha da timeline -->
                    <div class="flex-1 h-0.5 bg-gradient-to-r from-green-200 via-green-100 to-gray-200"></div>
                    
                    <!-- Data de criação -->
                    <div 
                        x-data="{ showFullDate: false }"
                        @mouseenter="showFullDate = true"
                        @mouseleave="showFullDate = false"
                        class="relative"
                    >
                        <span class="text-gray-600 flex items-center gap-1.5 cursor-help">
                            <i class="far fa-calendar text-xs"></i>
                            <span x-text="showFullDate ? '{{ $step->created_at->format('d/m/Y H:i') }}' : '{{ $step->created_at->format('d/m/Y') }}'"></span>
                        </span>
                    </div>
                    
                    <span class="text-gray-300">•</span>
                    
                    <!-- Atualização recente -->
                    <div 
                        x-data="{ timeAgo: '{{ $step->updated_at->diffForHumans() }}' }"
                        class="flex items-center gap-1.5 text-gray-600"
                        x-init="
                            setInterval(() => {
                                timeAgo = moment('{{ $step->updated_at->toISOString() }}').fromNow();
                            }, 60000)
                        "
                    >
                        <i class="far fa-clock text-xs"></i>
                        <span x-text="timeAgo"></span>
                    </div>
                </div>
            </div>

            <!-- Botão Fechar com Animação -->
            <button
                @click="closeAsideWithAnimation()"
                class="relative flex-shrink-0 p-2 text-gray-400 hover:text-gray-600 rounded-full transition-all duration-300 group"
                aria-label="Fechar painel de detalhes"
                :disabled="isClosing"
            >
                <!-- Anel animado -->
                <div class="absolute inset-0 rounded-full bg-gradient-to-r from-green-100 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <!-- Ícone com rotação -->
                <div class="relative">
                    <i class="fas fa-times text-lg transition-transform duration-300 group-hover:rotate-90"></i>
                </div>
                
                <!-- Tooltip -->
                <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                    Fechar (Esc)
                </div>
            </button>
        </div>
    </header>

    <!-- CONTENT - Scroll suave com efeitos -->
    <div 
        class="flex-1 overflow-y-auto custom-scrollbar"
        x-ref="content"
        x-on:scroll.debounce.50="handleScroll"
    >
        <!-- RESPONSÁVEIS - Cards interativos -->
        <section class="p-6 border-b border-gray-100">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Card Setor -->
                <div 
                    x-data="{ isExpanded: false }"
                    class="card-hover bg-gradient-to-br from-white to-gray-50 rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-all duration-300"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i class="fas fa-building text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Setor Responsável
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">Responsável pela execução</p>
                            </div>
                        </div>
                        <button 
                            @click="isExpanded = !isExpanded"
                            class="p-1 text-gray-400 hover:text-gray-600 rounded transition-colors"
                            :aria-expanded="isExpanded"
                        >
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                               :class="{ 'rotate-180': isExpanded }"></i>
                        </button>
                    </div>
                    
                    @if ($step->responsable_organization)
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center text-white font-bold">
                                    {{ substr($step->responsable_organization->acronym ?? '', 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">
                                        {{ $step->responsable_organization->acronym ?? '' }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $step->responsable_organization->title ?? '' }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Detalhes expandidos -->
                            <div 
                                x-show="isExpanded"
                                x-collapse
                                class="pt-3 border-t border-gray-100 space-y-2"
                            >
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-users text-gray-400 text-xs"></i>
                                    <span class="text-gray-600">Equipe: 12 membros</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-phone text-gray-400 text-xs"></i>
                                    <span class="text-gray-600">(11) 99999-9999</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                <i class="fas fa-building text-gray-400"></i>
                            </div>
                            <p class="text-sm text-gray-500 mb-2">Nenhum setor atribuído</p>
                            <button class="text-xs text-green-600 hover:text-green-700 font-medium flex items-center gap-1 mx-auto">
                                <i class="fas fa-plus"></i>
                                Atribuir setor
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Card Responsável -->
                <div class="card-hover bg-gradient-to-br from-white to-blue-50 rounded-xl border border-blue-100 p-5 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i class="fas fa-user-tie text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Responsável
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">Executor principal</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                            Principal
                        </span>
                    </div>
                    
                    @if ($step->responsable)
                        <div class="space-y-4">
                            <!-- Avatar e Informações -->
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        {{ substr($step->responsable->name ?? '', 0, 1) }}
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                                        <i class="fas fa-check text-white text-[8px]"></i>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-gray-900 truncate">
                                        {{ $step->responsable->name ?? '' }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $step->responsable->occupation->title ?? 'Sem cargo' }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ações Rápidas -->
                            <div class="flex flex-wrap gap-2 pt-3 border-t border-blue-100">
                                <a 
                                    href="mailto:{{ $step->responsable->email ?? '' }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors duration-200"
                                >
                                    <i class="far fa-envelope"></i>
                                    Email
                                </a>
                                <button 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
                                >
                                    <i class="fas fa-phone"></i>
                                    Ligar
                                </button>
                                <button 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition-colors duration-200"
                                >
                                    <i class="fas fa-tasks"></i>
                                    Tarefas
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 mb-3">
                                <i class="fas fa-user-plus text-blue-400"></i>
                            </div>
                            <p class="text-sm text-gray-500 mb-3">Nenhum responsável atribuído</p>
                            <button 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-600 text-white text-xs font-medium rounded-lg hover:from-blue-600 hover:to-cyan-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                            >
                                <i class="fas fa-user-plus"></i>
                                Atribuir responsável
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- DESCRIÇÃO - Editor avançado -->
        <section class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between mb-4">
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
                    x-data="{ chars: {{ strlen(trim($step->description ?? '')) }} }"
                    class="flex items-center gap-2"
                >
                    <div class="relative">
                        <svg class="w-10 h-10 transform -rotate-90">
                            <circle 
                                cx="20" 
                                cy="20" 
                                r="15" 
                                stroke="#e5e7eb" 
                                stroke-width="3" 
                                fill="none"
                            />
                            <circle 
                                cx="20" 
                                cy="20" 
                                r="15" 
                                stroke="#8b5cf6" 
                                stroke-width="3" 
                                fill="none"
                                stroke-linecap="round"
                                :stroke-dasharray="94.2"
                                :stroke-dashoffset="94.2 - (chars / 1000 * 94.2)"
                                class="transition-all duration-1000 ease-out"
                            />
                        </svg>
                        <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xs font-bold"
                              :class="{ 'text-green-600': chars <= 800, 'text-yellow-600': chars > 800 && chars <= 950, 'text-red-600': chars > 950 }"
                              x-text="chars"
                        ></span>
                    </div>
                    <span class="text-xs text-gray-500">/1000</span>
                </div>
            </div>
            
            <!-- Editor/Visualizador -->
            @if($isEditingDescription)
                <!-- Modo Edição Avançado -->
                <div class="space-y-4" wire:key="description-edit-{{ $step->id }}">
                    <!-- Toolbar do Editor -->
                    <div class="flex flex-wrap gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <button type="button" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-white rounded">
                            <i class="fas fa-bold"></i>
                        </button>
                        <button type="button" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-white rounded">
                            <i class="fas fa-italic"></i>
                        </button>
                        <button type="button" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-white rounded">
                            <i class="fas fa-list-ul"></i>
                        </button>
                        <button type="button" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-white rounded">
                            <i class="fas fa-link"></i>
                        </button>
                        <div class="flex-1"></div>
                        <button type="button" class="p-2 text-green-600 hover:text-green-700 hover:bg-green-50 rounded">
                            <i class="fas fa-magic"></i>
                            <span class="text-xs ml-1">IA Assist</span>
                        </button>
                    </div>
                    
                    <!-- Textarea com auto-expand -->
                    <div class="relative">
                        <textarea
                            wire:model.defer="description"
                            id="task-description-{{ $step->id }}"
                            rows="6"
                            placeholder="Descreva detalhadamente esta tarefa...&#10;• Use bullet points para listar requisitos&#10;• Inclua links importantes&#10;• Especifique prazos e entregáveis"
                            class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 resize-none"
                            x-data="{
                                resize() {
                                    this.style.height = 'auto';
                                    this.style.height = (this.scrollHeight + 2) + 'px';
                                }
                            }"
                            x-init="resize()"
                            @input="resize()"
                            @keydown.ctrl.enter="$wire.saveDescription()"
                        ></textarea>
                        
                        <!-- Dicas flutuantes -->
                        <div class="absolute -top-8 right-0 flex items-center gap-2 text-xs text-gray-500">
                            <kbd class="px-2 py-1 bg-gray-100 rounded border border-gray-300">Ctrl + Enter</kbd>
                            <span>para salvar</span>
                        </div>
                    </div>
                    
                    <!-- Barra de Ações -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <i class="fas fa-lightbulb"></i>
                            <span>Dica: Descreva objetivos, requisitos e observações importantes</span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <!-- Botão de preview -->
                            <button
                                type="button"
                                @click="$wire.cancelDescriptionEdit()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center gap-2"
                                :disabled="$wire.savingDescription"
                            >
                                <i class="fas fa-eye"></i>
                                Preview
                            </button>
                            
                            <!-- Botão cancelar -->
                            <button
                                type="button"
                                wire:click="cancelDescriptionEdit"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center gap-2"
                                :disabled="$wire.savingDescription"
                            >
                                <i class="fas fa-times"></i>
                                Cancelar
                            </button>
                            
                            <!-- Botão salvar com animação -->
                            <button
                                type="button"
                                wire:click="saveDescription"
                                class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg transition-all duration-300 flex items-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                :disabled="$wire.savingDescription"
                                x-data="{ saving: false }"
                                x-on:click="saving = true; setTimeout(() => saving = false, 2000)"
                            >
                                <i class="fas fa-check" x-show="!saving"></i>
                                <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                                <span x-text="saving ? 'Salvando...' : 'Salvar Alterações'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Modo Visualização com Markdown -->
                <div 
                    class="space-y-3 cursor-pointer group"
                    wire:click="enableDescriptionEdit"
                    wire:key="description-view-{{ $step->id }}"
                >
                    @if(trim($step->description))
                        <div class="prose prose-sm max-w-none p-4 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 group-hover:border-green-300 transition-all duration-300">
                            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">
                                {!! nl2br(e(trim($step->description))) !!}
                            </div>
                            
                            <!-- Overlay de edição -->
                            <div class="absolute inset-0 bg-gradient-to-t from-white/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl flex items-center justify-center">
                                <span class="inline-flex items-center gap-2 px-4 py-2 bg-white text-green-700 text-sm font-medium rounded-lg shadow-lg border border-green-200">
                                    <i class="fas fa-edit"></i>
                                    Clique para editar descrição
                                </span>
                            </div>
                        </div>
                    @else
                        <!-- Placeholder interativo -->
                        <div class="flex flex-col items-center justify-center h-full py-12 text-gray-400 group rounded-2xl border-2 border-dashed border-gray-300 hover:border-green-400 hover:bg-green-50/50 transition-all duration-300 min-h-[200px] cursor-pointer">
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
                            <p class="text-sm text-center text-gray-500 max-w-md mb-4">
                                Descreva objetivos, requisitos, entregáveis e observações importantes desta tarefa
                            </p>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <kbd class="px-2 py-1 bg-gray-100 rounded">Dica</kbd>
                                <span>Use bullet points e seja específico nos detalhes</span>
                            </div>
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
                            <span class="text-sm">{{ $step->start_date?->format('d/m/Y') ?? 'Não definido' }}</span>
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
                                {{ $step->deadline?->isPast() ? 'Atrasado' : 'Em dia' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="far fa-calendar text-sm"></i>
                            <span class="text-sm">{{ $step->deadline?->format('d/m/Y') ?? 'Não definido' }}</span>
                        </div>
                        @if($step->deadline)
                            <div class="mt-2">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                    <span>Tempo restante</span>
                                    <span>{{ now()->diffInDays($step->deadline, false) }} dias</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full rounded-full transition-all duration-1000"
                                        :class="{
                                            'bg-green-500': {{ now()->diffInDays($step->deadline, false) }} > 7,
                                            'bg-amber-500': {{ now()->diffInDays($step->deadline, false) }} > 0 && {{ now()->diffInDays($step->deadline, false) }} <= 7,
                                            'bg-red-500': {{ now()->diffInDays($step->deadline, false) }} <= 0
                                        }"
                                        :style="{ width: Math.min(100, Math.max(0, ({{ now()->diffInDays($step->created_at, false) }} / {{ $step->created_at->diffInDays($step->deadline, false) }}) * 100)) + '%' }"
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
        <div class="flex items-center justify-between">
            <!-- Status rápido -->
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500">Status atual:</span>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-sm font-medium text-gray-900">Em andamento</span>
                </div>
            </div>
            
            <!-- Ações rápidas -->
            <div class="flex items-center gap-2">
                <!-- Botão de histórico -->
                <button 
                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200 relative group"
                    x-data="{ showHistory: false }"
                    @click="showHistory = !showHistory"
                >
                    <i class="fas fa-history"></i>
                    <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                        Histórico
                    </div>
                </button>
                
                <!-- Botão de anexos -->
                <button 
                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200 relative group"
                >
                    <i class="fas fa-paperclip"></i>
                    <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                        Anexos (3)
                    </div>
                </button>
                
                <!-- Botão de comentários -->
                <button 
                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200 relative group"
                >
                    <i class="far fa-comment"></i>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 text-white text-[10px] rounded-full flex items-center justify-center">
                        5
                    </span>
                    <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                        Comentários
                    </div>
                </button>
                
                <!-- Divisor -->
                <div class="w-px h-6 bg-gray-300 mx-2"></div>
                
                <!-- Botão de fechar com confirmação -->
                <button
                    @click="confirmClose()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center gap-2"
                >
                    <i class="fas fa-times"></i>
                    Fechar
                </button>
                
                <!-- Botão principal -->
                <button
                    class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg transition-all duration-300 flex items-center gap-2 shadow-md hover:shadow-lg"
                >
                    <i class="fas fa-check-circle"></i>
                    Marcar como Concluído
                </button>
            </div>
        </div>
    </footer>
</div>

@push('scripts')
<script>
    // Alpine.js Component
    function taskStepAside(stepId) {
        return {
            // Estado
            isLoading: false,
            scrolled: false,
            isClosing: false,
            progress: 0,
            
            // Inicialização
            init() {
                console.log(`Aside da tarefa ${stepId} inicializado`);
                
                // Carregar dados adicionais
                this.loadAdditionalData();
                
                // Animar entrada
                this.animateEntrance();
                
                // Configurar atalhos de teclado
                this.setupKeyboardShortcuts();
            },
            
            // Métodos
            loadAdditionalData() {
                // Simular carregamento de dados adicionais
                setTimeout(() => {
                    this.isLoading = false;
                }, 800);
            },
            
            animateEntrance() {
                // Animar elementos sequencialmente
                const elements = this.$refs.content.querySelectorAll('[x-entrance]');
                elements.forEach((el, index) => {
                    setTimeout(() => {
                        el.classList.add('animate-fade-in-up');
                    }, index * 100);
                });
            },
            
            setupKeyboardShortcuts() {
                // Atalhos globais
                document.addEventListener('keydown', (e) => {
                    // Ctrl + S para salvar
                    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                        e.preventDefault();
                        if (typeof this.$wire !== 'undefined') {
                            this.$wire.saveDescription?.();
                        }
                    }
                    
                    // Ctrl + E para editar
                    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                        e.preventDefault();
                        if (typeof this.$wire !== 'undefined') {
                            this.$wire.enableDescriptionEdit?.();
                        }
                    }
                });
            },
            
            handleScroll() {
                const content = this.$refs.content;
                this.scrolled = content.scrollTop > 20;
            },
            
            closeAside() {
                if (typeof this.$wire !== 'undefined') {
                    this.$wire.set('openAsideTaskStep', false);
                } else {
                    // Fallback para Alpine puro
                    const event = new CustomEvent('close-aside', { 
                        detail: { stepId: stepId },
                        bubbles: true 
                    });
                    this.$el.dispatchEvent(event);
                }
            },
            
            closeAsideWithAnimation() {
                this.isClosing = true;
                
                // Animação de saída
                this.$refs.asideContainer.style.transform = 'translateX(100%)';
                this.$refs.asideContainer.style.opacity = '0';
                
                // Fechar após animação
                setTimeout(() => {
                    this.closeAside();
                }, 300);
            },
            
            confirmClose() {
                if (this.hasUnsavedChanges()) {
                    if (confirm('Tem alterações não salvas. Deseja realmente fechar?')) {
                        this.closeAsideWithAnimation();
                    }
                } else {
                    this.closeAsideWithAnimation();
                }
            },
            
            hasUnsavedChanges() {
                // Verificar se há alterações não salvas
                return false; // Implementar lógica real
            }
        };
    }
</script>
@endpush