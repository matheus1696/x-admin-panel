<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-emerald-600 to-emerald-800 py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Logo -->
        <div>
            @include('auth._partials.auth-logo')
        </div>

        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-xl p-8">
            <!-- Header -->
            <div class="text-center space-y-2">
                <h2 class="text-2xl font-semibold text-slate-800">{{ __('Criar nova senha') }}</h2>
                <p class="text-sm text-slate-500 leading-relaxed">
                    {{ __('Crie uma nova senha para sua conta.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <x-form.label for="email" :value="__('E-mail')" class="text-sm font-medium text-slate-700" />
                    <x-form.input 
                        name="email" 
                        type="email" 
                        :value="old('email', $request->email)" 
                        required 
                        autofocus 
                        autocomplete="email" 
                        placeholder="seu@email.com"
                        class="bg-slate-50"
                        readonly
                    />
                    <x-form.error for="email" class="mt-1 text-sm" />
                </div>

                <!-- New Password -->
                <div x-data="{ show: false }">
                    <x-form.label for="password" :value="__('Nova senha')" class="text-sm font-medium text-slate-700" />
                    <div class="flex items-center gap-2 bg-slate-200 rounded-lg pr-3">
                        <x-form.input 
                            name="password" 
                            x-bind:type="show ? 'text' : 'password'" 
                            required 
                            autocomplete="new-password" 
                            placeholder="********"
                        />
                        <button type="button" x-on:click="show = !show" class="text-slate-400 hover:text-emerald-600 transition-colors">
                            <i x-show="!show" class="fas fa-eye text-sm"></i>
                            <i x-show="show" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <x-form.error for="password" class="mt-1 text-sm" />
                    
                    <!-- Password Strength Hint -->
                    <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                        <i class="fas fa-info-circle text-emerald-500"></i>
                        {{ __("Mínimo 8 caracteres com letras, números e símbolos.") }}
                    </p>
                </div>

                <!-- Confirm New Password -->
                <div x-data="{ show: false }">
                    <x-form.label for="password_confirmation" :value="__('Confirmar nova senha')" class="text-sm font-medium text-slate-700" />
                    <div class="flex items-center gap-2 bg-slate-200 rounded-lg pr-3">
                        <x-form.input 
                            name="password_confirmation" 
                            x-bind:type="show ? 'text' : 'password'" 
                            required 
                            autocomplete="new-password" 
                            placeholder="********"
                        />
                        <button type="button" x-on:click="show = !show" class="text-slate-400 hover:text-emerald-600 transition-colors">
                            <i x-show="!show" class="fas fa-eye text-sm"></i>
                            <i x-show="show" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <x-form.error for="password_confirmation" class="mt-1 text-sm" />
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <x-button 
                        type="submit" 
                        text="{{ __('Redefinir senha') }}" 
                        fullWidth="true" 
                        size="sm"
                        variant="green_solid"
                        icon="fas fa-key"
                    />
                </div>

                <!-- Back to Login -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium transition-colors inline-flex items-center gap-1 group">
                        <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                        {{ __('Voltar ao login') }}
                    </a>
                </div>

                <!-- Security Hint -->
                <div class="flex items-center justify-center gap-2 text-xs text-slate-400 pt-2">
                    <i class="fas fa-shield-alt text-emerald-500/70 text-xs"></i>
                    <span class="font-light">{{ __('Escolha uma senha forte e única') }}</span>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div>
            @include('auth._partials.footer-card')
        </div>
    </div>
</x-guest-layout>