<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-emerald-600 to-emerald-800 py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Logo -->
        <div>
            @include('auth._partials.auth-logo')
        </div>

        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-xl p-8">
            <!-- Header -->
            <div class="text-center space-y-3">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 mb-2">
                    <i class="fas fa-envelope-open-text text-2xl text-emerald-600"></i>
                </div>
                <h2 class="text-2xl font-semibold text-slate-800">{{ __('Verifique seu e-mail') }}</h2>
                <p class="text-sm text-slate-500 leading-relaxed">
                    {{ __('Obrigado por se cadastrar! Enviamos um link de verificação para o seu e-mail.') }}
                </p>
            </div>

            <!-- Success Message -->
            @if (session('status') == 'verification-link-sent')
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                        <p class="text-xs text-emerald-700 font-medium">
                            {{ __('Um novo link de verificação foi enviado para seu e-mail.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Additional Info -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-emerald-500 text-sm mt-0.5"></i>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ __('Se você não recebeu o e-mail, podemos enviar outro.') }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 justify-between items-center pt-4">
                <!-- Resend Verification Email -->
                <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:flex-1">
                    @csrf
                    <x-button 
                        type="submit" 
                        text="{{ __('Reenviar e-mail') }}" 
                        fullWidth="true" 
                        size="sm"
                        variant="green_solid"
                        icon="fas fa-paper-plane"
                    />
                </form>

                <!-- Log Out -->
                <form method="POST" action="{{ route('logout') }}" class="w-full sm:flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full text-sm text-slate-600 hover:text-slate-900 font-medium py-2.5 px-4 
                                   border border-slate-200 rounded-xl hover:bg-slate-50 
                                   transition-all duration-200 flex items-center justify-center gap-2 group">
                        <i class="fas fa-sign-out-alt text-xs group-hover:-translate-x-1 transition-transform"></i>
                        {{ __('Sair da conta') }}
                    </button>
                </form>
            </div>

            <!-- Help Text -->
            <div class="text-center pt-2">
                <p class="text-xs text-slate-400 flex items-center justify-center gap-1">
                    <i class="fas fa-exclamation-circle text-emerald-500/70"></i>
                    {{ __('Verifique também sua caixa de spam se necessário.') }}
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div>
            @include('auth._partials.footer-card')
        </div>
    </div>
</x-guest-layout>