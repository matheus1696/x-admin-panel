<div>

    <x-page.header
        :title="'Execucao da Campanha de Auditoria'"
        :subtitle="$campaign->title"
        icon="fa-solid fa-list-check"
    >
        <x-slot name="button">
            <div class="flex items-center gap-2">
                <x-button :href="route('assets.audits.campaigns.index')" :text="'Historico'" icon="fa-solid fa-list" variant="gray_outline" />
                <button
                    type="button"
                    onclick="window.open('{{ route('assets.audits.campaigns.pdf', $campaign->uuid) }}', '_blank')"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-800"
                >
                    <i class="fa-solid fa-file-pdf"></i>
                    Relatorio PDF
                </button>
                @if ($campaign->status !== 'CONCLUDED')
                    <x-button type="button" wire:click="finalizeCampaign" :text="'Finalizar campanha'" icon="fa-solid fa-check-double" />
                @endif
            </div>
        </x-slot>
    </x-page.header>

    <div class="mb-4 grid grid-cols-2 gap-3 md:grid-cols-5">
        <x-page.card>
            <p class="text-xs uppercase text-gray-500">Total</p>
            <p class="text-xl font-semibold text-gray-900">{{ $metrics['total'] }}</p>
        </x-page.card>
        <x-page.card>
            <p class="text-xs uppercase text-gray-500">Concluidos</p>
            <p class="text-xl font-semibold text-emerald-700">{{ $metrics['done'] }}</p>
        </x-page.card>
        <x-page.card>
            <p class="text-xs uppercase text-gray-500">Pendentes</p>
            <p class="text-xl font-semibold text-amber-700">{{ $metrics['pending'] }}</p>
        </x-page.card>
        <x-page.card>
            <p class="text-xs uppercase text-gray-500">Pendencias</p>
            <p class="text-xl font-semibold text-red-700">{{ $metrics['openIssues'] }}</p>
        </x-page.card>
        <x-page.card>
            <p class="text-xs uppercase text-gray-500">Conformidade</p>
            <p class="text-xl font-semibold text-blue-700">{{ $metrics['conformity'] }}%</p>
        </x-page.card>
    </div>

    <x-page.filter :title="'Filtros de execucao'" :accordionOpen="true">
        <div class="md:col-span-6">
            <x-form.label :value="'Busca'" />
            <x-form.input type="text" wire:model.live.debounce.500ms="filters.search" :placeholder="'Codigo, descricao ou patrimonio'" />
        </div>
        <div class="md:col-span-3">
            <x-form.label :value="'Status'" />
            <x-form.select-livewire
                wire:model.live="filters.status"
                name="filters.status"
                :options="[
                    ['value' => 'all', 'label' => 'Todos'],
                    ['value' => 'PENDING', 'label' => 'Pendente'],
                    ['value' => 'FOUND', 'label' => 'Encontrado'],
                    ['value' => 'NOT_FOUND', 'label' => 'Nao encontrado'],
                    ['value' => 'DIVERGENCE', 'label' => 'Divergencia'],
                    ['value' => 'DAMAGED', 'label' => 'Danificado'],
                    ['value' => 'NO_TAG', 'label' => 'Sem etiqueta'],
                ]"
            />
        </div>
        <div class="md:col-span-3">
            <x-form.label :value="'Itens por pagina'" />
            <x-form.select-livewire
                wire:model.live="filters.perPage"
                name="filters.perPage"
                :options="[
                    ['value' => 15, 'label' => '15'],
                    ['value' => 30, 'label' => '30'],
                    ['value' => 50, 'label' => '50'],
                ]"
            />
        </div>
    </x-page.filter>

    <x-page.table :pagination="$items" :empty-message="'Nenhum item de auditoria encontrado.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="'Ativo'" />
                <x-page.table-th class="w-32" :value="'Patrimonio'" />
                <x-page.table-th class="w-36" :value="'Local esperado'" />
                <x-page.table-th class="w-28 text-center" :value="'Status'" />
                <x-page.table-th class="w-20 text-center" :value="'Acoes'" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($items as $item)
                <tr>
                    <x-page.table-td>
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-900">{{ $item->asset->code }} - {{ $item->asset->description }}</span>
                            <span class="text-xs text-gray-500">{{ $item->asset->unit?->title ?: '-' }} / {{ $item->asset->sector?->title ?: 'Sem setor' }}</span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td :value="$item->asset->patrimony_number ?: '-'" />
                    <x-page.table-td :value="($item->asset->unit?->title ?: '-') . ' / ' . ($item->asset->sector?->title ?: 'Sem setor')" />
                    <x-page.table-td class="text-center" :value="$item->status" />
                    <x-page.table-td>
                        <div class="flex items-center justify-center">
                            <x-button type="button" wire:click="openAuditItem({{ $item->id }})" icon="fa-solid fa-camera-retro" :title="'Auditar item'" variant="green_text" />
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>

    <x-page.card>
        <p class="mb-2 text-sm font-semibold uppercase tracking-wide text-gray-700">Pendencias abertas</p>
        @if ($openIssuesList->isEmpty())
            <p class="text-sm text-gray-500">Nenhuma pendencia aberta.</p>
        @else
            <div class="space-y-2">
                @foreach ($openIssuesList as $issue)
                    <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-900">
                        <span class="font-semibold">{{ $issue->issue_type }}</span>
                        <span> - {{ $issue->asset?->code ?: '-' }}</span>
                        @if ($issue->notes)
                            <p class="mt-1 text-xs text-red-700">{{ $issue->notes }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-page.card>

    <x-modal :show="$showModal" wire:key="audit-campaign-item-modal">
        @if ($modalKey === 'audit-item')
            <x-slot name="header">
                <h2 class="text-sm font-semibold uppercase text-gray-700">Registrar auditoria do item</h2>
            </x-slot>

            <form wire:submit.prevent="saveAuditItem" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-4">
                    <x-form.label :value="'Resultado'" />
                    <x-form.select-livewire
                        wire:model.live="auditStatus"
                        name="auditStatus"
                        :options="[
                            ['value' => 'FOUND', 'label' => 'Encontrado'],
                            ['value' => 'NOT_FOUND', 'label' => 'Nao encontrado'],
                            ['value' => 'DIVERGENCE', 'label' => 'Divergencia'],
                            ['value' => 'DAMAGED', 'label' => 'Danificado'],
                            ['value' => 'NO_TAG', 'label' => 'Sem etiqueta'],
                        ]"
                    />
                    <x-form.error for="auditStatus" />
                </div>
                <div class="md:col-span-4">
                    <x-form.label :value="'Unidade observada (opcional)'" />
                    <x-form.input type="text" wire:model="observedUnit" />
                    <x-form.error for="observedUnit" />
                </div>
                <div class="md:col-span-4">
                    <x-form.label :value="'Setor observado (opcional)'" />
                    <x-form.input type="text" wire:model="observedSector" />
                    <x-form.error for="observedSector" />
                </div>
                <div class="md:col-span-12">
                    <x-form.label :value="'Foto (opcional)'" />
                    <x-form.input type="file" wire:model="photo" accept="image/*" />
                    <x-form.error for="photo" />
                </div>
                <div class="md:col-span-12">
                    <x-form.label :value="'Observacoes'" />
                    <x-form.textarea wire:model="auditNotes" rows="3" />
                    <x-form.error for="auditNotes" />
                </div>
                <div class="md:col-span-12 flex justify-end gap-2">
                    <x-button type="button" wire:click="closeModal" :text="'Cancelar'" variant="gray_outline" />
                    <x-button type="submit" :text="'Salvar auditoria'" icon="fa-solid fa-floppy-disk" />
                </div>
            </form>
        @endif
    </x-modal>
</div>

