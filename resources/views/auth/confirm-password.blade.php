<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-green-800 via-green-700 to-green-600 py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Logo -->
        @include('auth._partials.auth-logo')
        
        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ __("Confirm your password") }}</h2>
                <p class="mt-3 text-sm text-gray-600">
                    {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-6">
                @csrf

                <!-- Password -->
                <div>
                    <x-form.label for="password" :value="__('Password')" />
                    <x-form.input name="password" type="password" required autocomplete="current-password" placeholder="Digite sua senha atual" />
                    <x-form.error for="password"/>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <x-button.btn-submit value="{{ __('Confirm') }}" />
                </div>

                <!-- Help Text -->
                <div class="text-center">
                    <p class="text-xs text-gray-500">
                        {{ __("For your security, please confirm your password to continue.") }}
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        @include('auth._partials.footer-card')
    </div>
</x-guest-layout>