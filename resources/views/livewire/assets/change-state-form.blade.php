<div>
    <x-button
        wire:click="open"
        :text="__('assets.operations.change_state.action')"
        icon="fa-solid fa-shuffle"
        variant="yellow_outline"
        :full-width="true"
    />

    <x-modal :show="$showModal" wire:key="change-state-modal">
        @if ($modalKey === 'change-state')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">{{ __('assets.operations.change_state.title') }}</h2>
            </x-slot>

            <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-12">
                    <x-form.label :value="__('assets.operations.change_state.fields.to_state')" />
                    <x-form.select-livewire
                        wire:model="toState"
                        name="toState"
                        :default="__('assets.operations.change_state.placeholders.select_state')"
                        :options="$availableStates"
                    />
                    <x-form.error for="toState" />
                </div>

                <div class="md:col-span-12">
                    <x-form.label :value="__('assets.operations.fields.notes')" />
                    <x-form.textarea wire:model="notes" rows="3" name="notes" />
                    <x-form.error for="notes" />
                </div>

                <div class="md:col-span-12 flex justify-end gap-2">
                    <x-button type="button" wire:click="closeModal" :text="__('assets.actions.cancel')" variant="gray_outline" />
                    <x-button type="submit" :text="__('assets.operations.change_state.submit')" variant="yellow_solid" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
