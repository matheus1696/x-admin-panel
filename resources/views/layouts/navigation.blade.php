<nav class="lg:flex-1 space-y-1.5 overflow-hidden pb-2" x-data="{ activeDropdown: null }">

    <!-- Dashboard -->
    @auth
        <x-sidebar.main-link href="{{ route('dashboard') }}" icon="fa-solid fa-chart-line" title="Dashboard" :active="request()->routeIs('dashboard')" />
    @endauth

    <x-sidebar.main-link href="{{ route('contacts.index') }}" icon="fa-solid fa-address-book" title="Contatos" :active="request()->routeIs('contacts.index')" />

    <!-- Organograma Dashboard -->
    @auth
        <x-sidebar.main-link href="{{ route('organization.chart.dashboard.index') }}" title="Organograma" icon="fa-solid fa-diagram-project" :active="request()->routeIs('organization.chart.dashboard.index')" />
    @endauth

    <!-- Organização -->
    @canany(['organization.chart.config.manage','organization.workflow.manage',])
        <x-sidebar.main-dropdown title="Organização" icon="fa-solid fa-sitemap" :active="request()->routeIs('organization.chart.config.*') || request()->routeIs('organization.workflow.*')" >
            @can('organization.chart.config.manage')
                <x-sidebar.dropdown-link href="{{ route('organization.chart.config.index') }}" title="Gerenciamento do Organograma" icon="fa-solid fa-gear" :active="request()->routeIs('organization.chart.config.index')" />
            @endcan

            @can('organization.workflow.manage')
                <x-sidebar.dropdown title="Gestão de Fluxo de Trabalho" icon="fa-solid fa-diagram-project" :active="request()->routeIs('organization.workflow.*')" >
                    <x-sidebar.dropdown-link href="{{ route('organization.workflow.index') }}" title="Processos"  :active="request()->routeIs('organization.workflow.index')" />
                </x-sidebar.dropdown>
            @endcan
        </x-sidebar.main-dropdown>
    @endcanany


    <!-- Configuração -->
    @canany(['config.establishments.manage','config.occupations.manage','config.regions.manage','config.financial-blocks.manage'])
        <x-sidebar.main-dropdown title="Administração" icon="fa-solid fa-gear" :active="request()->routeIs('config.establishments.*') || request()->routeIs('config.occupations.*') || request()->routeIs('config.regions.*') || request()->routeIs('config.financial.blocks.*')" >

            <!-- Configuração de Occupações -->
            @can('config.occupations.manage')
                <x-sidebar.dropdown-link href="{{ route('config.occupations.index') }}" title="Ocupações" icon="fa-solid fa-briefcase" :active="request()->routeIs('config.occupations.index')" />
            @endcan

            <!-- Configuração do Estabelecimento -->
            @can('config.establishments.manage')
                <x-sidebar.dropdown title="Estabelecimentos" icon="fa-solid fa-hospital" :active="request()->routeIs('config.establishments.*')" >
                    <x-sidebar.dropdown-link href="{{ route('config.establishments.types.index') }}" title="Tipos" :active="request()->routeIs('config.establishments.types.*')" />
                    <x-sidebar.dropdown-link href="{{ route('config.establishments.index') }}" title="Lista" :active="request()->routeIs('config.establishments.index')" />
                </x-sidebar.dropdown>
            @endcan

            <!-- Configuração do Regiões -->
            @can('config.regions.manage')
                <x-sidebar.dropdown title="Regiões" icon="fa-solid fa-map" :active="request()->routeIs('config.regions.*')" >
                    <x-sidebar.dropdown-link href="{{ route('config.regions.countries.index') }}" title="Países" />
                    <x-sidebar.dropdown-link href="{{ route('config.regions.states.index') }}" title="Estados" />
                    <x-sidebar.dropdown-link href="{{ route('config.regions.cities.index') }}" title="Cidades" />
                </x-sidebar.dropdown>
            @endcan

            <!-- Configuração de Bloco Financeiro -->
            @can('config.financial-blocks.manage')
                <x-sidebar.dropdown-link href="{{ route('config.financial.blocks.index') }}" title="Blocos Financeiros" icon="fa-solid fa-coins" :active="request()->routeIs('config.financial.blocks.index')" />
            @endcan

        </x-sidebar.main-dropdown>
    @endcanany

    <!-- Usuários & Acessos -->
    @can('users.view')
        <x-sidebar.main-link href="{{ route('admin.users.index') }}" title="Gerenciamento de Usuários" icon="fa-solid fa-users" :active="request()->routeIs('admin.users.index')"/>
    @endcan

    <!-- Auditoria -->
    @can('audit.logs.view')
        <x-sidebar.main-dropdown title="Auditoria" icon="fa-solid fa-shield-halved" :active="request()->routeIs('admin.audit.*')" >
            <x-sidebar.dropdown-link href="{{ route('audit.logs.index') }}" title="Logs do Sistema" icon="fa-solid fa-list-check" />
        </x-sidebar.main-dropdown>
    @endcan

    <!-- Perfil -->
    @auth
        <x-sidebar.main-link
            href="{{ route('profile.edit') }}"
            title="Perfil"
            icon="fa-solid fa-user"
            :active="request()->routeIs('profile.*')"
        />        
    @endauth
    
</nav>