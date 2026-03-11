<div>

    <x-page.header
        :title="'Detalhamento do Item: '.$item"
        :subtitle="'Listagem dos ativos liberados deste item'"
        icon="fa-solid fa-layer-group"
    >
        <x-slot name="button">
            <x-button
                :href="route('assets.index')"
                :text="'Voltar para lista'"
                icon="fa-solid fa-arrow-left"
                variant="gray_outline"
            />
        </x-slot>
    </x-page.header>

    <x-page.table :pagination="$assets" :empty-message="'Nenhum ativo encontrado para este item.'">
        <x-slot name="thead">
            <tr>
                <x-page.table-th :value="'Codigo'" />
                <x-page.table-th class="hidden md:table-cell" :value="'Estado'" />
                <x-page.table-th class="hidden md:table-cell" :value="'Localizacao'" />
                <x-page.table-th class="w-24 text-center" :value="'Acoes'" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($assets as $asset)
                <tr>
                    <x-page.table-td :value="$asset->code" />
                    <x-page.table-td class="hidden md:table-cell" :value="$asset->state->value" />
                    <x-page.table-td class="hidden md:table-cell" :value="$asset->unit?->title ?? 'Sem unidade'" />
                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            <x-button
                                :href="route('assets.show', $asset->uuid)"
                                icon="fa-solid fa-eye"
                                :title="'Visualizar'"
                                variant="blue_text"
                            />
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
