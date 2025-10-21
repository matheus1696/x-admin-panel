<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-purple-600 via-blue-500 to-cyan-500 py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="flex justify-center items-center mb-10">
            <img src="{{ asset('asset/img/logo_white_full.png') }}" alt="X-AdminPanel Logo" class="h-10">
        </div>
        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ __("Log in to your account") }}</h2>
                <p class="mt-2 text-sm text-gray-600">
                    
                    @if (Route::has('register'))
                        {{ __("or") }}  
                        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
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
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2 text-xs text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-xs text-indigo-600 hover:text-indigo-500 font-medium" href="{{ route('password.request') }}">
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
    </div>
</x-guest-layout>