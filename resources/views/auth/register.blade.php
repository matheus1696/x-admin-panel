<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-emerald-700 to-emerald-800 py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Logo -->
        <div>
            @include('auth._partials.auth-logo')
        </div>

        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-xl p-8">
            <!-- Header -->
            <div class="text-center space-y-1">
                <h2 class="text-2xl font-semibold text-slate-800">{{ __('Criar sua conta') }}</h2>
                <p class="text-sm text-slate-500">
                    {{ __('Já tem uma conta?') }}
                    <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 font-medium ml-1 transition-colors">
                        {{ __('Fazer login') }}
                    </a>
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <x-form.label for="name" :value="__('Nome completo')" class="text-sm font-medium text-slate-700" />
                    <x-form.input 
                        name="name" 
                        type="text" 
                        :value="old('name')" 
                        required 
                        autofocus 
                        autocomplete="name" 
                        placeholder="Seu nome completo"
                    />
                    <x-form.error for="name" class="mt-1 text-sm" />
                </div>

                <!-- Email Address -->
                <div>
                    <x-form.label for="email" :value="__('E-mail')" class="text-sm font-medium text-slate-700" />
                    <x-form.input 
                        name="email" 
                        type="email" 
                        :value="old('email')" 
                        required 
                        autocomplete="email" 
                        placeholder="seu@email.com"
                    />
                    <x-form.error for="email" class="mt-1 text-sm" />
                </div>

                <!-- Password -->
                <div x-data="{ show: false }">
                    <x-form.label for="password" :value="__('Senha')" class="text-sm font-medium text-slate-700" />
                    <div class="flex items-center gap-2 bg-slate-200 rounded-lg pr-3">
                        <x-form.input 
                            name="password" 
                            x-bind:type="show ? 'text' : 'password'" 
                            required 
                            placeholder="********"
                        />
                        <button type="button" x-on:click="show = !show" class="text-slate-400 hover:text-emerald-600 transition-colors">
                            <i x-show="!show" class="fas fa-eye text-sm"></i>
                            <i x-show="show" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <x-form.error for="password" class="mt-1 text-sm" />
                </div>

                <!-- Confirm Password -->
                <div x-data="{ show: false }">
                    <x-form.label for="password_confirmation" :value="__('Confirmar senha')" class="text-sm font-medium text-slate-700" />
                    <div class="flex items-center gap-2 bg-slate-200 rounded-lg pr-3">
                        <x-form.input 
                            name="password_confirmation" 
                            x-bind:type="show ? 'text' : 'password'" 
                            required 
                            placeholder="********"
                        />
                        <button type="button" x-on:click="show = !show" class="text-slate-400 hover:text-emerald-600 transition-colors">
                            <i x-show="!show" class="fas fa-eye text-sm"></i>
                            <i x-show="show" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <x-form.error for="password_confirmation" class="mt-1 text-sm" />
                </div>

                <!-- Terms & Privacy -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            id="terms" 
                            name="terms" 
                            type="checkbox" 
                            class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500/20 transition-colors"
                            required
                        >
                    </div>
                    <div class="ml-3">
                        <label for="terms" class="text-xs text-slate-600">
                            {{ __('Eu concordo com os') }}
                            <a href="#" class="text-emerald-600 hover:text-emerald-700 font-medium mx-1 transition-colors">
                                {{ __('Termos de Serviço') }}
                            </a>
                            {{ __('e') }}
                            <a href="#" class="text-emerald-600 hover:text-emerald-700 font-medium mx-1 transition-colors">
                                {{ __('Política de Privacidade') }}
                            </a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <x-button 
                        type="submit" 
                        text="{{ __('Criar minha conta') }}" 
                        fullWidth="true" 
                        size="sm"
                        variant="green_solid"
                        preventSubmit="true"
                    />
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div>
            @include('auth._partials.footer-card')
        </div>
    </div>
</x-guest-layout>