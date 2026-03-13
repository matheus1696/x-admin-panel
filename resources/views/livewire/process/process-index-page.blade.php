<div>
    @php
        $statusCollection = collect($statuses);
        $selectedStatus = (string) ($filters['status'] ?? \App\Models\Process\ProcessStatus::IN_PROGRESS);
        $totalProcesses = (int) $processes->count();
        $visibleProcesses = (int) $processes->count();
        $overdueVisible = $processes->filter(fn ($process) => $processIdsWithOverdueCurrentStep->contains((int) $process->id))->count();
        $withUpdatesVisible = $processes->filter(fn ($process) => $processIdsWithUnseenUpdates->contains((int) $process->id))->count();
        $activeQuickFilters = collect([
            ['enabled' => (bool) ($filters['overdue_only'] ?? false), 'label' => 'Somente atrasados'],
            ['enabled' => (bool) ($filters['my_sectors_only'] ?? false), 'label' => 'Somente meu setor'],
        ])->where('enabled', true)->pluck('label')->values();
    @endphp

    <x-page.header title="Gestao de Processos" subtitle="Acompanhe fila atual, prioridade e andamento dos processos com foco na operacao do dia" icon="fa-solid fa-folder-tree" >
        <x-slot name="button">
            <div class="flex items-center gap-2">
                @can('process.create')
                    <x-button text="Novo Processo" icon="fa-solid fa-plus" wire:click="create" />
                @endcan
            </div>
        </x-slot>
    </x-page.header>

    <x-page.filter title="Filtros" showClear="true" clearAction="resetFilters">
        <div class="col-span-12 md:col-span-8">
            <x-form.label value="Titulo" />
            <x-form.input wire:model.live.debounce.400ms="filters.title" placeholder="Buscar por titulo..." />
        </div>

        <div class="col-span-12 md:col-span-4">
            <x-form.label value="Setor" />
            <x-form.select-livewire
                wire:model.live="filters.organization_id"
                name="filters.organization_id"
                :options="$organizations->map(fn($organization) => ['value' => $organization->id, 'label' => $organization->acronym ? $organization->acronym.' - '.$organization->title : $organization->title])->prepend(['value' => '', 'label' => 'Todos'])->values()->all()"
            />
        </div>

    </x-page.filter>

    <section class="overflow-hidden rounded-3xl border border-emerald-900/10 bg-white shadow-sm mb-5">
        <div class="border-b border-emerald-100 bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 px-6 py-5 text-white">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <h2 class="text-2xl font-semibold tracking-tight">Infomações dos processos</h2>
                    <p class="text-sm text-white/80">
                        Leia rapidamente o que exige acao, o que recebeu atualizacao e qual recorte esta aplicado agora.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <article class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur-sm">
                        <p class="text-[10px] font-semibold uppercase text-white/65">Total</p>
                        <p class="text-xl font-bold text-white">{{ $totalProcesses }}</p>
                    </article>
                    <article class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur-sm">
                        <p class="text-[10px] font-semibold uppercase text-white/65">Na tela</p>
                        <p class="text-xl font-bold text-white">{{ $visibleProcesses }}</p>
                    </article>
                    <article class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur-sm">
                        <p class="text-[10px] font-semibold uppercase text-white/65">Atrasados</p>
                        <p class="text-xl font-bold text-white">{{ $overdueVisible }}</p>
                    </article>
                    <article class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur-sm">
                        <p class="text-[10px] font-semibold uppercase text-white/65">Novidades</p>
                        <p class="text-xl font-bold text-white">{{ $withUpdatesVisible }}</p>
                    </article>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 bg-gradient-to-b from-white to-slate-50/50 px-6 py-5 xl:grid-cols-[2fr_1fr]">
            <div class="space-y-3">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Status do recorte</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($statuses as $status)
                        @php
                            $isSelected = $selectedStatus === $status['code'];
                            $isActiveStatus = $status['code'] === \App\Models\Process\ProcessStatus::IN_PROGRESS;
                            $buttonClass = $isSelected
                                ? 'border-emerald-700 bg-emerald-700 text-white shadow-sm'
                                : ($isActiveStatus
                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:border-emerald-300'
                                    : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900');
                        @endphp

                        <button
                            type="button"
                            wire:click="$set('filters.status', '{{ $status['code'] }}')"
                            class="rounded-full border px-3.5 py-2 text-xs font-semibold transition {{ $buttonClass }}"
                        >
                            {{ $status['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">                
                <p class="text-right text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Filtro Falicitados</p>
                <div class="flex flex-wrap justify-end gap-2">
                    <button
                        type="button"
                        wire:click="toggleQuickFilter('overdue_only')"
                        class="rounded-full border px-3.5 py-2 text-xs font-semibold transition
                            {{ (bool) ($filters['overdue_only'] ?? false)
                                ? 'border-red-600 bg-red-600 text-white shadow-sm'
                                : 'border-red-200 bg-white text-red-700 hover:border-red-400' }}"
                    >
                        Somente atrasados
                    </button>

                    <button
                        type="button"
                        wire:click="toggleQuickFilter('my_sectors_only')"
                        class="rounded-full border px-3.5 py-2 text-xs font-semibold transition
                            {{ (bool) ($filters['my_sectors_only'] ?? false)
                                ? 'border-sky-600 bg-sky-600 text-white shadow-sm'
                                : 'border-sky-200 bg-white text-sky-700 hover:border-sky-400' }}"
                    >
                        Somente meu setor
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-3">
        @forelse ($processes as $process)
            @php
                $hasUnseenUpdate = $processIdsWithUnseenUpdates->contains((int) $process->id);
                $isOverdueCurrentStep = $processIdsWithOverdueCurrentStep->contains((int) $process->id);
                $processStatus = $statusCollection->firstWhere('code', (string) $process->status);
                $statusCode = (string) data_get($processStatus, 'code', $process->status);
                $statusLabel = (string) data_get($processStatus, 'label', $process->status);

                $statusBadgeClass = match ($statusCode) {
                    \App\Models\Process\ProcessStatus::IN_PROGRESS => 'bg-blue-100 text-blue-700',
                    \App\Models\Process\ProcessStatus::CLOSED => 'bg-emerald-100 text-emerald-700',
                    \App\Models\Process\ProcessStatus::CANCELLED => 'bg-rose-100 text-rose-700',
                    default => 'bg-slate-100 text-slate-700',
                };

                $cardAccentClass = match ($statusCode) {
                    \App\Models\Process\ProcessStatus::IN_PROGRESS => 'border-blue-200 hover:border-blue-300',
                    \App\Models\Process\ProcessStatus::CLOSED => 'border-emerald-200 hover:border-emerald-300',
                    \App\Models\Process\ProcessStatus::CANCELLED => 'border-rose-200 hover:border-rose-300',
                    default => 'border-slate-200 hover:border-slate-300',
                };
            @endphp

            <a
                href="{{ route('process.show', $process->uuid) }}"
                class="group block overflow-hidden rounded-3xl border bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $cardAccentClass }}"
            >
                <div class="grid grid-cols-1 gap-0 xl:grid-cols-[190px_1fr_180px]">
                    <div class="flex flex-col items-center justify-center gap-4 border-b border-slate-200 bg-slate-50/80 px-5 py-4 xl:border-b-0 xl:border-r">                        
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] {{ $statusBadgeClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="flex-1 flex items-center justify-center">
                            <p class="font-mono text-sm text-slate-500 font-semibold">{{ $process->code ?? 'Sem codigo' }}</p>
                        </div>

                        <div class="text-center">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">Ultima atualizacao</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $process->updated_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="px-5 py-3">

                        <h3 class="mt-3 text-lg font-semibold tracking-tight text-slate-900 group-hover:text-emerald-800">
                            {{ $process->title }}
                        </h3>

                        <div class="mt-3 flex flex-wrap gap-5 text-xs text-slate-500">
                            <div>
                                <p class="font-semibold uppercase tracking-[0.14em] text-slate-400">Fluxo de Trabalho</p>
                                <p class="mt-1 text-sm text-slate-700">{{ $process->workflow?->title ?? 'Sem workflow' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold uppercase tracking-[0.14em] text-slate-400">Setor atual</p>
                                <p class="mt-1 text-sm text-slate-700">{{ $process->organization?->title ?? 'Nao definido' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold uppercase tracking-[0.14em] text-slate-400">Responsavel</p>
                                <p class="mt-1 text-sm text-slate-700">{{ $process->owner?->name ?? 'Nao atribuido' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-between border-t border-slate-200 bg-gradient-to-br from-slate-50 to-white px-5 py-4 xl:border-l xl:border-t-0">
                        <div class="space-y-2">
                            @if ($hasUnseenUpdate)
                                <p class="text-center rounded-full bg-amber-100 px-2.5 py-1 text-[9px] font-semibold uppercase tracking-[0.14em] text-amber-700">
                                    Nova atualizacao
                                </p>
                            @endif

                            
                            @if ($isOverdueCurrentStep)
                                <p class="text-center rounded-full bg-rose-100 px-2.5 py-1 text-[9px] font-semibold uppercase tracking-[0.14em] text-rose-700">
                                    Etapa atrasada
                                </p>
                            @endif
                        </div>

                        <div class="xl:hidden">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Abertura</p>
                            <p class="mt-1 text-sm text-slate-700">{{ $process->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>

                        <div class="hidden text-right xl:block">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Abertura</p>
                            <p class="mt-1 text-sm text-slate-700">{{ $process->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>

                        <i class="fa-solid fa-chevron-right text-xs text-slate-400 xl:hidden"></i>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center shadow-sm">
                <p class="text-sm font-semibold text-slate-700">Nenhum processo encontrado.</p>
                <p class="mt-2 text-sm text-slate-500">Ajuste os filtros ou abra um novo processo para iniciar a fila.</p>
            </div>
        @endforelse
    </section>

    <x-modal :show="$showModal" size="lg">
        @if ($modalKey === 'modal-process-create')
            <x-slot name="header">
                <h2 class="text-sm font-semibold uppercase text-gray-700">Novo Processo</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.process._partials.process-form')

                <div class="flex justify-end gap-2 pt-2">
                    <x-button text="Cancelar" variant="secondary" wire:click="closeModal" />
                    <x-button type="submit" text="Salvar" icon="fa-solid fa-save" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
