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

    <div class="md:col-span-2 pt-2 border-t border-gray-200">

        @php
            $photoUrl =
                $responsible_photo instanceof Livewire\Features\SupportFileUploads\TemporaryUploadedFile
                    ? $responsible_photo->temporaryUrl()
                    : ($current_responsible_photo
                        ? asset('storage/' . $current_responsible_photo)
                        : asset('https://tse4.mm.bing.net/th/id/OIP.dDKYQqVBsG1tIt2uJzEJHwHaHa?rs=1&pid=ImgDetMain&o=7&rm=3'));
        @endphp

        <div class="flex items-center justify-center">
            <img src="{{ $photoUrl }}" alt="{{ $responsible_name ?? 'Responsável' }}" class="w-20 h-20 rounded-full mt-2 border border-slate-300 object-cover">
        </div>

        <div>
            <x-form.label value="Foto do responsável" />
            <x-form.input type="file" wire:model="responsible_photo" accept=".jpg,.jpeg,.png"/>
            <x-form.error for="responsible_photo" />
        </div>
    </div>

    <div>
        <x-form.label value="Nome do responsável" />
        <x-form.input wire:model.defer="responsible_name" placeholder="Nome do responsável" />
        <x-form.error for="responsible_name" />
    </div>

    <div>
        <x-form.label value="Contato" />
        <x-form.input wire:model.defer="responsible_contact" placeholder="Telefone ou ramal" />
        <x-form.error for="responsible_contact" />
    </div>

    <div class="col-span-2">
        <x-form.label value="Email" />
        <x-form.input wire:model.defer="responsible_email" placeholder="Email do responsável" />
        <x-form.error for="responsible_email" />
    </div>
</div>