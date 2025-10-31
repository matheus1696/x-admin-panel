<!-- Divisão visual entre as seções -->
<div class="col-span-2 md:col-span-12 border-t border-gray-200 my-6">Dados Individuais</div>

<!-- Dados do Paciente -->
<div class="grid grid-cols-2 md:grid-cols-12 gap-4">
    
    <!-- Nome do Paciente -->
    <div class="col-span-2 md:col-span-6">
        <x-form.label value="Nome do Paciente" for="patient_name" />
        <x-form.input type="text" name="patient_name" id="patient_name" value="{{ old('patient_name', $user->patient_name ?? '') }}" placeholder="Nome completo do paciente" required />
        <x-form.error :messages="$errors->get('patient_name')" />
    </div>

    <!-- Data de Nascimento do Paciente -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Data de Nascimento" for="patient_birth_date" />
        <x-form.input type="date" name="patient_birth_date" id="patient_birth_date" value="{{ old('patient_birth_date', isset($user->patient_birth_date) ? \Carbon\Carbon::parse($user->patient_birth_date)->format('Y-m-d') : '') }}" min="1900-01-01" max="{{ now()->format('Y-m-d') }}" onchange="calculateAge()"/>
        <x-form.error :messages="$errors->get('patient_birth_date')" />
    </div>

    <!-- Idade -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Idade" for="patient_age" />
        <x-form.input type="text" name="patient_age" id="patient_age" value="{{ old('patient_age', $user->patient_age ?? '') }}" placeholder="Idade calculada" readonly />
        <x-form.error :messages="$errors->get('patient_age')" />
    </div>

    <!-- Sexo -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Sexo" for="patient_gender" />
        <x-form.select name="patient_gender" id="patient_gender" required>
            <option value="">Selecione o sexo</option>
            <option value="Masculino" @selected(old('patient_gender', $user->patient_gender ?? '') === 'Masculino')>Masculino</option>
            <option value="Feminino" @selected(old('patient_gender', $user->patient_gender ?? '') === 'Feminino')>Feminino</option>
            <option value="Ignorado" @selected(old('patient_gender', $user->patient_gender ?? '') === 'Ignorado')>Ignorado</option>
        </x-form.select>
        <x-form.error :messages="$errors->get('patient_gender')" />
    </div>

    <!-- Gestante -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Gestante" for="pregnant" />
        <x-form.select name="pregnant" id="pregnant">
            <option value="">Selecione a situação</option>
            <option value="1º trimestre" @selected(old('pregnant', $user->pregnant ?? '') === '1º trimestre')>1º trimestre</option>
            <option value="2º trimestre" @selected(old('pregnant', $user->pregnant ?? '') === '2º trimestre')>2º trimestre</option>
            <option value="3º trimestre" @selected(old('pregnant', $user->pregnant ?? '') === '3º trimestre')>3º trimestre</option>
            <option value="idade gestacional ignorada" @selected(old('pregnant', $user->pregnant ?? '') === 'idade gestacional ignorada')>Idade gestacional ignorada</option>
            <option value="não" @selected(old('pregnant', $user->pregnant ?? '') === 'não')>Não</option>
            <option value="não se aplica" @selected(old('pregnant', $user->pregnant ?? '') === 'não se aplica')>Não se aplica</option>
            <option value="ignorado" @selected(old('pregnant', $user->pregnant ?? '') === 'ignorado')>Ignorado</option>
        </x-form.select>
        <x-form.error :messages="$errors->get('pregnant')" />
    </div>

    <!-- Raça/Cor -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Raça/Cor" for="race" />
        <x-form.select name="race" id="race" required>
            <option value="">Selecione a raça/cor</option>
            <option value="Branca" @selected(old('race', $user->race ?? '') === 'Branca')>Branca</option>
            <option value="Preta" @selected(old('race', $user->race ?? '') === 'Preta')>Preta</option>
            <option value="Amarela" @selected(old('race', $user->race ?? '') === 'Amarela')>Amarela</option>
            <option value="Parda" @selected(old('race', $user->race ?? '') === 'Parda')>Parda</option>
            <option value="Indígena" @selected(old('race', $user->race ?? '') === 'Indígena')>Indígena</option>
            <option value="Ignorado" @selected(old('race', $user->race ?? '') === 'Ignorado')>Ignorado</option>
        </x-form.select>
        <x-form.error :messages="$errors->get('race')" />
    </div>

    <!-- Escolaridade -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Escolaridade" for="education" />
        <x-form.select name="education" id="education">
            <option value="">Selecione a escolaridade</option>
            <option value="Analfabeto" @selected(old('education', $user->education ?? '') === 'Analfabeto')>Analfabeto</option>
            <option value="Fundamental Incompleto" @selected(old('education', $user->education ?? '') === 'Fundamental Incompleto')>Fundamental Incompleto</option>
            <option value="Fundamental Completo" @selected(old('education', $user->education ?? '') === 'Fundamental Completo')>Fundamental Completo</option>
            <option value="Médio Incompleto" @selected(old('education', $user->education ?? '') === 'Médio Incompleto')>Médio Incompleto</option>
            <option value="Médio Completo" @selected(old('education', $user->education ?? '') === 'Médio Completo')>Médio Completo</option>
            <option value="Superior Incompleto" @selected(old('education', $user->education ?? '') === 'Superior Incompleto')>Superior Incompleto</option>
            <option value="Superior Completo" @selected(old('education', $user->education ?? '') === 'Superior Completo')>Superior Completo</option>
            <option value="Pós-graduação" @selected(old('education', $user->education ?? '') === 'Pós-graduação')>Pós-graduação</option>
            <option value="Ignorado" @selected(old('education', $user->education ?? '') === 'Ignorado')>Ignorado</option>
        </x-form.select>
        <x-form.error :messages="$errors->get('education')" />
    </div>

    <!-- Nº Cartão SUS -->
    <div class="col-span-2 md:col-span-4">
        <x-form.label value="Nº Cartão SUS" for="sus_card" />
        <x-form.input type="text" name="sus_card" id="sus_card" value="{{ old('sus_card', $user->sus_card ?? '') }}" placeholder="000 0000 0000 0000" onkeyup="handleSUSCard(event)" maxlength="18"/>
        <x-form.error :messages="$errors->get('sus_card')" />
    </div>

    <!-- Nome da Mãe -->
    <div class="col-span-2 md:col-span-5">
        <x-form.label value="Nome da Mãe" for="mother_name" />
        <x-form.input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name', $user->mother_name ?? '') }}" placeholder="Nome completo da mãe do paciente" />
        <x-form.error :messages="$errors->get('mother_name')" />
    </div>

</div>