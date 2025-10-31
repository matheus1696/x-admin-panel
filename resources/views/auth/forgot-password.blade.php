<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-green-800 via-green-700 to-green-600 py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="flex justify-center items-center mb-10">
            <img src="{{ asset('asset/img/logo_white_full.png') }}" alt="X-AdminPanel Logo" class="h-10">
        </div>
        
        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ __("Reset your password") }}</h2>
                <p class="mt-3 text-sm text-gray-600">
                    {{ __("Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.") }}
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-form.label for="email" :value="__('Email')" />
                    <x-form.input name="email" type="email" :value="old('email')" required autofocus autocomplete="email" placeholder="Seu endereço de email" />
                    <x-form.error :messages="$errors->get('email')"/>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 items-center justify-between pt-4">
                    <a href="{{ route('login') }}" class="text-sm text-green-600 hover:text-green-500 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        {{ __('Back to login') }}
                    </a>

                    <x-button.btn-submit value="{{ __('Send Reset Link') }}" />
                </div>

                <!-- Success Message -->
                @if (session('status'))
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-green-700 font-medium">
                                {{ session('status') }}
                            </span>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</x-guest-layout>