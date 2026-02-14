<div wire:init="loadTask" class="relative h-full">

    <!-- Flash Message -->
    <x-alert.flash />

    @if ($isLoading)
        <!-- LOADING PREMIUM - Design sofisticado -->
        <div class="absolute inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-white via-green-50/20 to-white">
            <!-- Card flutuante com glassmorphism -->
            <div class="flex flex-col items-center gap-6 p-8 max-w-sm mx-4">
                
                <!-- Animação principal com ícone de tarefa -->
                <div class="relative">
                    <!-- Anel giratório premium -->
                    <div class="absolute inset-0 rounded-full">
                        <div class="w-24 h-24 rounded-full border-4 border-emerald-100 border-t-emerald-600 border-r-emerald-600 animate-spin"></div>
                    </div>
                    
                    <!-- Ícone central com pulse -->
                    <div class="relative flex items-center justify-center w-24 h-24">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/20 to-green-500/20 rounded-full animate-ping opacity-75"></div>
                        <div class="relative bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl w-16 h-16 flex items-center justify-center shadow-xl shadow-emerald-500/20">
                            <i class="fas fa-tasks text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Texto principal com gradiente -->
                <div class="text-center space-y-2">
                    <h3 class="text-xl font-bold bg-gradient-to-r from-emerald-700 to-green-700 bg-clip-text text-transparent">
                        Carregando tarefa
                    </h3>
                    
                    <!-- Dots animados -->
                    <div class="flex justify-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0s"></span>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    </div>
                </div>

                <!-- Subtítulo com efeito fade -->
                <p class="text-sm text-gray-500 text-center max-w-xs">
                    Preparando informações e detalhes da tarefa
                </p>

                <!-- Status sutil -->
                <span class="text-[10px] text-gray-400 mt-2">
                    <i class="fas fa-circle-notch animate-spin mr-1"></i>
                    Processando...
                </span>
            </div>
        </div>
    @else
        <div x-data="taskAside({{ $task->id }})" class="h-full flex flex-col bg-gradient-to-br from-white via-green-50/30 to-white" x-ref="asideContainer" >
            <!-- HEADER -->
            <header class="sticky top-0 z-30 bg-white/80 border-b border-white/40 shadow-lg">
                <div class="px-6 py-5">
                    <div class="flex items-start justify-between gap-4">

                        <div class="flex-1 min-w-0 space-y-4">
                            <!-- Badges -->
                            <div class="flex flex-wrap items-center gap-2.5">
                                <!-- Badge Prioridade -->
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full 
                                        shadow-sm hover:-translate-y-0.5 hover:shadow-md transition-all duration-300
                                        {!! $task->taskPriority->color_code_tailwind ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700' !!}">
                                    <i class="fas fa-exclamation-circle text-[10px] animate-pulse"></i>
                                    {{ $task->taskPriority->title ?? 'Sem prioridade' }}
                                </span>

                                <!-- Badge Status -->
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full 
                                        shadow-sm hover:-translate-y-0.5 hover:shadow-md transition-all duration-300
                                        {!! $task->taskStatus->color_code_tailwind ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700' !!}">
                                    <i class="fas fa-play-circle text-[10px]"></i>
                                    {{ $task->taskStatus->title ?? 'Sem status' }}
                                </span>

                                <!-- Badge ID -->
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-white/70 backdrop-blur-sm border border-gray-200/80 text-gray-600 shadow-sm">
                                    <i class="fas fa-hashtag text-[10px]"></i>
                                    {{ $task->code }}
                                </span>

                                <!-- Badge Category -->
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-white/70 backdrop-blur-sm border border-gray-200/80 text-gray-600 shadow-sm">
                                    <i class="fas fa-tag text-green-700"></i>
                                    {{ $task->taskCategory->title ?? 'Sem categoria' }}
                                </span>

                                <!-- Badge Category -->
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-white/70 backdrop-blur-sm border border-gray-200/80 text-gray-600 shadow-sm">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-700"></span>
                                    </span>
                                    <span class="font-medium">Criado</span>
                                    <span class="text-gray-900">{{ $task->created_at->format('d/m/Y') }}</span>
                                    <span class="text-gray-400">{{ $task->created_at->format('H:i') }}</span>
                                </span>
                            </div>

                            <!-- Título -->
                            <div class="space-y-1.5">
                                <h1 class="text-2xl font-bold tracking-tight line-clamp-1">
                                    <span class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-emerald-700 bg-clip-text text-transparent">
                                        {{ $task->code }}
                                    </span>
                                    <span class="text-gray-300 font-light mx-2">/</span>
                                    <span class="text-gray-800 font-semibold">{{ $task->title }}</span>
                                </h1>
                            </div>

                            <!-- Timeline -->
                            <div class="flex items-center gap-2 text-xs">
                                <div class="flex items-center gap-2 text-gray-500 bg-white/40 px-3 py-1.5 rounded-full">
                                    <i class="far fa-clock text-emerald-400"></i>
                                    <span class="font-medium">Atualizado</span>
                                    <span class="text-gray-900">{{ $task->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botão Fechar -->
                        <button @click="openAsideTask = false"  class="relative group shrink-0 text-gray-400 hover:text-gray-600 transition-all duration-300">
                            <div class="absolute inset-0 bg-gray-100/80 rounded-full scale-0 group-hover:scale-100 transition-transform duration-300"></div>
                            <div class="relative px-4 py-2">
                                <i class="fas fa-times text-lg transition-all duration-500 group-hover:rotate-180"></i>
                            </div>
                            <span class="absolute -bottom-7 left-1/2 -translate-x-1/2 text-[10px] font-medium text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                Fechar
                            </span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <div class="flex-1 overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-gray-50/50 [&::-webkit-scrollbar-thumb]:bg-gray-300/50 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:hover:bg-gray-400/50">
                
                <!-- DESCRIÇÃO -->
                <section class="p-6 border-b border-gray-100/80 bg-white/30" x-data="{ chars: {{ strlen(trim($task->description ?? '')) }} }">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center size-10 bg-gradient-to-br from-purple-50 to-purple-100/80 rounded-xl shadow-sm">
                                <i class="fas fa-file-alt text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    Descrição da Tarefa
                                    <span class="px-2 py-0.5 text-[10px] font-medium bg-purple-100 text-purple-700 rounded-full">Detalhes</span>
                                </h3>
                                <p class="text-xs text-gray-500">Especificações e observações importantes</p>
                            </div>
                        </div>
                        
                        <!-- Contador Premium -->
                        <div class="flex items-center gap-1 px-3 py-1.5 bg-white/70 backdrop-blur-sm rounded-full border border-gray-100 shadow-sm">
                            <span class="text-xs font-medium" :class="{ 'text-emerald-600': chars <= 800, 'text-amber-600': chars > 800 && chars <= 950, 'text-rose-600': chars > 950 }" x-text="chars"></span>
                            <span class="text-xs text-gray-400">/1000</span>
                        </div>
                    </div>
                    
                    @if($isEditingDescription && !$task->finished_at)
                        <!-- Modo Edição Premium -->
                        <div class="space-y-4" wire:key="description-edit-{{ $task->id }}">
                            
                            <div class="relative group">
                                <x-form.textarea
                                    wire:model.defer="description"
                                    id="task-description-{{ $task->id }}"
                                    placeholder="Descreva detalhadamente esta tarefa..."
                                    x-init="chars = $el.value.length"
                                    @input="chars = $el.value.length"
                                ></x-form.textarea>
                            </div>

                            <div class="flex items-center gap-3 text-xs text-gray-500 bg-gray-50/80 p-3 rounded-xl border border-gray-100">
                                <i class="fas fa-info-circle text-emerald-500"></i>
                                <span>Descreva objetivos, requisitos e observações importantes da tarefa</span>
                            </div>

                            <div class="flex justify-between items-center gap-3">
                                <x-button type="button" wire:click="cancelDescriptionEdit" icon="fas fa-times" text="Cancelar" variant="red_outline" fullWidth="true"  />
                                
                                <x-button type="button" wire:click="saveDescription" icon="fas fa-check" text="Salvar alterações" fullWidth="true"  />
                            </div>
                        </div>
                    @else
                        <!-- Modo Visualização Premium -->
                        <div class="space-y-3 group"
                            wire:click="enableDescriptionEdit"
                            wire:key="description-view-{{ $task->id }}">
                            
                            @if($task->description)
                                <div class="relative bg-gradient-to-br from-gray-50/80 to-white rounded-2xl border border-gray-200/80 group-hover:border-emerald-200/80 transition-all duration-500 h-48 overflow-y-auto shadow-sm group-hover:shadow-md">
                                    <div class="p-2 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300/50 [&::-webkit-scrollbar-thumb]:rounded-full h-full overflow-y-auto">{!! nl2br(e(trim($task->description))) !!}</div>
                                    
                                    @if (!$task->finished_at)
                                        <!-- Overlay Edição -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/50 to-transparent backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all duration-500 rounded-2xl flex items-end justify-center pb-6">
                                            <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-emerald-700 text-xs font-medium rounded-full shadow-lg border border-emerald-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                                                <i class="fas fa-pen-fancy"></i>
                                                Editar descrição
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                @if (!$task->finished_at)
                                    <!-- Placeholder Premium -->
                                    <div class="flex flex-col items-center justify-center py-14 px-6 text-gray-400 group rounded-2xl border-2 border-dashed border-gray-200/80 hover:border-emerald-300/80 hover:bg-gradient-to-br hover:from-emerald-50/30 hover:to-white transition-all duration-500 h-48 cursor-pointer bg-white/50 backdrop-blur-sm">
                                        <div class="relative mb-4">
                                            <div class="w-20 h-20 bg-gradient-to-br from-emerald-100/80 to-green-100/80 rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 shadow-sm">
                                                <i class="far fa-file-alt text-3xl text-emerald-500/80 group-hover:text-emerald-600"></i>
                                            </div>
                                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-white rounded-full border-2 border-emerald-200/80 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                                <i class="fas fa-plus text-emerald-500 text-xs"></i>
                                            </div>
                                        </div>
                                        <p class="text-base font-medium text-gray-700 group-hover:text-emerald-700 transition-colors mb-2">
                                            Adicionar descrição detalhada
                                        </p>
                                        <p class="text-xs text-center text-gray-400 max-w-md">
                                            Clique para descrever objetivos, requisitos e entregáveis
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </section>
                
                <!-- INFORMAÇÕES - Card Detalhado Premium -->
                <section class="p-6 border-b border-gray-100/80 bg-gradient-to-br from-white/40 to-gray-50/20">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex items-center justify-center size-10 bg-gradient-to-br from-blue-50 to-sky-100/80 rounded-xl shadow-sm">
                            <i class="fas fa-sliders-h text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Informações da Tarefa</h3>
                            <p class="text-xs text-gray-500">Configurações e metadados</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex items-center justify-between p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm hover:border-gray-200 hover:bg-white/90 transition-all">
                            <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                <i class="fas fa-user-circle text-gray-400 transition-transform duration-300 group-hover:rotate-12 "></i>
                                Solicitante
                            </span>
                            @if (!$task->finished_at)
                                <div class="flex-1">
                                    <x-form.select-livewire wire:model.live="responsable_id" 
                                            :collection="$users" 
                                            valueField="id" labelField="name" 
                                            :selected="$task->user_id" 
                                            variant="inline" size="xs" 
                                            class="!bg-transparent !border-0 !text-gray-900 !font-medium !shadow-none !p-0" />
                                </div>
                            @else
                                <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                    {{ $task->user?->name }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm hover:border-gray-200 hover:bg-white/90 transition-all">
                            <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                <i class="fas fa-folder text-gray-400 transition-transform duration-300 group-hover:rotate-12 "></i>
                                Categoria
                            </span>
                            @if (!$task->finished_at)
                                <div class="flex-1">
                                    <x-form.select-livewire wire:model.live="list_category_id" 
                                            :collection="$taskCategories" 
                                            valueField="id" labelField="title" 
                                            :selected="$task->task_category_id" 
                                            variant="inline" />
                                </div>
                            @else
                                <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                    {{ $task->taskCategory?->title }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm hover:border-gray-200 hover:bg-white/90 transition-all">
                            <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                <i class="fas fa-flag text-gray-400 transition-transform duration-300 group-hover:rotate-12 "></i>
                                Prioridade
                            </span>
                            
                            @if (!$task->finished_at)
                                <div class="flex-1">
                                    <x-form.select-livewire wire:model.live="list_priority_id" 
                                            :collection="$taskPriorities" 
                                            valueField="id" labelField="title" 
                                            :selected="$task->task_priority_id" 
                                            variant="inline" />
                                </div>
                            @else
                                <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                    {{ $task->taskPriority?->title }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm hover:border-gray-200 hover:bg-white/90 transition-all">
                            <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                <i class="fas fa-flag text-gray-400 transition-transform duration-300 group-hover:rotate-12 "></i>
                                Status
                            </span>
                            @if (!$task->finished_at)
                                <div class="flex-1">
                                    <x-form.select-livewire wire:model.live="list_status_id" 
                                            :collection="$taskStatuses" 
                                            valueField="id" labelField="title" 
                                            :selected="$task->task_status_id" 
                                            variant="inline" />
                                </div>
                            @else
                                <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                    {{ $task->taskStatus?->title }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm">
                            <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                <i class="fas fa-calendar-plus text-gray-400 transition-transform duration-300 group-hover:rotate-12 "></i>
                                Criado em
                            </span>
                            <span class="text-xs font-semibold text-gray-900 bg-gray-100/80 px-3 py-1.5 rounded-lg">
                                {{ $task->created_at->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm">
                            <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                <i class="fas fa-calendar-plus text-gray-400 transition-transform duration-300 group-hover:rotate-12 "></i>
                                Atualizado em
                            </span>
                            <span class="text-xs font-semibold text-gray-900 bg-gray-100/80 px-3 py-1.5 rounded-lg">
                                {{ $task->updated_at->format('d/m/Y') }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm">
                            <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                                <i class="fas fa-play text-gray-400 transition-transform duration-300 group-hover:rotate-12 "></i>
                                Início
                            </span>
                            <span class="text-xs font-semibold text-gray-900 bg-gray-100/80 px-3 py-1.5 rounded-lg">
                                {{ $task->started_at?->format('d/m/Y') ?? '—' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 rounded-xl border shadow-sm transition-all duration-300 col-span-1 group {{ $task->deadline_at && $task->deadline_at->isPast() && !$task->finished_at ? 'bg-gradient-to-br from-rose-50/90 to-rose-100/70 border-rose-200/80 text-rose-700' : 'bg-white/90 border-gray-200/80 text-gray-500 hover:border-gray-200/80' }}">
                            
                            <!-- Label com ícone animado -->
                            <span class="text-xs font-medium flex items-center gap-2 {{ $task->deadline_at && $task->deadline_at->isPast() && !$task->finished_at ? 'text-rose-600' : 'text-gray-600' }}">
                                <i class="fas fa-hourglass-end transition-transform duration-300 group-hover:rotate-12 {{ $task->deadline_at && $task->deadline_at->isPast() && !$task->finished_at ? 'text-rose-500' : 'text-gray-500' }}"></i>
                                Prazo final
                            </span>

                            @if($isEditingDeadline && !$task->finished_at)
                                <!-- Modo Edição Premium -->
                                <div class="flex items-center gap-2" wire:key="deadline-edit-{{ $task->id }}">
                                    
                                    <!-- Input com estilo refinado -->
                                    <div class="relative">
                                        <x-form.input type="date" wire:model.defer="deadline_at" :value="$task->deadline_at" variant="minimal" />
                                    </div>
                                    
                                    <!-- Botões de ação compactos -->
                                    <div class="flex items-center gap-1">
                                        <x-button type="button" wire:click="saveDeadline" icon="fas fa-check" variant="green_outline" />
                                        <x-button type="button" wire:click="cancelDeadlineEdit" icon="fas fa-times" variant="red_outline" />
                                    </div>
                                </div>
                            @else
                                <!-- Modo Visualização Premium -->
                                <div class="flex items-center group/edit" wire:click="enableDeadlineEdit" wire:key="deadline-view-{{ $task->id }}">
                                    
                                    @if (!$task->finished_at)
                                        <!-- Ícone de edição (aparece no hover) -->
                                        <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover/edit:opacity-100 transition-opacity duration-300 {{ $task->deadline_at && $task->deadline_at->isPast() ? 'hover:text-rose-600' : 'hover:text-emerald-600' }}"></i>
                                    @endif
                                    
                                    <!-- Valor com hover reveal -->
                                    <span class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-300" >
                                        @if($task->deadline_at)
                                            {{ $task->deadline_at->format('d/m/Y') }}
                                            <!-- Badge contextual -->
                                            @php
                                                $daysLeft = $task->deadline_at ? now()->diffInDays($task->deadline_at, false) : null;
                                            @endphp
                                            
                                            @if($task->deadline_at->isPast() && !$task->finished_at)
                                                <span class="ml-1.5 text-[10px] font-medium text-rose-700 bg-rose-200/60 px-1.5 py-0.5 rounded-full uppercase">
                                                    atrasado
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-gray-500 italic flex items-center gap-1">
                                                Sem prazo definido
                                                <i class="fas fa-plus-circle text-emerald-500 opacity-0 group-hover/edit:opacity-100 transition-opacity text-[10px]"></i>
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

                <!-- ATIVIDADES -->
                <section class="p-6 bg-white/30" x-data="{ showDatePicker: false }">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-gradient-to-br from-amber-50 to-orange-100/80 rounded-xl shadow-sm">
                                <i class="fas fa-clock-rotate-left text-amber-600"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Atividades</h3>
                                <p class="text-xs text-gray-500">Histórico e comentários</p>
                            </div>
                        </div>
                    </div>

                    <!-- Nova Atividade -->
                    @if (!$task->finished_at)
                        <div class="relative mb-6 group">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0">
                                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-100 to-green-100 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                                        <span class="text-xs font-semibold text-emerald-700">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div>
                                        <form wire:submit.prevent="storeComment()">
                                            <x-form.textarea name="comment" wire:model.defer="comment" placeholder="Escreva um comentário..." rows="3"/>
                                            <div class="flex items-center justify-end">
                                                <x-button type="submit" icon="fas fa-paper-plane" text="Comentar" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Timeline Atividades -->
                    <div class="grid grid-col-1 gap-4 mt-6">
                        @foreach ($task->taskActivities() as $taskActivity)
                            @if ($taskActivity->type == 'comment')                                
                                <div class="relative pl-8 border-l-2 border-emerald-200/60 last:border-l-0">
                                    <div class="absolute left-[-9px] top-0 w-4 h-4 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-full border-2 border-white shadow-md"></div>
                                    
                                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-gray-100/80 shadow-sm hover:shadow-md transition-all">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-semibold text-gray-900">{{ $taskActivity->user->name}}</span>
                                                <span class="text-[10px] text-gray-400">•</span>
                                                <span class="text-[10px] text-gray-500">{{ $taskActivity->created_at->format('d/m/Y')}}</span>
                                            </div>
                                            <span class="px-2 py-1 bg-gray-100/80 text-[10px] font-medium text-gray-600 rounded-full border border-gray-200">
                                                Comentário
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            {{ $taskActivity->description}}
                                        </p>
                                    </div>
                                </div>
                            @else 
                                <div class="text-center">
                                    <span class="px-2 py-1 bg-gray-100/80 text-[10px] font-medium text-gray-600 rounded-full border border-gray-200">
                                        Aviso • {{ $taskActivity->created_at->format('d/m/Y')}} • {{ $taskActivity->description}}
                                    </span>                                
                                </div>   
                            @endif
                        @endforeach
                    </div>
                </section>
            </div>

            <!-- FOOTER - Ações Premium -->
            <footer class="sticky bottom-0 bg-white/95 border-t border-green-700/50 shadow-lg">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-end gap-3">
                        
                        <div class="flex items-center gap-2">
                            <x-button @click="openAsideTask = false" 
                                    variant="gray_outline"
                                    icon="fas fa-times" 
                                    text="Fechar"  />
                        </div>

                        @if ($task->started_at)
                            @if ($task->taskSteps->count() < 1 || $task->taskStepsFinished->count() == $task->taskSteps->count())
                                @if (!$task->finished_at)
                                    <x-button icon="fas fa-check" text="Marca como concluído" wire:click="taskFinished()" />
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </footer>
        </div>        
    @endif
</div>