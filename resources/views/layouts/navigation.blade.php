<nav class="lg:flex-1 space-y-1.5 overflow-hidden pb-2" x-data="{ activeDropdown: null }">

    <!-- Dashboard -->
    @can('dashboard-view')
        <x-sidebar.main-link href="{{ route('dashboard') }}" icon="fa-solid fa-chart-line" title="Dashboard" :active="request()->routeIs('dashboard')" />
    @endcan

    <!-- Usuários -->
    @canany([ 'user-view', 'user-create', 'user-edit', 'user-permission' ])
        <x-sidebar.main-dropdown title="Usuários" icon="fa-solid fa-users" :active="request()->routeIs('users.*')" >
            @can('user-create')
                <x-sidebar.dropdown-link href="{{ route('users.create') }}" title="Criar Usuário" icon="fa-solid fa-user-plus" :active="request()->routeIs('users.create')" />
            @endcan

            @can('user-view')
                <x-sidebar.dropdown-link href="{{ route('users.index') }}" title="Listar Usuários" icon="fa-solid fa-list" :active="request()->routeIs('users.index')" />
            @endcan
        </x-sidebar.main-dropdown>
    @endcanany

    <!-- Configurações do Sistema -->
    @canany(['occupation-view', 'region-view'])
        <x-sidebar.main-dropdown title="Configurações" icon="fa-solid fa-gear" :active="request()->routeIs('config.*')">
            @can('occupation-view')
                <x-sidebar.dropdown-link href="{{ route('config.occupations.index') }}" title="Ocupações" icon="fa-solid fa-briefcase" :active="request()->routeIs('config.occupations.*')" />
            @endcan

            <!-- Estabelecimentos -->
            @canany(['establishment-view', 'establishment-type-view'])
                <x-sidebar.dropdown title="Estabelecimentos" icon="fa-solid fa-hospital" :active="request()->routeIs('establishments.*')" >
                    @can('establishment-type-view')
                        <x-sidebar.dropdown-link href="{{ route('establishments.types.index') }}" title="Tipos de Estabelecimento" :active="request()->routeIs('establishments.types.*')" />
                    @endcan

                    @can('establishment-view')
                        <x-sidebar.dropdown-link href="{{ route('establishments.index') }}" title="Estabelecimentos" :active="request()->routeIs('establishments.index')" />
                    @endcan
                </x-sidebar.dropdown>
            @endcanany
            
            @can('financial-block-view')
                <x-sidebar.dropdown-link href="{{ route('financial.blocks.index') }}"  title="Blocos Financeiros" icon="fa-solid fa-coins" :active="request()->routeIs('financial-blocks.*')" />
            @endcan

            @can('region-view')
                <x-sidebar.dropdown title="Regiões" icon="fa-solid fa-map" :active="request()->routeIs('config.regions.*')" >
                    <x-sidebar.dropdown-link href="{{ route('config.regions.cities.index') }}" title="Cidades" :active="request()->routeIs('config.regions.cities.*')" />

                    <x-sidebar.dropdown-link href="{{ route('config.regions.states.index') }}" title="Estados" :active="request()->routeIs('config.regions.states.*')" />

                    <x-sidebar.dropdown-link href="{{ route('config.regions.countries.index') }}" title="Países" :active="request()->routeIs('config.regions.countries.*')" />
                </x-sidebar.dropdown>
            @endcan
        </x-sidebar.main-dropdown>
    @endcanany

    <!-- Auditoria -->
    @can('log-view')
        <x-sidebar.main-dropdown title="Auditoria" icon="fa-solid fa-shield-halved" :active="request()->routeIs('audit.*')" >
            <x-sidebar.dropdown-link href="{{ route('audit.logs.index') }}" title="Logs do Sistema" icon="fa-solid fa-list-check" :active="request()->routeIs('audit.logs.index')" />
        </x-sidebar.main-dropdown>
    @endcan

    <!-- Perfil -->
    @auth
        <x-sidebar.main-link href="{{ route('profile.edit') }}" title="Perfil" icon="fa-solid fa-user" :active="request()->routeIs('profile.edit')" />
    @endauth

</nav>
 