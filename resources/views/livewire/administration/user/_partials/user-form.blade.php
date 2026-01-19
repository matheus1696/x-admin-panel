<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    
    <!-- Matriculation -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="Matricula" for="matriculation" />
        <x-form.input type="text" wire:model.defer="matriculation" id="matriculation" value="{{ old('matriculation', $matriculation ?? '') }}" placeholder="00.000-00" maxlength="9" data-mask="matriculation"/>
        <x-form.error :messages="$errors->get('matriculation')" />
    </div>

    <!-- CPF -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="CPF" for="cpf" />
        <x-form.input type="text" wire:model.live="cpf" id="cpf" value="{{ old('cpf', $cpf ?? '') }}" data-mask="cpf" maxlength="14" placeholder="000.000.000-00"/>
        <x-form.error :messages="$errors->get('cpf')" />
    </div>

    <!-- Nome -->
    <div class="col-span-2 md:col-span-4">
        <x-form.label value="Nome" for="name" />
        <x-form.input type="text" wire:model.defer="name" id="name" value="{{ old('name', $name ?? '') }}" placeholder="Nome completo do usuário" required />
        <x-form.error :messages="$errors->get('name')" />
    </div>

    <!-- Email -->
    @isset ($userId)
        <div class="col-span-2 md:col-span-4">
            <x-form.label value="E-mail" for="email" />
            <x-form.input type="email" id="email" value="{{ $email }}" disabled/>
        </div>
    @else
        <div class="col-span-2 md:col-span-4">
            <x-form.label value="E-mail" for="email" />
            <x-form.input type="email" wire:model.defer="email" id="email" value="{{ old('email', $email ?? '') }}" placeholder="Digite o e-mail do usuário" required />
            <x-form.error :messages="$errors->get('email')" />
        </div>
    @endisset

    <!-- Ocupação -->
    <div class="col-span-2 md:col-span-4">
        <x-form.label value="Ocupação" for="occupation_id" />
        <x-form.select-livewire wire:model.defer="occupation_id" name="occupation_id" :collection="$occupations" labelField="title" valueField="id" default="Selecione a ocupação" :selected="$occupation_id"/>
        <x-form.error :messages="$errors->get('occupation_id')" />
    </div>

    <!-- Data de Nascimento -->
    <div class="md:col-span-2">
        <x-form.label value="Data de Nascimento" for="birth_date" />
        <x-form.input type="date" wire:model.defer="birth_date" id="birth_date" value="{{ old('birth_date', isset($birth_date) ? \Carbon\Carbon::parse($birth_date)->format('Y-m-d') : '') }}" min="1950-01-01" max="{{ now()->format('Y-m-d') }}"/>
        <x-form.error :messages="$errors->get('birth_date')" />
    </div>
    
    <!-- Gênero -->
    <div class="md:col-span-2">
        <x-form.label value="Gênero" for="gender_id" />
        <x-form.select-livewire wire:model.defer="gender_id" name="gender_id" :collection="$genders" labelField="title" valueField="id" default="Selecione o gênero" :selected="$gender_id"/>
        <x-form.error :messages="$errors->get('gender_id')" />
    </div>

    <!-- Telefone Pessoal -->
    <div class="md:col-span-2">
        <x-form.label value="Telefone Pessoal" for="phone_personal" />
        <x-form.input type="text" wire:model.defer="phone_personal" id="phone_personal" value="{{ old('phone_personal', $phone_personal ?? '') }}" placeholder="(00) 00000-0000" data-mask="phone" maxlength="15"/>
        <x-form.error :messages="$errors->get('phone_personal')" />
    </div>

    <!-- Telefone Profissional -->
    <div class="md:col-span-2">
        <x-form.label value="Telefone Profissional" for="phone_work" />
        <x-form.input type="text" wire:model.defer="phone_work" id="phone_work" value="{{ old('phone_work', $phone_work ?? '') }}" placeholder="(00) 00000-0000" data-mask="phone" maxlength="15"/>
        <x-form.error :messages="$errors->get('phone_work')" />
    </div>

</div>
