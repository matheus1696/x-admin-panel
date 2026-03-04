<div>
    <x-button
        wire:click="open"
        :text="__('assets.operations.release.action')"
        icon="fa-solid fa-arrow-up-right-from-square"
        :full-width="true"
    />

    <x-modal :show="$showModal" wire:key="release-asset-modal">
        @if ($modalKey === 'release-asset')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">{{ __('assets.operations.release.title') }}</h2>
            </x-slot>

            <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="md:col-span-6">
                    <x-form.label :value="__('assets.operations.fields.unit')" />
                    <x-form.select-livewire
                        wire:model="unitId"
                        name="unitId"
                        :default="__('assets.filters.all_units')"
                        :options="collect($units)->map(fn ($unit) => ['value' => $unit->id, 'label' => $unit->title])->values()->all()"
                    />
                    <x-form.error for="unitId" />
                </div>

                <div class="md:col-span-6">
                    <x-form.label :value="__('assets.operations.fields.sector')" />
                    <x-form.select-livewire
                        wire:model="sectorId"
                        name="sectorId"
                        :default="__('assets.operations.placeholders.optional_sector')"
                        :options="collect($sectors)->map(fn ($sector) => ['value' => $sector->id, 'label' => $sector->title])->values()->all()"
                    />
                    <x-form.error for="sectorId" />
                </div>

                <div class="md:col-span-12">
                    <x-form.label :value="__('assets.operations.fields.notes')" />
                    <x-form.textarea wire:model="notes" rows="3" name="notes" />
                    <x-form.error for="notes" />
                </div>

                <div class="md:col-span-12 flex justify-end gap-2">
                    <x-button type="button" wire:click="closeModal" :text="__('assets.actions.cancel')" variant="gray_outline" />
                    <x-button type="submit" :text="__('assets.operations.release.submit')" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
