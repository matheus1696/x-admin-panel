<div class="grid grid-cols-2 md:grid-cols-12 gap-4">

    <!-- Matriculation -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="Matricula" for="matriculation" />
        <x-form.input type="text" name="matriculation" id="matriculation" value="{{ old('matriculation', $user->matriculation ?? '') }}" placeholder="00.000-00" maxlength="9" required onkeyup="handleMatriculation(event)"/>
        <x-form.error :messages="$errors->get('matriculation')" />
    </div>

    <!-- CPF -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="CPF" for="cpf" />
        <x-form.input type="text" name="cpf" id="cpf" value="{{ old('cpf', $user->cpf ?? '') }}" placeholder="000.000.000-00" required onkeyup="handleCPF(event)" maxlength="14"/>
        <x-form.error :messages="$errors->get('cpf')" />
    </div>

    <!-- Nome -->
    <div class="col-span-2 md:col-span-4">
        <x-form.label value="Nome" for="name" />
        <x-form.input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Nome completo do usuário" required />
        <x-form.error :messages="$errors->get('name')" />
    </div>

    <!-- Email -->
    @isset ($user)
        <div class="col-span-2 md:col-span-4">
            <x-form.label value="E-mail" for="email" />
            <x-form.input type="email" id="email" value="{{ $user->email }}" disabled/>
        </div>
    @else
        <div class="col-span-2 md:col-span-4">
            <x-form.label value="E-mail" for="email" />
            <x-form.input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" placeholder="Digite o e-mail do usuário" required />
            <x-form.error :messages="$errors->get('email')" />
        </div>
    @endisset

    <!-- Data de Nascimento -->
    <div class="md:col-span-3">
        <x-form.label value="Data de Nascimento" for="birth_date" />
        <x-form.input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', isset($user->birth_date) ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '') }}" min="1950-01-01" max="{{ now()->format('Y-m-d') }}"/>
        <x-form.error :messages="$errors->get('birth_date')" />
    </div>
    
    <!-- Gênero -->
    <div class="md:col-span-3">
        <x-form.label value="Gênero" for="gender" />
        <x-form.select name="gender" id="gender">
            <option value="Masculino" @selected(old('gender', $user->gender ?? 'Masculino') === 'Masculino')>Masculino</option>
            <option value="Feminino" @selected(old('gender', $user->gender ?? 'Feminino') === 'Feminino')>Feminino</option>
        </x-form.select>
        <x-form.error :messages="$errors->get('gender')" />
    </div>

    <!-- Telefone Pessoal -->
    <div class="md:col-span-3">
        <x-form.label value="Telefone Pessoal" for="phone_personal" />
        <x-form.input type="text" name="phone_personal" id="phone_personal" value="{{ old('phone_personal', $user->phone_personal ?? '') }}" placeholder="(00) 00000-0000" onkeyup="handlePhone(event)" maxlength="15"/>
        <x-form.error :messages="$errors->get('phone_personal')" />
    </div>

    <!-- Telefone Profissional -->
    <div class="md:col-span-3">
        <x-form.label value="Telefone Profissional" for="phone_work" />
        <x-form.input type="text" name="phone_work" id="phone_work" value="{{ old('phone_work', $user->phone_work ?? '') }}" placeholder="(00) 00000-0000" onkeyup="handlePhone(event)" maxlength="15"/>
        <x-form.error :messages="$errors->get('phone_work')" />
    </div>

    @isset($user)
        <!-- Status -->
        <div class="md:col-span-3">
            <x-form.label value="Status" for="status" />
            <x-form.select name="status" id="status" required>
                <option value="1" @selected(old('status', $user->status ?? '1') == '1')>Ativo</option>
                <option value="0" @selected(old('status', $user->status ?? '1') == '0')>Inativo</option>
            </x-form.select>
            <x-form.error :messages="$errors->get('status')" />
        </div>        
    @endif

</div>
