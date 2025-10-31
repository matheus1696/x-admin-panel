<!-- Divisão visual entre as seções -->
<div class="col-span-2 md:col-span-12 border-t border-gray-200 my-6">Dados de Residência</div>

<!-- Dados de Residência -->
<div class="grid grid-cols-2 md:grid-cols-12 gap-4">
    
    <!-- UF de Residência -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="UF de Residência" for="residence_uf" />
        <x-form.select name="residence_uf" id="residence_uf" onchange="handleResidenceUFChange(event)">
            <option value="">Selecione a UF</option>
            <option value="AC" @selected(old('residence_uf', $user->residence_uf ?? '') === 'AC')>AC</option>
            <option value="AL" @selected(old('residence_uf', $user->residence_uf ?? '') === 'AL')>AL</option>
            <option value="AP" @selected(old('residence_uf', $user->residence_uf ?? '') === 'AP')>AP</option>
            <option value="AM" @selected(old('residence_uf', $user->residence_uf ?? '') === 'AM')>AM</option>
            <option value="BA" @selected(old('residence_uf', $user->residence_uf ?? '') === 'BA')>BA</option>
            <option value="CE" @selected(old('residence_uf', $user->residence_uf ?? '') === 'CE')>CE</option>
            <option value="DF" @selected(old('residence_uf', $user->residence_uf ?? '') === 'DF')>DF</option>
            <option value="ES" @selected(old('residence_uf', $user->residence_uf ?? '') === 'ES')>ES</option>
            <option value="GO" @selected(old('residence_uf', $user->residence_uf ?? '') === 'GO')>GO</option>
            <option value="MA" @selected(old('residence_uf', $user->residence_uf ?? '') === 'MA')>MA</option>
            <option value="MT" @selected(old('residence_uf', $user->residence_uf ?? '') === 'MT')>MT</option>
            <option value="MS" @selected(old('residence_uf', $user->residence_uf ?? '') === 'MS')>MS</option>
            <option value="MG" @selected(old('residence_uf', $user->residence_uf ?? '') === 'MG')>MG</option>
            <option value="PA" @selected(old('residence_uf', $user->residence_uf ?? '') === 'PA')>PA</option>
            <option value="PB" @selected(old('residence_uf', $user->residence_uf ?? '') === 'PB')>PB</option>
            <option value="PR" @selected(old('residence_uf', $user->residence_uf ?? '') === 'PR')>PR</option>
            <option value="PE" @selected(old('residence_uf', $user->residence_uf ?? '') === 'PE')>PE</option>
            <option value="PI" @selected(old('residence_uf', $user->residence_uf ?? '') === 'PI')>PI</option>
            <option value="RJ" @selected(old('residence_uf', $user->residence_uf ?? '') === 'RJ')>RJ</option>
            <option value="RN" @selected(old('residence_uf', $user->residence_uf ?? '') === 'RN')>RN</option>
            <option value="RS" @selected(old('residence_uf', $user->residence_uf ?? '') === 'RS')>RS</option>
            <option value="RO" @selected(old('residence_uf', $user->residence_uf ?? '') === 'RO')>RO</option>
            <option value="RR" @selected(old('residence_uf', $user->residence_uf ?? '') === 'RR')>RR</option>
            <option value="SC" @selected(old('residence_uf', $user->residence_uf ?? '') === 'SC')>SC</option>
            <option value="SP" @selected(old('residence_uf', $user->residence_uf ?? '') === 'SP')>SP</option>
            <option value="SE" @selected(old('residence_uf', $user->residence_uf ?? '') === 'SE')>SE</option>
            <option value="TO" @selected(old('residence_uf', $user->residence_uf ?? '') === 'TO')>TO</option>
        </x-form.select>
        <x-form.error :messages="$errors->get('residence_uf')" />
    </div>

    <!-- Município de Residência -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Município de Residência" for="residence_city" />
        <x-form.input type="text" name="residence_city" id="residence_city" value="{{ old('residence_city', $user->residence_city ?? '') }}" placeholder="Nome do município" />
        <x-form.error :messages="$errors->get('residence_city')" />
    </div>

    <!-- Código IBGE Residência -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="Código IBGE" for="residence_ibge_code" />
        <x-form.input type="text" name="residence_ibge_code" id="residence_ibge_code" value="{{ old('residence_ibge_code', $user->residence_ibge_code ?? '') }}" placeholder="0000000" maxlength="7" onkeyup="handleResidenceIBGECode(event)"/>
        <x-form.error :messages="$errors->get('residence_ibge_code')" />
    </div>

    <!-- Distrito -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="Distrito" for="district" />
        <x-form.input type="text" name="district" id="district" value="{{ old('district', $user->district ?? '') }}" placeholder="Nome do distrito" />
        <x-form.error :messages="$errors->get('district')" />
    </div>

    <!-- Bairro -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Bairro" for="neighborhood" />
        <x-form.input type="text" name="neighborhood" id="neighborhood" value="{{ old('neighborhood', $user->neighborhood ?? '') }}" placeholder="Nome do bairro" />
        <x-form.error :messages="$errors->get('neighborhood')" />
    </div>

    <!-- Logradouro -->
    <div class="col-span-2 md:col-span-4">
        <x-form.label value="Logradouro" for="street" />
        <x-form.input type="text" name="street" id="street" value="{{ old('street', $user->street ?? '') }}" placeholder="Nome da rua, avenida, etc." />
        <x-form.error :messages="$errors->get('street')" />
    </div>

    <!-- Código do Logradouro -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="Código" for="street_code" />
        <x-form.input type="text" name="street_code" id="street_code" value="{{ old('street_code', $user->street_code ?? '') }}" placeholder="Código" />
        <x-form.error :messages="$errors->get('street_code')" />
    </div>

    <!-- Número -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="Número" for="street_number" />
        <x-form.input type="text" name="street_number" id="street_number" value="{{ old('street_number', $user->street_number ?? '') }}" placeholder="Nº" />
        <x-form.error :messages="$errors->get('street_number')" />
    </div>

    <!-- Complemento -->
    <div class="col-span-2 md:col-span-4">
        <x-form.label value="Complemento" for="complement" />
        <x-form.input type="text" name="complement" id="complement" value="{{ old('complement', $user->complement ?? '') }}" placeholder="Apto, casa, bloco, etc." />
        <x-form.error :messages="$errors->get('complement')" />
    </div>

    <!-- Geo Campo 1 -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Geo Campo 1" for="geo_field_1" />
        <x-form.input type="text" name="geo_field_1" id="geo_field_1" value="{{ old('geo_field_1', $user->geo_field_1 ?? '') }}" placeholder="Coordenada ou dado geográfico" />
        <x-form.error :messages="$errors->get('geo_field_1')" />
    </div>

    <!-- Geo Campo 2 -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Geo Campo 2" for="geo_field_2" />
        <x-form.input type="text" name="geo_field_2" id="geo_field_2" value="{{ old('geo_field_2', $user->geo_field_2 ?? '') }}" placeholder="Coordenada ou dado geográfico" />
        <x-form.error :messages="$errors->get('geo_field_2')" />
    </div>

    <!-- Ponto de Referência -->
    <div class="col-span-2 md:col-span-6">
        <x-form.label value="Ponto de Referência" for="reference_point" />
        <x-form.input name="reference_point" id="reference_point" placeholder="Ponto de referência próximo" rows="2">{{ old('reference_point', $user->reference_point ?? '') }}</x-form.input>
        <x-form.error :messages="$errors->get('reference_point')" />
    </div>

    <!-- CEP -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="CEP" for="zip_code" />
        <x-form.input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', $user->zip_code ?? '') }}" placeholder="00000-000" onkeyup="handleCEP(event)" maxlength="9"/>
        <x-form.error :messages="$errors->get('zip_code')" />
    </div>

    <!-- Telefone de Residência -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="Telefone" for="residence_phone" />
        <x-form.input type="text" name="residence_phone" id="residence_phone" value="{{ old('residence_phone', $user->residence_phone ?? '') }}" placeholder="(00) 00000-0000" onkeyup="handlePhone(event)" maxlength="15"/>
        <x-form.error :messages="$errors->get('residence_phone')" />
    </div>

    <!-- Zona -->
    <div class="col-span-2 md:col-span-2">
        <x-form.label value="Zona" for="zone" />
        <x-form.select name="zone" id="zone">
            <option value="">Selecione a zona</option>
            <option value="Urbana" @selected(old('zone', $user->zone ?? '') === 'Urbana')>Urbana</option>
            <option value="Rural" @selected(old('zone', $user->zone ?? '') === 'Rural')>Rural</option>
            <option value="Periurbana" @selected(old('zone', $user->zone ?? '') === 'Periurbana')>Periurbana</option>
            <option value="Ignorado" @selected(old('zone', $user->zone ?? '') === 'Ignorado')>Ignorado</option>
        </x-form.select>
        <x-form.error :messages="$errors->get('zone')" />
    </div>

    <!-- País (para residentes fora do Brasil) -->
    <div class="col-span-2 md:col-span-3">
        <x-form.label value="País (se residente fora do Brasil)" for="country" />
        <x-form.input type="text" name="country" id="country" value="{{ old('country', $user->country ?? '') }}" placeholder="Nome do país" />
        <x-form.error :messages="$errors->get('country')" />
    </div>

</div>