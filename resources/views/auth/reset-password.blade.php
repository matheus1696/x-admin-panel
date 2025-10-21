<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-purple-600 via-blue-500 to-cyan-500 py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="flex justify-center items-center mb-10">
            <img src="{{ asset('asset/img/logo_white_full.png') }}" alt="X-AdminPanel Logo" class="h-10">
        </div>
        
        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ __("Reset your password") }}</h2>
                <p class="mt-3 text-sm text-gray-600">
                    {{ __("Create a new password for your account.") }}
                </p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-6">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <x-form.label for="email" :value="__('Email')" />
                    <x-form.input name="email" type="email" :value="old('email', $request->email)" required autofocus autocomplete="email" placeholder="Seu endereço de email" 
                    />
                    <x-form.error :messages="$errors->get('email')"/>
                </div>

                <!-- Password -->
                <div>
                    <x-form.label for="password" :value="__('New Password')" />
                    <x-form.input 
                        name="password" 
                        type="password" 
                        required 
                        autocomplete="new-password" 
                        placeholder="Digite sua nova senha" 
                    />
                    <x-form.error :messages="$errors->get('password')"/>
                    
                    <!-- Password Strength Hint -->
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __("Use 8+ characters with a mix of letters, numbers & symbols.") }}
                    </p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-form.label for="password_confirmation" :value="__('Confirm New Password')" />
                    <x-form.input 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        autocomplete="new-password" 
                        placeholder="Confirme sua nova senha" 
                    />
                    <x-form.error :messages="$errors->get('password_confirmation')"/>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <x-button.btn-submit value="{{ __('Reset Password') }}" />
                </div>

                <!-- Back to Login -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                        {{ __('Back to login') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>