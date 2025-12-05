<x-app-layout>
    <div class="w-full md:w-1/2 mx-auto space-y-6 mt-6">
        
        <x-page.header icon="fa-solid fa-users" title="Criação de Usuários" subtitle="Realize a criação do novo usuários do sistema">
            <x-slot name="button">
                @can('create-users')
                    <x-button.btn-link href="{{ route('users.index') }}" value="Voltar para Lista" icon="fa-solid fa-rotate-left" />
                @endcan
            </x-slot>
        </x-page.header>

        <div>
            <!-- Card do Formulário -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Formulário -->
                <form action="{{ route('users.store') }}" method="POST" class="p-6">
                    @csrf

                    @include('admin.manage.users._partials.user_form')

                    <!-- Actions -->
                    <div class="flex items-center justify-end pt-6 mt-6 border-t border-gray-100">
                        <x-button.btn-submit value="Criar Usuário"/>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>