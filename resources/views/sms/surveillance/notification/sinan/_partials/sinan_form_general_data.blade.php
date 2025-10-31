<!-- Divisão visual entre as seções -->
<div class="col-span-2 md:col-span-12 border-t border-gray-200 my-6">Dados Gerais</div>

<!-- Campos de Notificação -->
<div class="grid grid-cols-2 md:grid-cols-12 gap-4">
    
    <!-- Tipo de Notificação -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Tipo de Notificação" for="notification_type" />
        <x-form.select name="notification_type" id="notification_type" required>
            <option value="Individual" @selected(old('notification_type', $user->notification_type ?? '') === 'Individual')>Individual</option>
            <option value="Agravo" @selected(old('notification_type', $user->notification_type ?? '') === 'Agravo')>Agravo</option>
        </x-form.select>
        <x-form.error :messages="$errors->get('notification_type')" />
    </div>

    <!-- Agravo -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Agravo" for="agravo" />
        <x-form.select name="agravo" id="agravo" required>
            <option value="Intoxicação Exógena" @selected(old('notification_type', $user->notification_type ?? '') === 'Individual')>Individual</option>
            <option value="LER/DORT" @selected(old('notification_type', $user->notification_type ?? '') === 'Agravo')>Agravo</option>
            <option value="Acidente de Trabalho" @selected(old('notification_type', $user->notification_type ?? '') === 'Agravo')>Agravo</option>
            <option value="Acidente de Trabalho (Com exposição à material biológico)" @selected(old('notification_type', $user->notification_type ?? '') === 'Agravo')>Agravo</option>
        </x-form.select>
        <x-form.error :messages="$errors->get('agravo')" />
    </div>

    <!-- Data da Notificação -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Data da Notificação" for="notification_date" />
        <x-form.input type="date" name="notification_date" id="notification_date" value="{{ old('notification_date', isset($user->notification_date) ? \Carbon\Carbon::parse($user->notification_date)->format('Y-m-d') : '') }}" max="{{ now()->format('Y-m-d') }}"/>
        <x-form.error :messages="$errors->get('notification_date')" />
    </div>

    <!-- UF -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="UF" for="uf" />
        <x-form.select name="uf" id="uf" onchange="handleUFChange(event)">
            <option value="">Selecione a UF</option>
            <option value="AC" @selected(old('uf', $user->uf ?? '') === 'AC')>AC</option>
            <option value="AL" @selected(old('uf', $user->uf ?? '') === 'AL')>AL</option>
            <option value="AP" @selected(old('uf', $user->uf ?? '') === 'AP')>AP</option>
            <option value="AM" @selected(old('uf', $user->uf ?? '') === 'AM')>AM</option>
            <option value="BA" @selected(old('uf', $user->uf ?? '') === 'BA')>BA</option>
            <option value="CE" @selected(old('uf', $user->uf ?? '') === 'CE')>CE</option>
            <option value="DF" @selected(old('uf', $user->uf ?? '') === 'DF')>DF</option>
            <option value="ES" @selected(old('uf', $user->uf ?? '') === 'ES')>ES</option>
            <option value="GO" @selected(old('uf', $user->uf ?? '') === 'GO')>GO</option>
            <option value="MA" @selected(old('uf', $user->uf ?? '') === 'MA')>MA</option>
            <option value="MT" @selected(old('uf', $user->uf ?? '') === 'MT')>MT</option>
            <option value="MS" @selected(old('uf', $user->uf ?? '') === 'MS')>MS</option>
            <option value="MG" @selected(old('uf', $user->uf ?? '') === 'MG')>MG</option>
            <option value="PA" @selected(old('uf', $user->uf ?? '') === 'PA')>PA</option>
            <option value="PB" @selected(old('uf', $user->uf ?? '') === 'PB')>PB</option>
            <option value="PR" @selected(old('uf', $user->uf ?? '') === 'PR')>PR</option>
            <option value="PE" @selected(old('uf', $user->uf ?? '') === 'PE')>PE</option>
            <option value="PI" @selected(old('uf', $user->uf ?? '') === 'PI')>PI</option>
            <option value="RJ" @selected(old('uf', $user->uf ?? '') === 'RJ')>RJ</option>
            <option value="RN" @selected(old('uf', $user->uf ?? '') === 'RN')>RN</option>
            <option value="RS" @selected(old('uf', $user->uf ?? '') === 'RS')>RS</option>
            <option value="RO" @selected(old('uf', $user->uf ?? '') === 'RO')>RO</option>
            <option value="RR" @selected(old('uf', $user->uf ?? '') === 'RR')>RR</option>
            <option value="SC" @selected(old('uf', $user->uf ?? '') === 'SC')>SC</option>
            <option value="SP" @selected(old('uf', $user->uf ?? '') === 'SP')>SP</option>
            <option value="SE" @selected(old('uf', $user->uf ?? '') === 'SE')>SE</option>
            <option value="TO" @selected(old('uf', $user->uf ?? '') === 'TO')>TO</option>
        </x-form.select>
        <x-form.error :messages="$errors->get('uf')" />
    </div>

    <!-- Município de Notificação -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Município de Notificação" for="notification_city" />
        <x-form.input type="text" name="notification_city" id="notification_city" value="{{ old('notification_city', $user->notification_city ?? '') }}" placeholder="Nome do município" />
        <x-form.error :messages="$errors->get('notification_city')" />
    </div>

    <!-- Código IBGE -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Código IBGE" for="ibge_code" />
        <x-form.input type="text" name="ibge_code" id="ibge_code" value="{{ old('ibge_code', $user->ibge_code ?? '') }}" placeholder="0000000" maxlength="7" onkeyup="handleIBGECode(event)"/>
        <x-form.error :messages="$errors->get('ibge_code')" />
    </div>

    <!-- Unidade de Saúde -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Unidade de Saúde" for="health_unit" />
        <x-form.input type="text" name="health_unit" id="health_unit" value="{{ old('health_unit', $user->health_unit ?? '') }}" placeholder="Nome da unidade de saúde" />
        <x-form.error :messages="$errors->get('health_unit')" />
    </div>

    <!-- Código da Unidade -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Código" for="unit_code" />
        <x-form.input type="text" name="unit_code" id="unit_code" value="{{ old('unit_code', $user->unit_code ?? '') }}" placeholder="Código da unidade" />
        <x-form.error :messages="$errors->get('unit_code')" />
    </div>

    <!-- Data dos Primeiros Sintomas -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Data dos Primeiros Sintomas" for="first_symptoms_date" />
        <x-form.input type="date" name="first_symptoms_date" id="first_symptoms_date" value="{{ old('first_symptoms_date', isset($user->first_symptoms_date) ? \Carbon\Carbon::parse($user->first_symptoms_date)->format('Y-m-d') : '') }}" max="{{ now()->format('Y-m-d') }}"/>
        <x-form.error :messages="$errors->get('first_symptoms_date')" />
    </div>

</div>