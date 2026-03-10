<div>
    <x-alert.flash />

    <x-page.header title="Detalhes do Processo" subtitle="{{ $process->title }}" icon="fa-solid fa-folder-open">
        <x-slot name="button">
            <x-button href="{{ route('process.index') }}" text="Voltar" icon="fa-solid fa-arrow-left" variant="gray_outline" />
        </x-slot>
    </x-page.header>

    <div class="space-y-4">
        <!-- Timeline -->
        <div class="mt-5 overflow-x-auto pb-2">
            @if ($timelineSteps->isNotEmpty())
                <div class="flex min-w-max items-start">
                    @foreach ($timelineSteps as $timelineStep)
                        <div class="flex items-start">
                            @php
                                $stepState = (string) ($timelineStep['state'] ?? '');
                                $isCompleted = in_array($stepState, ['ConcluÃ­da', 'Concluida'], true);
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
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-12 rounded-xl border border-gray-200 bg-gray-50 shadow-sm overflow-hidden">
            <!-- Aside com informacoes do processo -->
            <aside class="space-y-4 xl:col-span-3 p-4">
                <section>
                    <div class="flex items-start gap-2 pb-3 border-b border-gray-200/50">
                        <div class="size-10 rounded-full bg-gray-200 overflow-hidden">
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
                    
                    <div class="flex items-center gap-1 py-3 border-b border-gray-200/50">
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
                    <h2 class="pb-1 mt-1 text-xl font-semibold text-gray-900 border-b border-gray-200">{{ $process->title }}</h2>

                    <div class="pt-1 text-xs leading-6 text-gray-700 whitespace-pre-line min-h-24">
                        {{ $process->description ?: 'Sem descricao cadastrada.' }} 
                    </div>
                </section>
            </div>
        </div>

        @can('process.manage')
            @if ($canRetreatStep || $canAdvanceStep)
                <div class="flex items-center justify-end gap-2">
                    @if ($canRetreatStep)
                        <x-button
                            type="button"
                            wire:click="retreatStep"
                            text="Retroceder Etapa"
                            icon="fa-solid fa-backward-step"
                            variant="gray_outline"
                        />
                    @endif

                    @if ($canAdvanceStep)
                        <x-button
                            type="button"
                            wire:click="advanceStep"
                            text="Avancar Etapa"
                            icon="fa-solid fa-forward-step"
                        />
                    @endif
                </div>
            @endif
        @endcan

    </div>
</div>