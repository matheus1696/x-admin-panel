<div>
    <x-alert.flash />

    <x-page.header title="Locais de Registro" subtitle="Configure os pontos de referencia do controle de ponto" icon="fa-solid fa-location-dot">
        <x-slot name="button">
            <x-button text="Novo Local" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>

    <x-page.table>
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Nome" />
                <x-page.table-th value="Latitude / Longitude" />
                <x-page.table-th class="w-28 text-center" value="Raio" />
                <x-page.table-th class="w-24 text-center" value="Status" />
                <x-page.table-th class="w-24 text-center" value="Acoes" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($locations as $location)
                <tr>
                    <x-page.table-td :value="$location->name" />
                    <x-page.table-td :value="$location->latitude.', '.$location->longitude" />
                    <x-page.table-td class="text-center" :value="$location->radius_meters.' m'" />
                    <x-page.table-td class="text-center">
                        <x-page.table-status :condition="$location->active" />
                    </x-page.table-td>
                    <x-page.table-td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            <x-button wire:click="edit({{ $location->id }})" icon="fa-solid fa-pen" variant="green_text" />
                        </div>
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <x-page.table-td colspan="5" class="text-center text-sm text-gray-500 py-10">
                        Nenhum local cadastrado.
                    </x-page.table-td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

    <x-modal :show="$showModal" wire:key="time-clock-location-modal">
        @if ($modalKey === 'modal-form-create-time-clock-location')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Local de Registro</h2>
            </x-slot>

            <form wire:submit.prevent="save" class="space-y-4">
                @include('livewire.time-clock._partials.location-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-form-edit-time-clock-location')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Local de Registro</h2>
            </x-slot>

            <form wire:submit.prevent="save" class="space-y-4">
                @include('livewire.time-clock._partials.location-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
