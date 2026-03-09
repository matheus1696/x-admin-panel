<div class="mx-auto max-w-2xl">
    <x-alert.flash />

    <x-page.header
        :title="'Auditoria individual'"
        :subtitle="'Busque o ativo, envie a foto e registre a auditoria'"
        icon="fa-solid fa-camera"
        color="blue"
    >
        <x-slot name="button">
            <x-button
                :href="route('assets.index')"
                :text="'Voltar'"
                icon="fa-solid fa-arrow-left"
                variant="gray_outline"
            />
        </x-slot>
    </x-page.header>

    <div class="space-y-4">
        <div class="rounded-3xl border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
            <form wire:submit.prevent="searchAsset" class="space-y-4">
                <div>
                    <x-form.label :value="'Codigo do ativo'" />
                    <x-form.input
                        type="text"
                        wire:model="searchCode"
                        :placeholder="'Digite ou escaneie o codigo do ativo'"
                    />
                    <x-form.error for="searchCode" />
                </div>

                <div class="flex justify-end">
                    <x-button type="submit" :text="'Buscar ativo'" icon="fa-solid fa-magnifying-glass" />
                </div>
            </form>
        </div>

        @if ($asset)
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-4 rounded-2xl bg-gray-50 p-4">
                    <p class="text-sm font-semibold text-gray-800">{{ $asset->code }}</p>
                    <p class="mt-1 text-xs text-gray-600">{{ $asset->description }}</p>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ $asset->unit?->title ?? '-' }} / {{ $asset->sector?->title ?? '-' }}
                    </p>
                </div>

                <form wire:submit.prevent="audit" class="space-y-4">
                    <div>
                        <x-form.label :value="'Foto da auditoria'" />
                        <x-form.input type="file" wire:model="photo" accept="image/*" name="photo" />
                        <x-form.error for="photo" />
                    </div>

                    <div>
                        <x-form.label :value="'Observacoes'" />
                        <x-form.textarea wire:model="notes" rows="4" name="notes" />
                        <x-form.error for="notes" />
                    </div>

                    <div class="flex justify-end">
                        <x-button type="submit" :text="'Registrar auditoria'" icon="fa-solid fa-camera-retro" />
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
