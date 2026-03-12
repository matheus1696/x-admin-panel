<div>

    <x-page.header title="Gestao de Processos" subtitle="Acompanhe o ciclo de vida e a rastreabilidade dos processos" icon="fa-solid fa-folder-tree">
        <x-slot name="button">
            <div class="flex items-center gap-2">
                @can('process.create')
                    <x-button text="Novo Processo" icon="fa-solid fa-plus" wire:click="create" />
                @endcan
            </div>
        </x-slot>
    </x-page.header>

    <x-page.filter title="Filtros">
        <div class="col-span-12 md:col-span-4">
            <x-form.label value="Titulo" />
            <x-form.input wire:model.live.debounce.400ms="filters.title" placeholder="Buscar por titulo..." />
        </div>

        <div class="col-span-6 md:col-span-3">
            <x-form.label value="Setor" />
            <x-form.select-livewire
                wire:model.live="filters.organization_id"
                name="filters.organization_id"
                :options="$organizations->map(fn($organization) => ['value' => $organization->id, 'label' => $organization->title])->prepend(['value' => '', 'label' => 'Todos'])->values()->all()"
            />
        </div>

        <div class="col-span-6 md:col-span-2">
            <x-form.label value="Itens por pagina" />
            <x-form.select-livewire
                wire:model.live="filters.perPage"
                name="filters.perPage"
                :options="[
                    ['value' => 10, 'label' => '10'],
                    ['value' => 25, 'label' => '25'],
                    ['value' => 50, 'label' => '50'],
                ]"
            />
        </div>
    </x-page.filter>

    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <div>
            @foreach ($statuses as $status)
                <button
                    type="button"
                    wire:click="$set('filters.status', '{{ $status['code'] }}')"
                    class="rounded-full border px-3 py-1.5 text-xs font-semibold transition
                        {{ ($filters['status'] ?? \App\Models\Process\ProcessStatus::IN_PROGRESS) === $status['code']
                            ? 'border-emerald-600 bg-emerald-600 text-white'
                            : 'border-gray-300 bg-white text-gray-600 hover:border-emerald-400 hover:text-emerald-700' }}"
                >
                    {{ $status['label'] }}
                </button>
            @endforeach
        </div>

        <div>
            <button
            type="button"
            wire:click="toggleQuickFilter('overdue_only')"
            class="rounded-full border px-3 py-1.5 text-xs font-semibold transition
                {{ (bool) ($filters['overdue_only'] ?? false)
                    ? 'border-red-600 bg-red-600 text-white'
                    : 'border-red-200 bg-white text-red-700 hover:border-red-400' }}"
            >
                Somente atrasados
            </button>

            <button
                type="button"
                wire:click="toggleQuickFilter('my_sectors_only')"
                class="rounded-full border px-3 py-1.5 text-xs font-semibold transition
                    {{ (bool) ($filters['my_sectors_only'] ?? false)
                        ? 'border-sky-600 bg-sky-600 text-white'
                        : 'border-sky-200 bg-white text-sky-700 hover:border-sky-400' }}"
            >
                Somente meu setor
            </button>
        </div>
    </div>

    <div class="space-y-1">
        @forelse ($processes as $process)
            @php
                $hasUnseenUpdate = $processIdsWithUnseenUpdates->contains((int) $process->id);
                $isOverdueCurrentStep = $processIdsWithOverdueCurrentStep->contains((int) $process->id);
                $processStatus = collect($statuses)->firstWhere('code', (string) $process->status);
                $statusCardClass = match ((string) data_get($processStatus, 'code', $process->status)) {
                    \App\Models\Process\ProcessStatus::IN_PROGRESS => 'border-blue-200 bg-blue-50/70 hover:border-blue-300',
                    \App\Models\Process\ProcessStatus::CLOSED => 'border-emerald-200 bg-emerald-50/70 hover:border-emerald-300',
                    \App\Models\Process\ProcessStatus::CANCELLED => 'border-red-200 bg-red-50/70 hover:border-red-300',
                    default => 'border-gray-300 bg-white hover:border-emerald-400',
                };
            @endphp

            <a
                href="{{ route('process.show', $process->uuid) }}"
                class="block rounded-xl border p-4 shadow-sm transition hover:shadow-md {{ $statusCardClass }} {{ $hasUnseenUpdate ? 'bg-gray-300/70' : '' }}"
            >
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 flex items-center gap-4 min-w-0">
                        <div class="flex flex-col items-center gap-2 text-xs text-gray-700 border-r border-gray-300/70 pr-4">
                            <span>{{ $process->updated_at?->format('d/m/Y H:i') ?? '-' }}</span>
                            <span class="font-mono">{{ $process->code ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <p class="truncate text-sm font-semibold text-gray-900">{{ $process->title }}</p>
                            <p class="text-xs text-gray-500"> {{ $process->workflow?->title ?? 'Sem workflow' }} | {{ $process->organization?->title ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        @if ($isOverdueCurrentStep)
                            <span class="rounded-full bg-red-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-red-700">
                                Etapa atrasada
                            </span>
                        @endif
                    </div>
                    <i class="fa-solid fa-chevron-right mt-1 text-xs text-gray-400"></i>
                </div>
            </a>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-white px-4 py-8 text-center text-sm text-gray-500">
                Nenhum processo encontrado.
            </div>
        @endforelse
    </div>

    @if ($processes->total() > 0)
        <div class="mt-4 flex flex-col items-center justify-between gap-2 px-1 sm:flex-row">
            <div class="text-xs text-gray-500">
                Mostrando {{ $processes->firstItem() ?? 0 }} até {{ $processes->lastItem() ?? 0 }} de {{ $processes->total() }} registros
            </div>
            <div>
                {{ $processes->links('components.pagination') }}
            </div>
        </div>
    @endif

    <x-modal :show="$showModal" maxWidth="max-w-4xl">
        @if ($modalKey === 'modal-process-create')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Novo Processo</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.process._partials.process-form')

                <div class="flex justify-end gap-2 pt-2">
                    <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" />
                    <x-button type="submit" text="Salvar" icon="fa-solid fa-save" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
