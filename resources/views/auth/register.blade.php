<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-green-800 via-green-700 to-green-600 py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Logo -->
        @include('auth._partials.auth-logo')
        
        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ __("Create your account") }}</h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __("Already registered?") }}
                    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500">
                        {{ __("Sign in to your account") }}
                    </a>
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <x-form.label for="name" :value="__('Name')" />
                    <x-form.input name="name" type="text" :value="old('name')" required autofocus autocomplete="name" placeholder="{{ __('Your Name') }}" />
                    <x-form.error for="name"/>
                </div>

                <!-- Email Address -->
                <div>
                    <x-form.label for="email" :value="__('Email')" />
                    <x-form.input name="email" type="email" :value="old('email')" required autocomplete="email" placeholder="{{ __('Email Address') }}" />
                    <x-form.error for="email"/>
                </div>

                <!-- Password -->
                <div>
                    <x-form.label for="password" :value="__('Password')" />
                    <x-form.input name="password" type="password" required placeholder="{{ __('Password') }}" />
                    <x-form.error for="password"/>
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-form.label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-form.input name="password_confirmation" type="password" required placeholder="{{ __('Confirm Password') }}" />
                    <x-form.error for="password_confirmation"/>
                </div>

                <!-- Terms & Privacy (Opcional) -->
                <div class="flex items-center justify-center h-5">
                    <div>
                        <input id="terms" name="terms" type="checkbox" class="focus:ring-green-500 text-green-600 border-gray-300 rounded" required>
                    </div>
                    <div class="ml-3 text-[11px]">
                        <label for="terms" class="font-medium text-gray-700">
                            {{ __("I agree to the") }}
                            <a href="#" class="text-green-600 hover:text-green-500">{{ __("Terms of Service") }}</a>
                            {{ __("and") }}
                            <a href="#" class="text-green-600 hover:text-green-500">{{ __("Privacy Policy") }}</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <x-button.btn-submit value="{{ __('Create Account') }}" />
                </div>
            </form>
        </div>

        <!-- Footer -->
        @include('auth._partials.footer-card')
    </div>
</x-guest-layout>