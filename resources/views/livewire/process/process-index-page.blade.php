<div>

    <x-page.header title="Gestao de Processos" subtitle="Acompanhe o ciclo de vida e a rastreabilidade dos processos" icon="fa-solid fa-folder-tree">
        <x-slot name="button">
            <div class="flex items-center gap-2">
                @can('process.dashboard.view')
                    <x-button href="{{ route('process.dashboard') }}" text="Dashboard" icon="fa-solid fa-chart-column" variant="gray_outline" />
                @endcan
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
            <x-form.label value="Status" />
            <x-form.select-livewire
                wire:model.live="filters.status"
                name="filters.status"
                :options="collect($statuses)
                    ->map(fn($status) => ['value' => $status->value, 'label' => $status->label()])
                    ->prepend(['value' => 'all', 'label' => 'Todos'])
                    ->values()
                    ->all()"
            />
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

    <x-page.table :pagination="$processes">
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="w-24 text-center" value="Codigo" />
                <x-page.table-th value="Titulo" />
                <x-page.table-th class="hidden lg:table-cell" value="Setor" />
                <x-page.table-th class="w-32 text-center" value="Status" />
                <x-page.table-th class="w-40 text-center" value="Acoes" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($processes as $process)
                <tr>
                    <x-page.table-td class="text-center font-mono text-xs" :value="$process->code ?? '-'" />
                    <x-page.table-td class="whitespace-normal">
                        <div class="font-medium text-gray-900">{{ $process->title }}</div>
                        <div class="text-xs text-gray-500">{{ $process->workflow?->title ?? 'Sem workflow' }}</div>
                    </x-page.table-td>
                    <x-page.table-td class="hidden lg:table-cell" :value="$process->organization?->title ?? '-'" />
                    <x-page.table-td class="text-center">
                        @php
                            $processStatus = \App\Enums\Process\ProcessStatus::tryFrom((string) $process->status);
                            $processStatusClass = $processStatus?->badgeClass() ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="rounded-full px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.15em] {{ $processStatusClass }}">
                            {{ $processStatus?->label() ?? $process->status }}
                        </span>
                    </x-page.table-td>
                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            <x-button wire:click="openProcess('{{ $process->uuid }}')" icon="fa-solid fa-eye" title="Visualizar" variant="green_text" />
                        </div>
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <x-page.table-td colspan="5" class="text-center py-8 text-sm text-gray-500">
                        Nenhum processo encontrado.
                    </x-page.table-td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

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
