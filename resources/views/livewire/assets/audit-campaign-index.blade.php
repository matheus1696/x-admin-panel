<div>
    <x-alert.flash />

    <x-page.header
        :title="'Campanhas de Auditoria'"
        :subtitle="'Planeje, execute e acompanhe auditorias de ativos'"
        icon="fa-solid fa-clipboard-check"
    >
        <x-slot name="button">
            <x-button :href="route('assets.audits.campaigns.create')" :text="'Nova campanha'" icon="fa-solid fa-plus" />
        </x-slot>
    </x-page.header>

    <x-page.table :empty-message="'Nenhuma campanha de auditoria cadastrada.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="'Campanha'" />
                <x-page.table-th class="w-24 text-center" :value="'Status'" />
                <x-page.table-th :value="'Escopo'" />
                <x-page.table-th class="w-24 text-center" :value="'Pendentes'" />
                <x-page.table-th class="w-24 text-center" :value="'Concluidos'" />
                <x-page.table-th class="w-24 text-center" :value="'Pendencias'" />
                <x-page.table-th class="w-20 text-center" :value="'Acoes'" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($campaigns as $campaign)
                <tr>
                    <x-page.table-td>
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-900">{{ $campaign->title }}</span>
                            <span class="text-xs text-gray-500">
                                {{ optional($campaign->start_date)->format('d/m/Y') ?: '-' }} ate {{ optional($campaign->due_date)->format('d/m/Y') ?: '-' }}
                            </span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="text-center" :value="$campaign->status === 'CONCLUDED' ? 'Concluida' : ($campaign->status === 'IN_PROGRESS' ? 'Em andamento' : 'Planejada')" />
                    <x-page.table-td>
                        <div class="flex flex-col">
                            <span>{{ $campaign->unit?->title ?: 'Todas as unidades' }}</span>
                            <span class="text-xs text-gray-500">{{ $campaign->sector?->title ?: 'Todos os setores' }}</span>
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="text-center" :value="$campaign->pending_count" />
                    <x-page.table-td class="text-center" :value="$campaign->done_count" />
                    <x-page.table-td class="text-center" :value="$campaign->open_issues_count" />
                    <x-page.table-td>
                        <div class="flex items-center justify-center">
                            <x-button
                                :href="route('assets.audits.campaigns.show', $campaign->uuid)"
                                icon="fa-solid fa-eye"
                                :title="'Abrir campanha'"
                                variant="blue_text"
                            />
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>

