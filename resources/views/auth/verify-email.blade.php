<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-green-600 to-green-700 py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Logo -->
        @include('auth._partials.auth-logo')
        
        <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __("Verify your email address") }}</h2>
                <p class="mt-3 text-sm text-gray-600">
                    {{ __("Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?") }}
                </p>
            </div>

            <!-- Success Message -->
            @if (session('status') == 'verification-link-sent')
                <div class="rounded-md bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Additional Info -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-700">
                    {{ __("If you didn't receive the email, we will gladly send you another.") }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-4">
                <!-- Resend Verification Email -->
                <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:flex-1">
                    @csrf
                    <x-button.btn-submit value="{{ __('Resend Verification Email') }}" class="w-full" />
                </form>

                <!-- Log Out -->
                <form method="POST" action="{{ route('logout') }}" class="w-full sm:flex-1">
                    @csrf
                    <button type="submit" class="w-full text-center text-sm text-gray-600 hover:text-gray-900 font-medium py-2 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>

            <!-- Help Text -->
            <div class="text-center pt-4">
                <p class="text-xs text-gray-500">
                    {{ __("Check your spam folder if you can't find the verification email.") }}
                </p>
            </div>
        </div>

        <!-- Footer -->
        @include('auth._partials.footer-card')
    </div>
</x-guest-layout>