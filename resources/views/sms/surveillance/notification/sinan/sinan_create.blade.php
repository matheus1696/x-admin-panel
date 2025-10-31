<x-app-layout>
    <x-page.header icon="fa-solid fa-users" title="SINAN" subtitle="Sistema de Informação de Agravos de Notificação" />

    <div class="py-6">
        <!-- Card do Formulário -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Formulário -->
            <form action="{{ route('users.store') }}" method="POST" class="p-6">
                @csrf

                @include('sms.surveillance.notification.sinan._partials.sinan_form_general_data')

                @include('sms.surveillance.notification.sinan._partials.sinan_form_individual_data')

                @include('sms.surveillance.notification.sinan._partials.sinan_form_address_data')

                <!-- Actions -->
                <div class="flex items-center justify-end pt-6 mt-6 border-t border-gray-100">
                    <x-button.btn-submit value="Criar Usuário"/>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>