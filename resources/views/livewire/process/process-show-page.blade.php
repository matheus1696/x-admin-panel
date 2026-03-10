<div class="space-y-4">
    <x-alert.flash />

    <x-page.header title="Detalhes do Processo" subtitle="{{ $process->title }}" icon="fa-solid fa-folder-open">
        <x-slot name="button">
            <x-button href="{{ route('process.index') }}" text="Voltar" icon="fa-solid fa-arrow-left" variant="gray_outline" />
        </x-slot>
    </x-page.header>

    <section>

        <div class="mt-5 overflow-x-auto pb-2">
            @php
                $workflowSteps = $process->workflow?->workflowSteps ?? collect();
            @endphp

            @if ($workflowSteps->isNotEmpty())
                <div class="flex min-w-max items-start">
                    @foreach ($workflowSteps as $timelineStep)
                        <div class="flex items-start">
                            <article class="w-72 shrink-0 rounded-2xl border border-gray-200 bg-white p-4">
                                <div class="flex items-start justify-between gap-1">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $timelineStep->title }}</h3>

                                    @if ($timelineStep->required)
                                        <span class="rounded-full bg-amber-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-amber-700">
                                            Obrigatoria
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <p class="mt-2 text-xs text-gray-500">Setor: {{ $timelineStep->organization?->title ?? 'Nao definido' }}</p>
                                    <p class="mt-1 text-xs text-gray-500">Prazo: {{ $timelineStep->deadline_days ? $timelineStep->deadline_days.' dia(s)' : 'Nao informado' }}</p>
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
    </section>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
        <aside class="space-y-4 xl:col-span-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <section>
                <div class="flex items-start gap-2 pb-3 border-b border-gray-200/50">
                    <div class="size-10 bg-gray-400">
                        <img src="" alt="Avatar Usuário" class="h-full w-full rounded-full object-cover">
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
                <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-gray-500">Setores</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500">Setor principal</p>
                        <p class="font-semibold text-gray-900">{{ $process->organization?->title ?? 'Nao definido' }}</p>
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
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-700">{{ $organizationTitle }}</span>
                            @empty
                                <span class="text-xs text-gray-500">Nenhum setor vinculado nas etapas.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>
        </aside>

        <main class="space-y-4 xl:col-span-9">
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3 pb-3 border-b border-gray-200/50">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.2em] text-gray-500">Processo</p>
                        <h2 class="mt-1 text-xl font-semibold text-gray-900">{{ $process->title }}</h2>
                    </div>
                    <span class="rounded-full border border-gray-200 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-600">
                        {{ str($process->status)->replace('_', ' ') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4 pt-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs text-gray-500">Tipo do processo</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">Nao definido</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Fluxo atual</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $process->workflow?->title ?? 'Nao definido' }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="pb-3 border-b border-gray-200/50">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-gray-500">Descricao</p>
                    <p class="mt-1 text-sm text-gray-500">Resumo principal do processo</p>
                </div>

                <div class="pt-4 text-sm leading-6 text-gray-700 whitespace-pre-line min-h-24">
                    {{ $process->description ?: 'Sem descricao cadastrada.' }}
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="pb-3 border-b border-gray-200/50">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-gray-500">Despacho</p>
                    <p class="mt-1 text-sm text-gray-500">Manifestacoes e encaminhamentos do processo</p>
                </div>

                <div class="mt-4 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-sm text-gray-500">
                    Nenhum despacho registrado ate o momento.
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="pb-3 border-b border-gray-200/50">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-gray-500">Novo comentario</p>
                    <p class="mt-1 text-sm text-gray-500">Registre uma observacao antes de avancar a etapa</p>
                </div>

                <div class="pt-4 space-y-4">
                    <x-form.textarea
                        wire:model.defer="commentDraft"
                        rows="4"
                        placeholder="Escreva um comentario para o processo..."
                    />

                    <div class="flex flex-col gap-3 border-t border-gray-200/50 pt-4 md:flex-row md:items-center md:justify-between">
                        <p class="text-xs text-gray-500">
                            O envio do comentario e a progressao de etapa ainda nao possuem regra operacional implementada neste modulo.
                        </p>

                        <button
                            type="button"
                            disabled
                            class="inline-flex items-center justify-center rounded-xl bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-500 cursor-not-allowed"
                        >
                            Passar para a proxima etapa
                        </button>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="pb-3 border-b border-gray-200/50">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-gray-500">Documentos relacionados</p>
                    <p class="mt-1 text-sm text-gray-500">Arquivos e referencias vinculadas ao processo</p>
                </div>

                <div class="mt-4 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-sm text-gray-500">
                    Nenhum documento relacionado disponivel na estrutura atual.
                </div>
            </section>
        </main>
    </div>
</div>
