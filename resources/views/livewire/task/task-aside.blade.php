<div x-data="taskAside({{ $task->id }})" 
     class="h-full flex flex-col bg-gradient-to-br from-white via-green-50/30 to-white"
     x-ref="asideContainer"
>
    <!-- HEADER -->
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl backdrop-saturate-150 border-b border-white/40 shadow-lg">
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
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full 
                                   bg-white/70 backdrop-blur-sm border border-gray-200/80 text-gray-600 shadow-sm">
                            <i class="fas fa-hashtag text-[10px]"></i>
                            {{ $task->id }}
                        </span>
                    </div>

                    <!-- Título -->
                    <div class="space-y-1.5">
                        <h1 class="text-2xl font-bold tracking-tight">
                            <span class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-emerald-700 bg-clip-text text-transparent">
                                {{ $task->code }}
                            </span>
                            <span class="text-gray-300 font-light mx-2">/</span>
                            <span class="text-gray-800 font-semibold">{{ $task->title }}</span>
                        </h1>
                        
                        <!-- Categoria como subtítulo -->
                        <p class="text-xs text-gray-400 flex items-center gap-1.5">
                            <i class="fas fa-tag text-emerald-400"></i>
                            {{ $task->taskCategory->title ?? 'Sem categoria' }}
                        </p>
                    </div>

                    <!-- Timeline -->
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-2 text-gray-500 bg-white/60 px-3 py-1.5 rounded-full border border-gray-100 shadow-sm">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span class="font-medium">Criado</span>
                            <span class="text-gray-900">{{ $task->created_at->format('d/m/Y') }}</span>
                            <span class="text-gray-400">{{ $task->created_at->format('H:i') }}</span>
                        </div>
                        
                        <span class="text-gray-200 select-none">•</span>
                        
                        <div class="flex items-center gap-2 text-gray-500 bg-white/40 px-3 py-1.5 rounded-full">
                            <i class="far fa-clock text-emerald-400"></i>
                            <span class="font-medium">Atualizado</span>
                            <span class="text-gray-900">{{ $task->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botão Fechar -->
                <button @click="activeItem = null" 
                        class="relative group shrink-0 text-gray-400 hover:text-gray-600 transition-all duration-300">
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
            
            @if($isEditingDescription)
                <!-- Modo Edição Premium -->
                <div class="space-y-4" wire:key="description-edit-{{ $task->id }}">
                    
                    <div class="relative group">
                        <x-form.textarea
                            wire:model.defer="description"
                            id="task-description-{{ $task->id }}"
                            placeholder="Descreva detalhadamente esta tarefa..."
                            class="h-52 !bg-white/90 !border-gray-200/80 !rounded-xl !shadow-sm focus:!border-emerald-300 focus:!ring-2 focus:!ring-emerald-200/50 transition-all duration-300"
                            @keydown.ctrl.enter="$wire.saveDescription()"
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
                <div class="space-y-3 cursor-pointer group"
                     wire:click="enableDescriptionEdit"
                     wire:key="description-view-{{ $task->id }}">
                     
                    @if(trim($task->description))
                        <div class="relative bg-gradient-to-br from-gray-50/80 to-white rounded-2xl border border-gray-200/80 group-hover:border-emerald-200/80 transition-all duration-500 h-52 overflow-y-auto shadow-sm group-hover:shadow-md">
                            <div class="p-2 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300/50 [&::-webkit-scrollbar-thumb]:rounded-full h-full overflow-y-auto">{!! nl2br(e(trim($task->description))) !!}</div>
                            
                            <!-- Overlay Edição -->
                            <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/50 to-transparent backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all duration-500 rounded-2xl flex items-end justify-center pb-6">
                                <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-emerald-700 text-xs font-medium rounded-full shadow-lg border border-emerald-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                                    <i class="fas fa-pen-fancy"></i>
                                    Editar descrição
                                </span>
                            </div>
                        </div>
                    @else
                        <!-- Placeholder Premium -->
                        <div class="flex flex-col items-center justify-center py-14 px-6 text-gray-400 group rounded-2xl border-2 border-dashed border-gray-200/80 hover:border-emerald-300/80 hover:bg-gradient-to-br hover:from-emerald-50/30 hover:to-white transition-all duration-500 h-52 cursor-pointer bg-white/50 backdrop-blur-sm">
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
                        <i class="fas fa-user-circle text-gray-400"></i>
                        Solicitante
                    </span>
                    <div class="flex-1">
                        <x-form.select-livewire wire:model.live="responsable_id" 
                                :collection="$users" 
                                valueField="id" labelField="name" 
                                :selected="$task->user_id" 
                                variant="inline" size="xs" 
                                class="!bg-transparent !border-0 !text-gray-900 !font-medium !shadow-none !p-0" />
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm hover:border-gray-200 hover:bg-white/90 transition-all">
                    <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                        <i class="fas fa-folder text-gray-400"></i>
                        Categoria
                    </span>
                    <div class="flex-1">
                        <x-form.select-livewire wire:model.live="list_category_id" 
                                :collection="$taskCategories" 
                                valueField="id" labelField="title" 
                                :selected="$task->task_category_id" 
                                variant="inline" />
                    </div>
                </div>
                
                <div class="flex items-center p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm hover:border-gray-200 hover:bg-white/90 transition-all">
                    <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                        <i class="fas fa-flag text-gray-400"></i>
                        Prioridade
                    </span>
                    <div class="flex-1">
                        <x-form.select-livewire wire:model.live="list_priority_id" 
                                :collection="$taskPriorities" 
                                valueField="id" labelField="title" 
                                :selected="$task->task_priority_id" 
                                variant="inline" />
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm">
                    <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                        <i class="fas fa-calendar-plus text-gray-400"></i>
                        Criado em
                    </span>
                    <span class="text-xs font-semibold text-gray-900 bg-gray-100/80 px-3 py-1.5 rounded-lg">
                        {{ $task->created_at->format('d/m/Y') }}
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white/70 rounded-xl border border-gray-100/80 shadow-sm">
                    <span class="text-xs font-medium text-gray-500 flex items-center gap-2">
                        <i class="fas fa-play text-gray-400"></i>
                        Início
                    </span>
                    <span class="text-xs font-semibold text-gray-900 bg-gray-100/80 px-3 py-1.5 rounded-lg">
                        {{ $task->started_at?->format('d/m/Y') ?? '—' }}
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-3 rounded-xl border shadow-sm transition-all duration-300 col-span-1 group {{ $task->deadline_at && $task->deadline_at->isPast() ? 'bg-gradient-to-br from-rose-50/90 to-rose-100/70 border-rose-200/80 text-rose-700' : 'bg-white/90 border-gray-200/80 text-emerald-700 hover:border-emerald-200/80' }}">
                    
                    <!-- Label com ícone animado -->
                    <span class="text-xs font-medium flex items-center gap-2 {{ $task->deadline_at && $task->deadline_at->isPast() ? 'text-rose-600' : 'text-gray-600' }}">
                        <i class="fas fa-hourglass-end transition-transform duration-300 group-hover:rotate-12 {{ $task->deadline_at && $task->deadline_at->isPast() ? 'text-rose-500' : 'text-emerald-500' }}"></i>
                        Prazo final
                    </span>

                    @if($isEditingDeadline)
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
                        <div class="flex items-center cursor-pointer group/edit" wire:click="enableDeadlineEdit" wire:key="deadline-view-{{ $task->id }}">
                            
                            <!-- Ícone de edição (aparece no hover) -->
                            <i class="fas fa-pencil-alt text-[10px] text-gray-400 opacity-0 group-hover/edit:opacity-100 transition-opacity duration-300 {{ $task->deadline_at && $task->deadline_at->isPast() ? 'hover:text-rose-600' : 'hover:text-emerald-600' }}"></i>
                            
                            <!-- Valor com hover reveal -->
                            <span class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-300" >
                                @if($task->deadline_at)
                                    {{ $task->deadline_at->format('d/m/Y') }}
                                    <!-- Badge contextual -->
                                    @php
                                        $daysLeft = $task->deadline_at ? now()->diffInDays($task->deadline_at, false) : null;
                                    @endphp
                                    
                                    @if($task->deadline_at->isPast())
                                        <span class="ml-1.5 text-[10px] font-medium text-rose-700 bg-rose-200/60 px-1.5 py-0.5 rounded-full uppercase">
                                            atrasado
                                        </span>
                                    @elseif($daysLeft <= 3)
                                        <span class="ml-1.5 text-[10px] font-medium text-amber-700 bg-amber-100/80 px-1.5 py-0.5 rounded-full">
                                            {{ $daysLeft }} {{ $daysLeft == 1 ? 'dia' : 'dias' }}
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
                
                <button class="text-xs text-gray-400 hover:text-gray-600 flex items-center gap-1.5 px-3 py-1.5 bg-white/70 rounded-full border border-gray-200 hover:border-gray-300 transition-all">
                    <i class="fas fa-filter text-[10px]"></i>
                    Filtrar
                </button>
            </div>

            <!-- Nova Atividade -->
            <div class="relative mb-6 group">
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        <div class="w-9 h-9 bg-gradient-to-br from-emerald-100 to-green-100 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                            <span class="text-xs font-semibold text-emerald-700">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl border border-gray-200/80 shadow-sm group-focus-within:border-emerald-300 group-focus-within:ring-4 group-focus-within:ring-emerald-100/50 transition-all">
                            <x-form.textarea 
                                placeholder="Escreva um comentário... @ para mencionar" 
                                class="!border-0 !shadow-none !bg-transparent !min-h-[80px] !p-4 !text-sm !resize-none focus:!ring-0" />
                            <div class="flex items-center justify-end p-2 border-t border-gray-100">
                                <button class="text-xs bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-4 py-2 rounded-full font-medium hover:from-emerald-600 hover:to-emerald-700 shadow-md hover:shadow-lg transition-all">
                                    <i class="fas fa-paper-plane mr-1.5 text-xs"></i>
                                    Comentar
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-2 text-[10px] text-gray-400">
                            <i class="fas fa-at"></i>
                            <span>Mencione colegas</span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <i class="fas fa-link"></i>
                            <span>Anexar arquivo</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Atividades -->
            <div class="space-y-4 mt-6">
                <div class="relative pl-8 pb-4 border-l-2 border-emerald-200/60 last:border-l-0">
                    <div class="absolute left-[-8px] top-0 w-4 h-4 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-full border-2 border-white shadow-md"></div>
                    
                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-gray-100/80 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-gray-900">Sistema</span>
                                <span class="text-[10px] text-gray-400">•</span>
                                <span class="text-[10px] text-gray-500">2 horas atrás</span>
                            </div>
                            <span class="px-2 py-1 bg-gray-100/80 text-[10px] font-medium text-gray-600 rounded-full border border-gray-200">
                                Atualização
                            </span>
                        </div>
                        <p class="text-xs text-gray-600">
                            Status alterado de "Em Andamento" para "Em Revisão"
                        </p>
                    </div>
                </div>
                
                <div class="relative pl-8 pb-4 border-l-2 border-emerald-200/60 last:border-l-0">
                    <div class="absolute left-[-8px] top-0 w-4 h-4 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-full border-2 border-white shadow-md"></div>
                    
                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-gray-100/80 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-gray-900">João Silva</span>
                                <span class="text-[10px] text-gray-400">•</span>
                                <span class="text-[10px] text-gray-500">1 dia atrás</span>
                            </div>
                            <span class="px-2 py-1 bg-purple-100/80 text-[10px] font-medium text-purple-700 rounded-full border border-purple-200">
                                Comentário
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 mb-2">
                            Ajustei a documentação conforme solicitado. Favor revisar.
                        </p>
                        <div class="flex items-center gap-2 text-[10px] text-gray-400 border-t border-gray-100 pt-2 mt-1">
                            <i class="fas fa-paperclip"></i>
                            <span>documento_v1.3.pdf</span>
                            <span class="text-gray-300">•</span>
                            <span>2.4 MB</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ver mais -->
            <div class="text-center mt-6">
                <button class="text-xs text-gray-500 hover:text-gray-700 bg-white/70 hover:bg-white px-4 py-2 rounded-full border border-gray-200 hover:border-gray-300 transition-all shadow-sm inline-flex items-center gap-2">
                    <i class="fas fa-history"></i>
                    Ver todas as atividades
                    <span class="text-[10px] text-gray-400">12</span>
                </button>
            </div>
        </section>
    </div>

    <!-- FOOTER - Ações Premium -->
    <footer class="sticky bottom-0 bg-white/95 backdrop-blur-xl backdrop-saturate-150 border-t border-green-700/50 shadow-lg">
        <div class="px-6 py-4">
            <div class="flex items-center justify-end gap-3">
                
                <div class="flex items-center gap-2">
                    <x-button @click="activeItem = null" icon="fas fa-times" text="Fechar" variant="gray_outline" />
                </div>

                @if ($task->taskSteps->count() < 1 || $task->taskStepsFinished->count() == $task->taskSteps->count())
                    <x-button icon="fas fa-check" text="Marca como concluído" variant="green_outline" />
                @endif
            </div>
        </div>
    </footer>
</div>