<div>
    <x-button
        wire:click="open"
        :text="__('assets.operations.return.action')"
        icon="fa-solid fa-rotate-left"
        variant="red_outline"
        :full-width="true"
    />

    <x-modal :show="$showModal" wire:key="return-to-patrimony-modal">
        @if ($modalKey === 'return-to-patrimony')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">{{ __('assets.operations.return.title') }}</h2>
            </x-slot>

            <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4">
                <div>
                    <p class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ __('assets.operations.return.description') }}
                    </p>
                </div>

                <div>
                    <x-form.label :value="__('assets.operations.fields.notes')" />
                    <x-form.textarea wire:model="notes" rows="3" name="notes" />
                    <x-form.error for="notes" />
                </div>

                <div class="flex justify-end gap-2">
                    <x-button type="button" wire:click="closeModal" :text="__('assets.actions.cancel')" variant="gray_outline" />
                    <x-button type="submit" :text="__('assets.operations.return.submit')" variant="red_solid" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
