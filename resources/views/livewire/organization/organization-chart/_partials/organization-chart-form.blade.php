<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Sigla --}}
    <div>
        <x-form.label value="Sigla" />
        <x-form.input wire:model.defer="acronym" placeholder="Sigla" required />
        <x-form.error :messages="$errors->get('acronym')" />
    </div>

    {{-- Setor --}}
    <div>
        <x-form.label value="Setor" />
        <x-form.input wire:model.defer="title" placeholder="Nome do Setor" required />
        <x-form.error :messages="$errors->get('title')" />
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
        <x-form.error :messages="$errors->get('hierarchy')" />
    </div>

    <div class="md:col-span-2 pt-2 border-t border-gray-200">

        <div class="flex items-center justify-center">
            @if($responsible_photo)
                @if ($this->responsible_photo instanceof Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                    <img src="{{ $responsible_photo->temporaryUrl() }}" alt="Foto Temporária" class="w-20 h-20 rounded-full mt-2 border border-slate-300">
                @else
                    <img src="{{ asset('storage/' . $responsible_photo) }}" alt="{{ $responsible_name ?? 'Foto do Responsável'}}" class="w-20 h-20 rounded-full mt-2 border border-slate-300">
                @endif
                
            @else
                <img src="{{ asset('https://tse4.mm.bing.net/th/id/OIP.dDKYQqVBsG1tIt2uJzEJHwHaHa?rs=1&pid=ImgDetMain&o=7&rm=3') }}" class="w-20 h-20 rounded-full mt-2 border border-slate-300">
            @endif
        </div>

        <div>
            <x-form.label value="Foto do responsável" />
            <x-form.input type="file" wire:model="responsible_photo" accept=".jpg,.jpeg,.png" />
            <x-form.error :messages="$errors->get('responsible_photo')" />
        </div>
    </div>

    <div>
        <x-form.label value="Nome do responsável" />
        <x-form.input wire:model.defer="responsible_name" placeholder="Nome do responsável" />
        <x-form.error :messages="$errors->get('responsible_name')" />
    </div>

    <div>
        <x-form.label value="Contato" />
        <x-form.input wire:model.defer="responsible_contact" placeholder="Telefone ou ramal" />
        <x-form.error :messages="$errors->get('responsible_contact')" />
    </div>

    <div class="col-span-2">
        <x-form.label value="Email" />
        <x-form.input wire:model.defer="responsible_email" placeholder="Email do responsável" />
        <x-form.error :messages="$errors->get('responsible_email')" />
    </div>
</div>