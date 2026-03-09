<div>
    <x-alert.flash />

    <x-page.header
        :title="'Nova Campanha de Auditoria'"
        :subtitle="'Defina o escopo e inicie a execucao da auditoria de ativos'"
        icon="fa-solid fa-clipboard-check"
    />

    <x-page.card>
        <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 md:grid-cols-12">
            <div class="md:col-span-12">
                <x-form.label :value="'Titulo da campanha'" />
                <x-form.input type="text" wire:model="title" :placeholder="'Ex.: Auditoria mensal - Unidade Centro'" />
                <x-form.error for="title" />
            </div>

            <div class="md:col-span-4">
                <x-form.label :value="'Unidade (opcional)'" />
                <x-form.select-livewire
                    wire:model.live="unitId"
                    name="unitId"
                    :default="'Todas as unidades'"
                    :options="collect($units)->map(fn ($unit) => ['value' => $unit->id, 'label' => $unit->title])->values()->all()"
                />
                <x-form.error for="unitId" />
            </div>

            <div class="md:col-span-4">
                <x-form.label :value="'Setor (opcional)'" />
                <div wire:key="audit-campaign-create-sector-{{ $unitId ?: 'none' }}">
                    <x-form.select-livewire
                        wire:model.live="sectorId"
                        name="sectorId"
                        :default="$unitId ? 'Todos os setores' : 'Selecione primeiro a unidade'"
                        :disabled="!$unitId"
                        :options="collect($sectors)->map(fn ($sector) => ['value' => $sector->id, 'label' => $sector->title])->values()->all()"
                    />
                </div>
                <x-form.error for="sectorId" />
            </div>

            <div class="md:col-span-4">
                <x-form.label :value="'Bloco financeiro (opcional)'" />
                <x-form.select-livewire
                    wire:model.live="financialBlockId"
                    name="financialBlockId"
                    :default="'Todos os blocos'"
                    :options="collect($financialBlocks)->map(fn ($block) => ['value' => $block->id, 'label' => ($block->acronym ?: $block->title)])->values()->all()"
                />
                <x-form.error for="financialBlockId" />
            </div>

            <div class="md:col-span-6">
                <x-form.label :value="'Data de inicio'" />
                <x-form.input type="date" wire:model="startDate" />
                <x-form.error for="startDate" />
            </div>

            <div class="md:col-span-6">
                <x-form.label :value="'Prazo da campanha'" />
                <x-form.input type="date" wire:model="dueDate" />
                <x-form.error for="dueDate" />
            </div>

            <div class="md:col-span-12 flex justify-end gap-2">
                <x-button :href="route('assets.audits.campaigns.index')" :text="'Cancelar'" variant="gray_outline" />
                <x-button type="submit" :text="'Criar campanha'" icon="fa-solid fa-floppy-disk" />
            </div>
        </form>
    </x-page.card>
</div>

