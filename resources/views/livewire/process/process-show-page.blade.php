<div>
    <x-page.header title="Detalhes do Processo" subtitle="{{ $process->title }}" icon="fa-solid fa-folder-open">
        <x-slot name="button">
            <x-button href="{{ route('process.index') }}" text="Voltar" icon="fa-solid fa-arrow-left" variant="gray_outline" />
        </x-slot>
    </x-page.header>

    @php
        $stepOrganizations = ($process->workflow?->workflowSteps ?? collect())
            ->pluck('organization.title')
            ->filter()
            ->unique()
            ->values();

        $currentTimelineStep = $timelineSteps->firstWhere('state', 'Em andamento');
        $totalSteps = $timelineSteps->count();
        $completedSteps = $timelineSteps->where('state', 'Concluida')->count();
        $currentStateLabel = $currentTimelineStep['state'] ?? ($timelineSteps->last()['state'] ?? 'Pendente');
        $progressPercentage = $totalSteps > 0 ? (int) round(($completedSteps / $totalSteps) * 100) : 0;
    @endphp

    <div class="space-y-6">
        <div class="mt-5">
            <div class="overflow-x-auto bg-gray-100/50 pb-2 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-4 border-b border-gray-200 pb-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">Fluxo vinculado</p>
                            <h2 class="mt-1 text-lg font-semibold text-gray-900">{{ $process->workflow?->title ?? 'Fluxo nao definido' }}</h2>
                        </div>

                        <div class="min-w-[240px] lg:max-w-xs">
                            <div class="flex items-center justify-between gap-3 text-xs">
                                <span class="font-medium text-gray-500">Progresso do fluxo</span>
                                <span class="font-semibold text-gray-900">{{ $completedSteps }}/{{ $totalSteps }} etapa(s)</span>
                            </div>

                            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-200">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-700 transition-all duration-500"
                                    style="width: {{ $progressPercentage }}%;"
                                ></div>
                            </div>

                            <p class="mt-2 text-[11px] uppercase tracking-[0.14em] text-gray-400">{{ $progressPercentage }}% concluido</p>
                        </div>
                    </div>
                </div>

                @if ($timelineSteps->isNotEmpty())
                    <div class="flex min-w-max items-start">
                        @foreach ($timelineSteps as $timelineStep)
                            <div class="flex items-start">
                                @php
                                    $stepState = (string) ($timelineStep['state'] ?? '');
                                    $isCompleted = $stepState === 'Concluida';
                                    $isInProgress = $stepState === 'Em andamento';
                                    $isOverdue = (bool) ($timelineStep['is_overdue'] ?? false);
                                    $stateClass = $isCompleted
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : ($isInProgress
                                            ? ($isOverdue ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')
                                            : 'bg-slate-100 text-slate-700');

                                    $cardClass = $isCompleted
                                        ? 'border-emerald-200 bg-emerald-50/60'
                                        : ($isInProgress
                                            ? ($isOverdue ? 'border-red-200 bg-red-50/60' : 'border-amber-200 bg-amber-50/60')
                                            : 'border-slate-200 bg-slate-50/60');
                                @endphp

                                <article class="w-72 shrink-0 overflow-hidden rounded-2xl border shadow-sm {{ $cardClass }}">
                                    <header class="flex items-center justify-between gap-2 border-b border-gray-200/60 px-4 py-2 {{ $stateClass }}">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase">
                                            {{ $timelineStep['state'] }}
                                            @if ($timelineStep['completed_with_delay'] ?? false)
                                                <span
                                                    class="inline-flex size-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold text-white"
                                                    title="Etapa concluida com atraso de {{ $timelineStep['completed_delay_days'] }} dia(s)."
                                                >
                                                    A
                                                </span>
                                            @endif
                                        </span>
                                        <p class="text-[11px] font-medium text-gray-500">
                                            {{ $timelineStep['deadline_days'] ? $timelineStep['deadline_days'].' dia(s)' : 'Prazo n/a' }}
                                        </p>
                                    </header>

                                    <div class="px-4 py-2">
                                        <h3 class="truncate text-xs font-semibold text-gray-900">{{ $timelineStep['title'] }}</h3>
                                        <p class="mt-0.5 text-xs text-gray-500">Setor: {{ $timelineStep['organization_title'] ?? 'Nao definido' }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500">Usuario: {{ $timelineStep['owner_name'] }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500">
                                            Inicio: {{ data_get($timelineStep, 'started_at')?->format('d/m/Y H:i') ?? '-' }}
                                        </p>
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
                @else
                    <p class="py-6 text-sm text-gray-500">Nenhuma etapa vinculada ao processo.</p>
                @endif
            </div>
            
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-end pt-4">

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 xl:flex xl:flex-wrap xl:justify-end">
                    <x-button
                        type="button"
                        text="Comentar"
                        icon="fa-solid fa-comment"
                        variant="gray_outline"
                        wire:click="openCommentModal"
                        wire:loading.attr="disabled"
                        wire:target="openCommentModal"
                    />
                    <x-button
                        type="button"
                        text="Atribuir responsavel"
                        icon="fa-solid fa-user-pen"
                        variant="blue_outline"
                        wire:click="openAssignOwnerModal"
                        wire:loading.attr="disabled"
                        wire:target="openAssignOwnerModal"
                        :disabled="! $canManageStepActions"
                    />
                    <x-button
                        type="button"
                        text="Retroceder etapa"
                        icon="fa-solid fa-arrow-left"
                        variant="yellow_outline"
                        wire:click="openDispatchModal('retreat')"
                        wire:loading.attr="disabled"
                        wire:target="openDispatchModal"
                        :disabled="! $canManageStepActions"
                    />
                    <x-button
                        type="button"
                        text="Avancar etapa"
                        icon="fa-solid fa-arrow-right"
                        variant="green_solid"
                        wire:click="openDispatchModal('advance')"
                        wire:loading.attr="disabled"
                        wire:target="openDispatchModal"
                        :disabled="! $canManageStepActions"
                    />
                </div>
            </div>
        </div>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8 space-y-6">
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="border-b border-gray-200 pb-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">Assunto</p>
                        <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">{{ $process->title }}</h2>
                    </div>

                    <div class="mt-4 min-h-32 whitespace-pre-line text-sm leading-7 text-gray-700">
                        {{ $process->description ?: 'Sem descricao cadastrada.' }}
                    </div>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-2 border-b border-gray-200 pb-3">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">Eventos</p>
                            <h3 class="mt-1 text-lg font-semibold text-gray-900">Historico do processo</h3>
                        </div>
                        <span class="text-xs text-gray-500">{{ $process->events->count() }} evento(s)</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($process->events->sortBy('event_number') as $event)
                            @php
                                $eventType = \App\Enums\Process\ProcessEventType::tryFrom((string) ($event->event_type ?? ''));
                                $eventBadgeClass = $eventType?->badgeClass() ?? 'bg-slate-100 text-slate-700';
                                $eventLabel = $eventType?->label() ?? 'Evento';
                            @endphp

                            <article class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold {{ $eventBadgeClass }}">
                                                #{{ $event->event_number }}
                                            </span>
                                            <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-gray-500">
                                                {{ $eventLabel }}
                                            </span>
                                        </div>

                                        <p class="text-sm leading-6 text-gray-800">{{ $event->description ?: 'Sem descricao.' }}</p>
                                        <p class="text-xs text-gray-500">Usuario: {{ $event->user?->name ?? 'Sistema' }}</p>
                                    </div>

                                    <span class="shrink-0 text-xs text-gray-500">{{ $event->created_at?->format('d/m/Y H:i') }}</span>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-gray-500">Nenhum evento registrado para este processo.</p>
                        @endforelse
                    </div>
                </article>
            </div>

            <aside class="xl:col-span-4 space-y-6">
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm mt-4 space-y-4">
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">Abertura</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $process->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">Estado atual</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $currentStateLabel }}</dd>
                    </div>

                    <div class="border-b border-gray-200 pb-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">Setores</p>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse ($stepOrganizations as $organizationTitle)
                            <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700">{{ $organizationTitle }}</span>
                        @empty
                            <span class="text-sm text-gray-500">Nenhum setor vinculado nas etapas.</span>
                        @endforelse
                    </div>
                </article>
            </aside>
        </section>
    </div>

    <x-modal :show="$showModal" maxWidth="max-w-2xl">
        @if ($modalKey === 'modal-process-dispatch')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    {{ $pendingTransition === 'advance' ? 'Avancar Etapa' : 'Retroceder Etapa' }}
                </h2>
            </x-slot>

            <form wire:submit.prevent="confirmStepTransition" class="space-y-4">
                @if ($pendingTransition === 'advance' && $requiresOwnerBeforeAdvance)
                    <div class="space-y-1">
                        <x-form.label value="Usuario responsavel pela etapa" />
                        <x-form.select-livewire
                            wire:model.defer="assignedOwnerId"
                            name="assignedOwnerId"
                            placeholder="Selecione o usuario responsavel"
                            :options="$owners->map(fn ($owner) => ['value' => $owner->id, 'label' => $owner->name])->values()->all()"
                        />
                        <x-form.error for="assignedOwnerId" />
                        @if ($owners->isEmpty())
                            <p class="text-xs text-amber-600">Nao ha usuarios vinculados ao setor da etapa atual para concluir o avanco.</p>
                        @else
                            <p class="text-xs text-gray-500">Selecione quem foi o responsavel pela etapa antes de avancar o processo.</p>
                        @endif
                    </div>
                @endif

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
                        :disabled="$pendingTransition === 'advance' && $requiresOwnerBeforeAdvance && $owners->isEmpty()"
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
