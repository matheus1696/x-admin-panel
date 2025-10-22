<div class="grid grid-cols-2 md:grid-cols-12 gap-6">

    <!-- Nome -->
    <div class="col-span-2 md:col-span-6">
        <x-form.label value="Nome" for="name" />
        <x-form.input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Digite o nome completo do usuário" required autocomplete="name" />
        <x-form.error :messages="$errors->get('name')" />
    </div>

    <!-- Email -->
    @isset ($user)
        <div class="col-span-2 md:col-span-6">
            <x-form.label value="E-mail" for="email" />
            <x-form.input type="email" id="email" value="{{ $user->email }}" disabled/>
        </div>
    @else
        <div class="col-span-2 md:col-span-6">
            <x-form.label value="E-mail" for="email" />
            <x-form.input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" placeholder="Digite o e-mail do usuário" required autocomplete="email" />
            <x-form.error :messages="$errors->get('email')" />
        </div>
    @endisset
    

    <!-- Telefone Pessoal -->
    <div class="md:col-span-4">
        <x-form.label value="Telefone Pessoal" for="phone_personal" />
        <x-form.input type="text" name="phone_personal" id="phone_personal" value="{{ old('phone_personal', $user->phone_personal ?? '') }}" placeholder="(00) 00000-0000" autocomplete="tel" />
        <x-form.error :messages="$errors->get('phone_personal')" />
    </div>

    <!-- Telefone Profissional -->
    <div class="md:col-span-4">
        <x-form.label value="Telefone Profissional" for="phone_work" />
        <x-form.input type="text" name="phone_work" id="phone_work" value="{{ old('phone_work', $user->phone_work ?? '') }}" placeholder="(00) 00000-0000" autocomplete="tel" />
        <x-form.error :messages="$errors->get('phone_work')" />
    </div>

    @isset($user)

        <!-- Status -->
        <div class="md:col-span-4">
            <x-form.label value="Status" for="status" />
            <x-form.select name="status" id="status" required>
                <option value="1" @selected(old('status', $user->status ?? '') === '1')>Ativo</option>
                <option value="0" @selected(old('status', $user->status ?? '') === '0')>Inativo</option>
            </x-form.select>
            <x-form.error :messages="$errors->get('status')" />
        </div>
        
    @endif

</div>
