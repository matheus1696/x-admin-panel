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

    <!-- Relatórios -->
    @can('view-dashboard')
        <x-sidebar.link href="{{ route('reports.index') }}" icon="fa-solid fa-chart-bar" title="Relatórios" />
    @endcan

    <!-- Configurações -->
    @can('view-dashboard')
        <x-sidebar.dropdown title="Configurações" :active="request()->routeIs('settings.*') || request()->routeIs('profile.*') || request()->routeIs('roles.*')" icon="fa-solid fa-sliders" >

            <!-- Perfil do Usuário -->
            <x-sidebar.dropdown-link href="{{ route('profile.edit') }}" title="Meu Perfil" :active="request()->routeIs('profile.edit')" />
        
            <!-- Configurações do Sistema -->
            @can('view-dashboard')
                <x-sidebar.dropdown-link href="{{ route('settings.general') }}" title="Configurações Gerais" :active="request()->routeIs('settings.general')" />
            @endcan

            <!-- Gerenciar Roles e Permissões -->
            @can('view-dashboard')
            <x-sidebar.dropdown-link href="{{ route('roles.index') }}" title="Gerenciar Permissões" :active="request()->routeIs('roles.*')" />
            @endcan
    </x-sidebar.dropdown>
    @endcan

    <!-- Administração (Somente Admin) -->
    @role('super-admin')
    <x-sidebar.dropdown 
        title="Administração" :active="request()->routeIs('admin.*')" icon="fa-solid fa-lock">
            <x-sidebar.dropdown-link href="{{ route('admin.system-logs') }}" title="Logs do Sistema" :active="request()->routeIs('admin.system-logs')" />
        
            <x-sidebar.dropdown-link href="{{ route('admin.backup') }}" title="Backup" :active="request()->routeIs('admin.backup')" />
    </x-sidebar.dropdown>
    @endrole

</nav>