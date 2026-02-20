<x-app-layout>

    <div class="w-full md:w-1/2 mx-auto space-y-6 mt-6">
        
        <x-page.header icon="fa-solid fa-key" title="Alterar Senha" subtitle="Atualize sua senha para manter sua conta segura" />

        <x-page.card>
            <form action="{{ route('profile.password.update') }}" method="POST" class="p-6">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 gap-6">

                    <!-- Nome -->
                    <div>
                        <x-form.label value="Nome" />
                        <x-form.input :value="Auth::user()->name" disabled />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-form.label value="E-mail" />
                        <x-form.input :value="Auth::user()->email" disabled />
                    </div>

                    <!-- Senha Atual -->
                    <div>
                        <x-form.label value="Senha Atual" for="current_password" />
                        <x-form.input type="password" name="current_password" id="current_password" placeholder="********" required />
                        <x-form.error for="current_password" />
                    </div>

                    <!-- Nova Senha -->
                    <div>
                        <x-form.label value="Nova Senha" for="password" />
                        <x-form.input type="password" name="password" id="password" placeholder="********" required />
                        <x-form.error for="password" />
                    </div>

                    <!-- Confirmação -->
                    <div>
                        <x-form.label value="Confirmar Nova Senha" for="password_confirmation" />
                        <x-form.input type="password" name="password_confirmation" id="password_confirmation" placeholder="********" required />
                        <x-form.error for="password_confirmation" />
                    </div>

                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end mt-6">
                    <x-button type="submit" text="Alterar Senha" fullWidth="true"/>
                </div>
            </form>
        </x-page.card>
    </div>
</x-app-layout>
