<div>
    <x-page.header title="Detalhes do Processo" subtitle="{{ $process->title }}" icon="fa-solid fa-folder-open">
        <x-slot name="button">
            <x-button href="{{ route('process.index') }}" text="Voltar" icon="fa-solid fa-arrow-left" variant="gray_outline" />
        </x-slot>
    </x-page.header>

    <div class="space-y-4">
        <!-- Timeline -->
        <div class="mt-5 overflow-x-auto pb-2 border border-gray-200 rounded-xl p-2 bg-gray-100/50 shadow">
            @if ($timelineSteps->isNotEmpty())
                <div class="flex min-w-max items-start">
                    @foreach ($timelineSteps as $timelineStep)
                        <div class="flex items-start">
                            @php
                                $stepState = (string) ($timelineStep['state'] ?? '');
                                $isCompleted = $stepState === 'Concluida';
                                $isInProgress = $stepState === 'Em andamento';

                                $stateClass = $isCompleted
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : ($isInProgress ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700');

                                $cardClass = $isCompleted
                                    ? 'border-emerald-200 bg-emerald-50/60'
                                    : ($isInProgress ? 'border-amber-200 bg-amber-50/60' : 'border-slate-200 bg-slate-50/60');
                            @endphp
                            <article class="w-72 shrink-0 rounded-2xl border shadow-sm overflow-hidden {{ $cardClass }}">
                                <header class="flex items-center justify-between gap-2 border-b border-gray-200/60 px-4 py-2 {{ $stateClass }}">                                   
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase">
                                        {{ $timelineStep['state'] }}
                                    </span>
                                    <p class="text-[11px] font-medium text-gray-500">
                                        {{ $timelineStep['deadline_days'] ? $timelineStep['deadline_days'].' dia(s)' : 'Prazo n/a' }}
                                    </p>
                                </header>
                                
                                <div class="px-4 py-2">
                                    <h3 class="text-xs font-semibold text-gray-900 truncate">{{ $timelineStep['title'] }}</h3>
                                    <p class="mt-0.5 text-xs text-gray-500">Setor: {{ $timelineStep['organization_title'] ?? 'Nao definido' }}</p>
                                </div>
                            </article>

                            @if (! $loop->last)
                                <div class="flex h-20 w-4 shrink-0 items-center justify-center">
                                    <div class="h-[2px] w-full bg-gradient-to-r from-emerald-200 via-emerald-400 to-emerald-200"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>        

        <!-- Informacao de Abertura -->
        <div class="grid grid-cols-1 xl:grid-cols-12 rounded-xl border border-gray-200shadow overflow-hidden">
            <!-- Aside com informacoes do processo -->
            <aside class="space-y-4 xl:col-span-3 p-4 md:border-r border-gray-400">
                <section>
                    <div class="flex items-start gap-2 pb-3 border-b border-gray-300">
                        <div class="size-10 rounded-full bg-stone-300 overflow-hidden">
                            <img
                                src="{{ $process->owner?->avatar ? asset('storage/'.$process->owner->avatar) : asset('asset/img/favicon-infosaude-150-150.png') }}"
                                alt="Avatar do usuario"
                                class="h-full w-full rounded-full object-cover"
                            >
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs">{{ $process->owner?->name ?? 'Nao definido' }} - <span class="rounded-lg py-1 px-1.5 text-[9px] border border-gray-300 font-semibold">{{ $process->owner?->organization?->acronym ?? 'Nao definido' }}</span></p>
                            <p class="text-xs">{{ $process->owner?->organization?->title ?? 'Nao definido' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1 py-3 border-b border-gray-300">
                        <p class="text-xs text-gray-500">Fluxo vinculado</p>
                        <p class="text-xs font-semibold text-gray-900">{{ $process->workflow?->title ?? 'Nao definido' }}</p>
                    </div>
                </section>

                <section>                    
                    <div class="flex items-center gap-0.5">
                        <p class="text-xs font-semibold text-gray-500">Data de abertura: </p>
                        <p class="text-xs text-gray-900">{{ $process->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mt-1 space-y-3 text-sm">
                        <div class="flex items-center gap-0.5">
                            <p class="text-xs font-semibold text-gray-500">Setor: </p>
                            <p class="text-xs text-gray-900">{{ $process->organization?->title ?? 'Nao definido' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Setores nas etapas</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @php
                                    $stepOrganizations = ($process->workflow?->workflowSteps ?? collect())
                                        ->pluck('organization.title')
                                        ->filter()
                                        ->unique()
                                        ->values();
                                @endphp

                                @forelse ($stepOrganizations as $organizationTitle)
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] font-medium text-slate-700">{{ $organizationTitle }}</span>
                                @empty
                                    <span class="text-xs text-gray-500">Nenhum setor vinculado nas etapas.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>
            </aside>

            <!-- Conteudo principal com detalhes do processo -->
            <div class="xl:col-span-9 bg-white">

                <section class="bg-white p-4">              
                    <h2 class="mt-1 text-xl text-gray-900 border-b border-gray-200"><span class="font-semibold">Assunto: </span>{{ $process->title }}</h2>

                    <div class="text-sm leading-6 text-gray-700 whitespace-pre-line min-h-24">
                        {{ $process->description ?: 'Sem descricao cadastrada.' }} 
                    </div>
                </section>
            </div>
        </div>

        <!-- Eventos do Processo -->
        <section class="rounded-xl border border-gray-300 bg-white shadow p-4">
            <div class="flex items-center justify-between gap-2 pb-2 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800 uppercase">Eventos do processo</h3>
                <span class="text-[11px] text-gray-500">{{ $process->events->count() }} evento(s)</span>
            </div>

            <div class="mt-3 space-y-2">
                @forelse ($process->events->sortBy('event_number') as $event)
                    @php
                        $eventType = \App\Enums\Process\ProcessEventType::tryFrom((string) ($event->event_type ?? ''));
                        $eventBadgeClass = $eventType?->badgeClass() ?? 'bg-slate-100 text-slate-700';
                        $eventLabel = $eventType?->label() ?? 'Evento';
                    @endphp

                    <article class="rounded-lg border border-gray-200 bg-gray-50/70 px-3 py-2">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $eventBadgeClass }}">
                                    #{{ $event->event_number }}
                                </span>
                                <span class="text-[10px] font-semibold uppercase text-gray-500">
                                    {{ $eventLabel }}
                                </span>
                            </div>
                            <span class="text-[11px] text-gray-500">{{ $event->created_at?->format('d/m/Y H:i') }}</span>
                        </div>

                        <p class="mt-1 text-sm text-gray-800">{{ $event->description ?: 'Sem descricao.' }}</p>
                        <p class="mt-1 text-[11px] text-gray-500">Usuario: {{ $event->user?->name ?? 'Sistema' }}</p>
                    </article>
                @empty
                    <p class="text-sm text-gray-500">Nenhum evento registrado para este processo.</p>
                @endforelse
            </div>
        </section>
        
        <!-- Acoes disponiveis -->
        <div class="flex justify-end">
            <div class="inline-flex justify-center items-center gap-2 bg-emerald-800 py-2 px-4 rounded-lg divide-x divide-emerald-100/20">
                <div>
                    <x-button
                        type="button"
                        text="Comentar"
                        icon="fa-solid fa-comment"
                        variant="white_text"
                        fullWidth="true"
                        wire:click="openCommentModal"
                        wire:loading.attr="disabled"
                        wire:target="openCommentModal"
                    />
                </div>
                <div class="pl-2">
                    <x-button
                        type="button"
                        text="Atribuir responsavel"
                        icon="fa-solid fa-user-pen"
                        variant="white_text"
                        fullWidth="true"
                        wire:click="openAssignOwnerModal"
                        wire:loading.attr="disabled"
                        wire:target="openAssignOwnerModal"
                        :disabled="! $canManageStepActions"
                    />
                </div>
                <div class="pl-2">                    
                    <x-button
                        type="button"
                        text="Retroceder etapa"
                        icon="fa-solid fa-arrow-left"
                        variant="white_text"
                        fullWidth="true"
                        wire:click="openDispatchModal('retreat')"
                        wire:loading.attr="disabled"
                        wire:target="openDispatchModal"
                        :disabled="! $canManageStepActions"
                    />
                </div>
                <div class="pl-2">
                    <x-button
                        type="button"
                        text="Avancar etapa"
                        icon="fa-solid fa-arrow-right"
                        variant="white_text"
                        fullWidth="true"
                        wire:click="openDispatchModal('advance')"
                        wire:loading.attr="disabled"
                        wire:target="openDispatchModal"
                        :disabled="! $canManageStepActions"
                    />
                </div>
            </div>
        </div>
    </div>

    <x-modal :show="$showModal" maxWidth="max-w-2xl">
        @if ($modalKey === 'modal-process-dispatch')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    {{ $pendingTransition === 'advance' ? 'Avancar Etapa' : 'Retroceder Etapa' }}
                </h2>
            </x-slot>

            <form wire:submit.prevent="confirmStepTransition" class="space-y-4">
                <div class="space-y-1">
                    <x-form.label value="Justificativa (despacho)" />
                    <x-form.textarea
                        wire:model.defer="dispatchComment"
                        name="dispatchComment"
                        rows="4"
                        placeholder="Informe o motivo desta movimentacao de etapa..."
                    />
                    <x-form.error for="dispatchComment" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" />
                    <x-button
                        type="submit"
                        :text="$pendingTransition === 'advance' ? 'Confirmar e Avancar' : 'Confirmar e Retroceder'"
                        :icon="$pendingTransition === 'advance' ? 'fa-solid fa-arrow-right' : 'fa-solid fa-arrow-left'"
                    />
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-process-comment')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Novo Comentario</h2>
            </x-slot>

            <form wire:submit.prevent="saveComment" class="space-y-4">
                <div class="space-y-1">
                    <x-form.label value="Comentario (despacho)" />
                    <x-form.textarea
                        wire:model.defer="commentText"
                        name="commentText"
                        rows="4"
                        placeholder="Informe o comentario do despacho..."
                    />
                    <x-form.error for="commentText" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" />
                    <x-button type="submit" text="Salvar comentario" icon="fa-solid fa-save" />
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-process-assign-owner')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Atribuir Responsavel</h2>
            </x-slot>

            <form wire:submit.prevent="assignOwner" class="space-y-4">
                <div class="space-y-1">
                    <x-form.label value="Responsavel" />
                    <x-form.select-livewire
                        wire:model.defer="assignedOwnerId"
                        name="assignedOwnerId"
                        :options="$owners->map(fn ($owner) => ['value' => $owner->id, 'label' => $owner->name])->values()->all()"
                    />
                    <x-form.error for="assignedOwnerId" />
                    @if ($owners->isEmpty())
                        <p class="text-xs text-amber-600">Nao ha usuarios vinculados ao setor da etapa atual.</p>
                    @endif
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" />
                    <x-button
                        type="submit"
                        text="Confirmar atribuicao"
                        icon="fa-solid fa-user-check"
                        :disabled="$owners->isEmpty()"
                    />
                </div>
            </form>
        @endif
    </x-modal>
</div>
