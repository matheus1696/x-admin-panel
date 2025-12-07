<nav class="lg:flex-1 space-y-1.5 overflow-hidden pb-2" x-data="{ activeDropdown: null }">
    
    <!-- Dashboard -->
    @can('view-dashboard')
        <x-sidebar.link href="{{ route('dashboard') }}" icon="fa-solid fa-chart-line" title="Dashboard" :active="request()->routeIs('dashboard')"/>
    @endcan

    <!-- Usuários -->
    @canany(['create-users', 'view-users'])
    <x-sidebar.dropdown title="Gerenciamento de Usuários" :active="request()->routeIs('users.*')" icon="fa-solid fa-users">
        @can('create-users')
            <x-sidebar.dropdown-link href="{{ route('users.create') }}" title="Criar Usuário" :active="request()->routeIs('users.create')" />
        @endcan
        @can('view-users')
            <x-sidebar.dropdown-link href="{{ route('users.index') }}" title="Listar Usuários" :active="request()->routeIs('users.index')" />
        @endcan
    </x-sidebar.dropdown>
    @endcanany

    <!-- Auditoria (Somente Admin) -->
    @can('view-logs')
    <x-sidebar.dropdown title="Auditoria" :active="request()->routeIs('admin.logs.*')" icon="fa-solid fa-lock">
            <x-sidebar.dropdown-link href="{{ route('admin.logs.index') }}" title="Logs do Sistema" :active="request()->routeIs('admin.logs.index')" />
    </x-sidebar.dropdown>
    @endcan

    <!-- Perfil -->
    @auth
        <x-sidebar.link href="{{ route('profile.edit') }}" icon="fa-solid fa-user" title="Perfil" :active="request()->routeIs('profile.edit')"/>
    @endauth

</nav>