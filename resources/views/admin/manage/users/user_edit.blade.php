<x-app-layout>
    <x-page.header 
        icon="fa-solid fa-users" 
        title="Edição de Usuário" 
        subtitle="Atualize os dados do usuário do sistema"
    >
        <x-slot name="button">
            @can('create-users')
                <x-button.link-primary 
                    href="{{ route('users.index') }}" 
                    color="gray"
                    class="flex items-center gap-2"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar para Lista
                </x-button.link-primary>
            @endcan
        </x-slot>
    </x-page.header>

    <div class="py-6">
        <!-- Card do Formulário -->
        <div class="bg-white rounded-xl shadow-sm border border-blue-200 overflow-hidden">
            <!-- Formulário -->
            <form action="{{ route('users.update', $user) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                @include('admin.manage.users._partials.user_form')

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-100">
                    <div class="text-sm text-gray-500 flex items-center gap-2">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>Preencha todos os campos obrigatórios</span>
                    </div>
                    
                    <div>
                        <x-button.btn-submit value="Salvar Alterações"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>