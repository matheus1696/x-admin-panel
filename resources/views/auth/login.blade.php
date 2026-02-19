<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-emerald-700 to-emerald-800 py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Logo (seu component) -->
        <div>
            @include('auth._partials.auth-logo')
        </div>

        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-xl p-8">
            <!-- Header -->
            <div class="text-center space-y-1">
                <h2 class="text-2xl font-semibold text-slate-800">{{ __('Bem-vindo de volta') }}</h2>
                <p class="text-sm text-slate-500">
                    {{ __('Acesse sua conta para continuar') }}
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-form.label for="email" :value="__('E-mail')" class="text-sm font-medium text-slate-700" />
                    <x-form.input name="email" type="email" value="admin@example.com" required autofocus autocomplete="email" placeholder="seu@email.com"/>
                    <x-form.error for="email" class="mt-1 text-sm" />
                </div>

                <!-- Password -->
                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-form.label for="password" :value="__('Senha')" class="text-sm font-medium text-slate-700" />
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium transition-colors">
                                {{ __('Esqueceu?') }}
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 bg-slate-200 rounded-lg pr-3">
                        <x-form.input name="password" x-bind:type="show ? 'text' : 'password'" required placeholder="********" />
                        <button type="button" x-on:click="show = !show" class="text-slate-400 hover:text-emerald-600 transition-colors">
                            <i x-show="!show" class="fas fa-eye text-sm"></i>
                            <i x-show="show" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <x-form.error for="password" class="mt-1 text-sm" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer group">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500/20 transition-colors"
                            name="remember"
                        >
                        <span class="text-sm text-slate-600 group-hover:text-slate-800 transition-colors">
                            {{ __('Manter-me conectado') }}
                        </span>
                    </label>
                </div>

                <!-- Submit Button (seu component) -->
                <div class="pt-2">
                    <x-button 
                        type="submit" 
                        text="{{ __('Entrar na conta') }}" 
                        fullWidth="true" 
                        size="sm"
                        variant="green_solid"
                        preventSubmit="true"
                    />
                </div>

                <!-- Register Link -->
                @if (Route::has('register'))
                    <p class="text-center text-sm text-slate-500 pt-2">
                        {{ __('Não tem uma conta?') }}
                        <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-700 font-medium ml-1 transition-colors">
                            {{ __('Criar agora') }}
                        </a>
                    </p>
                @endif
            </form>
        </div>

        <!-- Footer (seu component) -->
        <div>
            @include('auth._partials.footer-card')
        </div>
    </div>
</x-guest-layout>