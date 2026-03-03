<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Sigla --}}
    <div>
        <x-form.label value="Sigla" />
        <x-form.input wire:model.defer="acronym" placeholder="Sigla" required />
        <x-form.error for="acronym" />
    </div>

    {{-- Setor --}}
    <div>
        <x-form.label value="Setor" />
        <x-form.input wire:model.defer="title" placeholder="Nome do Setor" required />
        <x-form.error for="title" />
    </div>

    {{-- Setor Pai --}}
    <div class="md:col-span-2">
        <x-form.label value="Setor Pai" />
        <x-form.select-livewire 
            wire:model.defer="hierarchy" 
            name="hierarchy" 
            :collection="$organizationCharts" 
            value-field="id" 
            label-acronym="acronym" 
            label-field="title" 
        />
        <x-form.error for="hierarchy" />
    </div>

    <div class="md:col-span-2">
        <x-form.label value="Responsável do setor" />

        @if ($responsibleUsers->isEmpty())
            <div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                Associe usuários ao setor para selecionar o responsável.
            </div>
        @else
            <x-form.select-livewire
                wire:model.defer="responsible_user_id"
                name="responsible_user_id"
                :collection="$responsibleUsers"
                value-field="id"
                label-field="name"
            />
        @endif

        <x-form.error for="responsible_user_id" />
    </div>
</div>
