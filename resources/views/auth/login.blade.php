<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-green-800 via-green-700 to-green-600 py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center gap-2 uppercase font-semibold text-white mb-6">
            <img src="{{ asset('asset/img/logo.png') }}" alt="Logo X-AdminPanel" class="h-10">
            <span class="text-2xl">{{ config('app.name') }}</span>
        </div>
        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ __("Log in to your account") }}</h2>
                <p class="mt-2 text-sm text-gray-600">
                    
                    @if (Route::has('register'))
                        {{ __("or") }}  
                        <a href="{{ route('register') }}" class="font-medium text-green-600 hover:text-green-500">
                            {{ __("create a new account") }}
                        </a>
                    @endif
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-form.label for="email" :value="__('Email')" />
                    <x-form.input name="email" type="email" :value="old('email')" required autofocus autocomplete="email" placeholder="Seu endereço de email" value="admin@example.com"/>
                    <x-form.error :messages="$errors->get('email')"/>
                </div>

                <!-- Password -->
                <div>
                    <x-form.label for="password" :value="__('Password')" />
                    <x-form.input name="password" type="password" required :placeholder="__('Password')" value="password" />
                    <x-form.error :messages="$errors->get('password')"/>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" name="remember">
                        <span class="ms-2 text-xs text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-xs text-green-600 hover:text-green-500 font-medium" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <x-button.btn-submit value="{{ __('Log in') }}" />
                </div>
            </form>
        </div>
        <div class="mt-2 text-[9px] text-center text-white uppercase tracking-wider font-medium">
            <p>Desenvolvido por <a href="https://webxperts.com.br">Webxperts</a> {{ config('app.name') }}</p>
        </div>
    </div>
</x-guest-layout>