<div>
    <x-button
        wire:click="open"
        :text="'Retornar ao patrimonio'"
        icon="fa-solid fa-rotate-left"
        variant="red_outline"
        :full-width="true"
    />

    <x-modal :show="$showModal" wire:key="return-to-patrimony-modal">
        @if ($modalKey === 'return-to-patrimony')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">{{ 'Retornar ao patrimonio' }}</h2>
            </x-slot>

            <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4">
                <div>
                    <p class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ 'Essa acao remove o setor atual e envia o ativo para a unidade patrimonial configurada.' }}
                    </p>
                </div>

                <div>
                    <x-form.label :value="'Observacoes'" />
                    <x-form.textarea wire:model="notes" rows="3" name="notes" />
                    <x-form.error for="notes" />
                </div>

                <div class="flex justify-end gap-2">
                    <x-button type="button" wire:click="closeModal" :text="'Cancelar'" variant="gray_outline" />
                    <x-button type="submit" :text="'Confirmar retorno'" variant="red_solid" />
                </div>
            </form>
        @endif
    </x-modal>
</div>
