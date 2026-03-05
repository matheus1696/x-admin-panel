<div>
    <x-button
        wire:click="open"
        :text="$iconOnly ? null : 'Liberar ativo'"
        :title="'Liberar ativo'"
        icon="fa-solid fa-arrow-up-right-from-square"
        :variant="$iconOnly ? 'blue_text' : 'green_solid'"
        :full-width="! $iconOnly"
    />

    <x-modal :show="$showModal" wire:key="release-asset-modal">
        @if ($modalKey === 'release-asset')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">{{ 'Liberar ativo' }}</h2>
            </x-slot>

            <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-6">
                    <x-form.label :value="'Numero patrimonial'" />
                    <x-form.input type="text" wire:model.defer="patrimonyNumber" placeholder="Ex: PAT-000123" />
                    <x-form.error for="patrimonyNumber" />
                </div>

                <div class="md:col-span-6">
                    <x-form.label :value="'Unidade de destino'" />
                    <x-form.select-livewire
                        wire:model="unitId"
                        name="unitId"
                        :default="'Todas as unidades'"
                        :options="collect($units)->map(fn ($unit) => ['value' => $unit->id, 'label' => $unit->title])->values()->all()"
                    />
                    <x-form.error for="unitId" />
                </div>

                <div class="md:col-span-12">
                    <x-form.label :value="'Setor de destino'" />
                    <x-form.select-livewire
                        wire:model="sectorId"
                        name="sectorId"
                        :default="'Setor opcional'"
                        :options="collect($sectors)->map(fn ($sector) => ['value' => $sector->id, 'label' => $sector->title])->values()->all()"
                    />
                    <x-form.error for="sectorId" />
                </div>

                <div class="md:col-span-12">
                    <x-form.label :value="'Observacoes'" />
                    <x-form.textarea wire:model="notes" rows="3" name="notes" />
                    <x-form.error for="notes" />
                </div>

                <div class="md:col-span-12 flex justify-end gap-2">
                    <x-button type="button" wire:click="closeModal" :text="'Cancelar'" variant="gray_outline" />
                    <x-button type="submit" :text="'Confirmar liberacao'" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
