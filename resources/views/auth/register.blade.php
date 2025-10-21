<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-purple-600 via-blue-500 to-cyan-500 py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="flex justify-center items-center mb-10">
            <img src="{{ asset('asset/img/logo_white_full.png') }}" alt="X-AdminPanel Logo" class="h-10">
        </div>
        
        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ __("Create your account") }}</h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __("Already registered?") }}
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        {{ __("Sign in to your account") }}
                    </a>
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <x-form.label for="name" :value="__('Name')" />
                    <x-form.input 
                        name="name" 
                        type="text" 
                        :value="old('name')" 
                        required 
                        autofocus 
                        autocomplete="name" 
                        placeholder="Seu nome completo" 
                    />
                    <x-form.error :messages="$errors->get('name')"/>
                </div>

                <!-- Email Address -->
                <div>
                    <x-form.label for="email" :value="__('Email')" />
                    <x-form.input 
                        name="email" 
                        type="email" 
                        :value="old('email')" 
                        required 
                        autocomplete="email" 
                        placeholder="Seu endereço de email" 
                    />
                    <x-form.error :messages="$errors->get('email')"/>
                </div>

                <!-- Password -->
                <div>
                    <x-form.label for="password" :value="__('Password')" />
                    <x-form.input 
                        name="password" 
                        type="password" 
                        required 
                        autocomplete="new-password" 
                        placeholder="Sua senha" 
                    />
                    <x-form.error :messages="$errors->get('password')"/>
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-form.label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-form.input 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        autocomplete="new-password" 
                        placeholder="Confirme sua senha" 
                    />
                    <x-form.error :messages="$errors->get('password_confirmation')"/>
                </div>

                <!-- Terms & Privacy (Opcional) -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" required>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-gray-700">
                            {{ __("I agree to the") }}
                            <a href="#" class="text-indigo-600 hover:text-indigo-500">{{ __("Terms of Service") }}</a>
                            {{ __("and") }}
                            <a href="#" class="text-indigo-600 hover:text-indigo-500">{{ __("Privacy Policy") }}</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <x-button.btn-submit value="{{ __('Create Account') }}" />
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>