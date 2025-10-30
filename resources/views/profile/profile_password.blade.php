<x-app-layout>
    <x-page.header 
        icon="fa-solid fa-key" 
        title="Alterar Senha" 
        subtitle="Atualize sua senha para manter sua conta segura"
    >
        <x-slot name="button">
            <x-button.btn-link href="{{ route('dashboard') }}" value="Voltar" icon="fa-solid fa-rotate-left" />
        </x-slot>
    </x-page.header>

    <div class="w-full md:w-1/2 mx-auto mt-5 py-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <form action="{{ route('profile.password.update') }}" method="POST" class="p-6">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 gap-6">

                    <!-- Senha Atual -->
                    <div>
                        <x-form.label value="Senha Atual" for="current_password" />
                        <x-form.input type="password" name="current_password" id="current_password" placeholder="********" required />
                        <x-form.error :messages="$errors->get('current_password')" />
                    </div>

                    <!-- Nova Senha -->
                    <div>
                        <x-form.label value="Nova Senha" for="password" />
                        <x-form.input type="password" name="password" id="password" placeholder="********" required />
                        <x-form.error :messages="$errors->get('password')" />
                    </div>

                    <!-- Confirmação -->
                    <div>
                        <x-form.label value="Confirmar Nova Senha" for="password_confirmation" />
                        <x-form.input type="password" name="password_confirmation" id="password_confirmation" placeholder="********" required />
                        <x-form.error :messages="$errors->get('password_confirmation')" />
                    </div>

                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end pt-6 mt-6">
                    <x-button.btn-submit value="Alterar Senha"/>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
