<div>
    <x-button
        wire:click="open"
        :text="$iconOnly ? null : 'Alterar estado'"
        :title="'Alterar estado'"
        icon="fa-solid fa-shuffle"
        :variant="'yellow_text'"
        :full-width="! $iconOnly"
    />

    <x-modal :show="$showModal" wire:key="change-state-modal">
        @if ($modalKey === 'change-state')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">{{ 'Alterar estado do ativo' }}</h2>
            </x-slot>

            <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-12">
                    <x-form.label :value="'Novo estado'" />
                    <x-form.select-livewire
                        wire:model="toState"
                        name="toState"
                        :default="'Selecione o estado'"
                        :options="$availableStates"
                    />
                    <x-form.error for="toState" />
                </div>

                <div class="md:col-span-12">
                    <x-form.label :value="'Observacoes'" />
                    <x-form.textarea wire:model="notes" rows="3" name="notes" />
                    <x-form.error for="notes" />
                </div>

                <div class="md:col-span-12 flex justify-end gap-2">
                    <x-button type="button" wire:click="closeModal" :text="'Cancelar'" variant="gray_outline" />
                    <x-button type="submit" :text="'Salvar estado'" variant="yellow_solid" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
