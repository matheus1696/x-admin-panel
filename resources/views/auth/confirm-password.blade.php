<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-emerald-700 to-emerald-800 py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Logo -->
        <div>
            @include('auth._partials.auth-logo')
        </div>

        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-xl p-8">
            <!-- Header -->
            <div class="text-center space-y-2">
                <h2 class="text-2xl font-semibold text-slate-800">{{ __('Confirmar senha') }}</h2>
                <p class="text-sm text-slate-500 leading-relaxed">
                    {{ __('Esta é uma área segura. Por favor, confirme sua senha antes de continuar.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-5">
                @csrf

                <!-- Password com toggle (igual ao login) -->
                <div x-data="{ show: false }">
                    <x-form.label for="password" :value="__('Sua senha')" class="text-sm font-medium text-slate-700" />
                    <div class="flex items-center gap-2 bg-slate-200 rounded-lg pr-3">
                        <x-form.input 
                            name="password" 
                            x-bind:type="show ? 'text' : 'password'" 
                            required 
                            autocomplete="current-password" 
                            placeholder="********"
                        />
                        <button type="button" x-on:click="show = !show" class="text-slate-400 hover:text-emerald-600 transition-colors">
                            <i x-show="!show" class="fas fa-eye text-sm"></i>
                            <i x-show="show" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <x-form.error for="password" class="mt-1 text-sm" />
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <x-button 
                        type="submit" 
                        text="{{ __('Confirmar senha') }}" 
                        fullWidth="true" 
                        size="sm"
                        variant="green_solid"
                        icon="fas fa-shield-alt"
                        preventSubmit="true"
                    />
                </div>

                <!-- Link para voltar (opcional) -->
                <div class="text-center">
                    <a href="{{ route('dashboard') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium transition-colors inline-flex items-center gap-1 group">
                        <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                        {{ __('Voltar para o dashboard') }}
                    </a>
                </div>

                <!-- Security Badge (igual ao login) -->
                <div class="flex items-center justify-center gap-2 text-xs text-slate-400 pt-2">
                    <i class="fas fa-lock text-emerald-500/70 text-xs"></i>
                    <span class="font-light">{{ __('Área segura - Confirmação necessária') }}</span>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div>
            @include('auth._partials.footer-card')
        </div>
    </div>
</x-guest-layout>