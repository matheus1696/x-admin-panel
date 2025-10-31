<x-app-layout>

    <div class="w-full md:w-1/2 mx-auto space-y-6 mt-6">

        <x-page.header icon="fa-solid fa-user" title="Meu Perfil" subtitle="Atualize seus dados pessoais" />

        <x-page.card>
            <form action="{{ route('profile.update') }}" method="POST" class="p-6">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                    <!-- Matricula -->
                    <div class="md:col-span-2">
                        <x-form.label value="Matrícula" for="matriculation" />
                        <x-form.input type="text" name="matriculation" id="matriculation" value="{{ old('matriculation', Auth::user()->matriculation) }}" placeholder="00.000-00" maxlength="9" required onkeyup="handleMatriculation(event)" />
                        <x-form.error :messages="$errors->get('matriculation')" />
                    </div>

                    <!-- CPF -->
                    <div class="md:col-span-2">
                        <x-form.label value="CPF" for="cpf" />
                        <x-form.input type="text" name="cpf" id="cpf" value="{{ old('cpf', Auth::user()->cpf) }}" placeholder="000.000.000-00" required onkeyup="handleCPF(event)" maxlength="14"/>
                        <x-form.error :messages="$errors->get('cpf')" />
                    </div>

                    <!-- Nome -->
                    <div class="col-span-2 md:col-span-4">
                        <x-form.label value="Nome" for="name" />
                        <x-form.input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" placeholder="Nome completo do usuário" required />
                        <x-form.error :messages="$errors->get('name')" />
                    </div>

                    <!-- Email (somente leitura) -->
                    <div class="col-span-2 md:col-span-4">
                        <x-form.label value="E-mail" for="email" />
                        <x-form.input type="email" id="email" value="{{ Auth::user()->email }}" disabled />
                    </div>

                    <!-- Data de Nascimento -->
                    <div class="md:col-span-2">
                        <x-form.label value="Data de Nascimento" for="birth_date" />
                        <x-form.input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', Auth::user()->birth_date ? \Carbon\Carbon::parse(Auth::user()->birth_date)->format('Y-m-d') : '') }}" min="1950-01-01" max="{{ now()->format('Y-m-d') }}" />
                        <x-form.error :messages="$errors->get('birth_date')" />
                    </div>

                    <!-- Gênero -->
                    <div class="md:col-span-2">
                        <x-form.label value="Gênero" for="gender" />
                        <x-form.select name="gender" id="gender">
                            <option value="Masculino" @selected(old('gender', Auth::user()->gender) === 'Masculino')>Masculino</option>
                            <option value="Feminino" @selected(old('gender', Auth::user()->gender) === 'Feminino')>Feminino</option>
                            <option value="Prefiro não informar" @selected(old('gender', Auth::user()->gender) === 'Prefiro não informar')>Prefiro não Informar</option>
                        </x-form.select>
                        <x-form.error :messages="$errors->get('gender')" />
                    </div>

                    <!-- Telefone Pessoal -->
                    <div class="md:col-span-2">
                        <x-form.label value="Telefone Pessoal" for="phone_personal" />
                        <x-form.input type="text" name="phone_personal" id="phone_personal" value="{{ old('phone_personal', Auth::user()->phone_personal) }}" placeholder="(00) 00000-0000" onkeyup="handlePhone(event)" maxlength="15"/>
                        <x-form.error :messages="$errors->get('phone_personal')" />
                    </div>

                    <!-- Telefone Profissional -->
                    <div class="md:col-span-2">
                        <x-form.label value="Telefone Profissional" for="phone_work" />
                        <x-form.input type="text" name="phone_work" id="phone_work" value="{{ old('phone_work', Auth::user()->phone_work) }}" placeholder="(00) 00000-0000" onkeyup="handlePhone(event)" maxlength="15" />
                        <x-form.error :messages="$errors->get('phone_work')" />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end mt-6">
                    <x-button.btn-submit value="Salvar Alterações"/>
                </div>
            </form>
        </x-page.card>
    </div>
</x-app-layout>
