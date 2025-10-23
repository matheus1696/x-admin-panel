<x-app-layout>

    <x-page.header icon="fa-solid fa-users" title="Gerenciar Usuários" subtitle="Gerencie os usuários do sistema">
        <x-slot name="button">
            @can('create-users')
                <x-button.btn-link href="{{ route('users.create') }}" value="Novo Usuário" icon="fa-solid fa-plus" />
            @endcan
        </x-slot>
    </x-page.header>

    <div class="py-6 w-full overflow-x-auto">

        <livewire:admin.manage.user-table />

    </div>
</x-app-layout>
