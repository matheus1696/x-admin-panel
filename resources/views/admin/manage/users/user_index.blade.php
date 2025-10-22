<x-app-layout>

    <x-page.header icon="fa-solid fa-users" title="Gerenciar Usuários" subtitle="Gerencie os usuários do sistema">
        <x-slot name="button">
            @can('create-users')
                <x-button.link-primary href="{{ route('users.create') }}">
                    <i class="fa-solid fa-plus"></i>
                    Novo Usuário
                </x-button.link-primary>
            @endcan
        </x-slot>
    </x-page.header>

    <div class="py-6 w-full overflow-x-auto">

        <livewire:admin.manage.user-table />

    </div>
</x-app-layout>
