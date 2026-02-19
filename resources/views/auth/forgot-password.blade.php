<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-emerald-700 to-emerald-800 py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Logo -->
        <div>
            @include('auth._partials.auth-logo')
        </div>

        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-xl p-8">
            <!-- Header -->
            <div class="text-center space-y-2">
                <h2 class="text-2xl font-semibold text-slate-800">{{ __('Redefinir senha') }}</h2>
                <p class="text-sm text-slate-500 text-justify leading-relaxed">
                    {{ __('Esqueceu sua senha? Sem problemas. Informe seu e-mail e enviaremos um link para redefinição.') }}
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-form.label for="email" :value="__('E-mail')" class="text-sm font-medium text-slate-700" />
                    <x-form.input 
                        name="email" 
                        type="email" 
                        :value="old('email')" 
                        required 
                        autofocus 
                        autocomplete="email" 
                        placeholder="seu@email.com"
                    />
                    <x-form.error for="email" class="mt-1 text-sm" />
                </div>

                <!-- Success Message (padronizada) -->
                @if (session('status'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                            <span class="text-xs text-emerald-700 font-medium">
                                {{ session('status') }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center text-sm text-emerald-600 hover:text-emerald-700 font-medium transition-colors group">
                        <i class="fas fa-arrow-left text-xs mr-2 group-hover:-translate-x-1 transition-transform"></i>
                        {{ __('Voltar ao login') }}
                    </a>

                    <x-button 
                        type="submit" 
                        text="{{ __('Enviar link') }}" 
                        size="sm"
                        variant="green_solid"
                        icon="fas fa-paper-plane"
                        withIconRight="true"
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