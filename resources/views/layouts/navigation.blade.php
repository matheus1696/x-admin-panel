<nav class="lg:flex-1 space-y-1.5 overflow-hidden pb-2" x-data="{ activeDropdown: null }">

    <!-- Dashboard -->
    <x-sidebar.main-link
        href="{{ route('dashboard') }}"
        icon="fa-solid fa-chart-line"
        title="Dashboard"
        :active="request()->routeIs('dashboard')"
    />

    <x-sidebar.main-link
        href="{{ route('admin.organization.index') }}"
        title="Organograma"
        icon="fa-solid fa-diagram-project"
        :active="request()->routeIs('admin.organization.index')"
    />

    <!-- Organização -->
    @canany(['organization.view', 'organization.manage', 'workflow.manage'])
        <x-sidebar.main-dropdown
            title="Organização"
            icon="fa-solid fa-sitemap"
            :active="request()->routeIs('admin.organization.config.*') || request()->routeIs('admin.workflow.*')"
        >
            @can('organization.manage')
                <x-sidebar.dropdown-link
                    href="{{ route('admin.organization.config.index') }}"
                    title="Gerenciamento do Organograma"
                    icon="fa-solid fa-gear"
                    :active="request()->routeIs('admin.organization.config.index')"
                />
            @endcan

            @can('workflow.manage')
                <x-sidebar.dropdown
                    title="Gestão de Fluxo de Trabalho"
                    icon="fa-solid fa-diagram-project"
                    :active="request()->routeIs('admin.workflow.*')"
                >
                    <x-sidebar.dropdown-link
                        href="{{ route('admin.workflow.index') }}"
                        title="Processos"
                        :active="request()->routeIs('admin.workflow.index')"
                    />
                </x-sidebar.dropdown>
            @endcan

        </x-sidebar.main-dropdown>
    @endcanany


    <!-- Administração -->
    @canany([
        'admin.establishments.manage',
        'admin.occupations.manage',
        'admin.regions.manage',
        'admin.financial-blocks.manage'
    ])
        <x-sidebar.main-dropdown
            title="Administração"
            icon="fa-solid fa-gear"
            :active="request()->routeIs('admin.establishments.*')
                || request()->routeIs('admin.occupations.*')
                || request()->routeIs('admin.regions.*')
                || request()->routeIs('admin.financial.blocks.*')"
        >

            @can('admin.occupations.manage')
                <x-sidebar.dropdown-link
                    href="{{ route('admin.occupations.index') }}"
                    title="Ocupações"
                    icon="fa-solid fa-briefcase"
                    :active="request()->routeIs('admin.occupations.*')"
                />
            @endcan

            @can('admin.establishments.manage')
                <x-sidebar.dropdown
                    title="Estabelecimentos"
                    icon="fa-solid fa-hospital"
                    :active="request()->routeIs('admin.establishments.*')"
                >
                    <x-sidebar.dropdown-link
                        href="{{ route('admin.establishments.types.index') }}"
                        title="Tipos"
                        :active="request()->routeIs('admin.establishments.types.*')"
                    />

                    <x-sidebar.dropdown-link
                        href="{{ route('admin.establishments.index') }}"
                        title="Lista"
                        :active="request()->routeIs('admin.establishments.index')"
                    />
                </x-sidebar.dropdown>
            @endcan

            @can('admin.regions.manage')
                <x-sidebar.dropdown
                    title="Regiões"
                    icon="fa-solid fa-map"
                    :active="request()->routeIs('admin.regions.*')"
                >
                    <x-sidebar.dropdown-link
                        href="{{ route('admin.regions.countries.index') }}"
                        title="Países"
                    />

                    <x-sidebar.dropdown-link
                        href="{{ route('admin.regions.states.index') }}"
                        title="Estados"
                    />

                    <x-sidebar.dropdown-link
                        href="{{ route('admin.regions.cities.index') }}"
                        title="Cidades"
                    />
                </x-sidebar.dropdown>
            @endcan

            @can('admin.financial-blocks.manage')
                <x-sidebar.dropdown-link
                    href="{{ route('admin.financial.blocks.index') }}"
                    title="Blocos Financeiros"
                    icon="fa-solid fa-coins"
                    :active="request()->routeIs('admin.financial.blocks.*')"
                />
            @endcan

        </x-sidebar.main-dropdown>
    @endcanany

    <!-- Usuários & Acessos -->
    @can('users.view')
        <x-sidebar.main-link href="{{ route('admin.users.index') }}" title="Gerenciamento de Usuários" icon="fa-solid fa-users" :active="request()->routeIs('admin.users.index')"/>
    @endcan

    <!-- Auditoria -->
    @can('audit.logs.view')
        <x-sidebar.main-dropdown
            title="Auditoria"
            icon="fa-solid fa-shield-halved"
            :active="request()->routeIs('admin.audit.*')"
        >
            <x-sidebar.dropdown-link
                href="{{ route('admin.audit.logs.index') }}"
                title="Logs do Sistema"
                icon="fa-solid fa-list-check"
            />
        </x-sidebar.main-dropdown>
    @endcan


    <!-- Perfil -->
    <x-sidebar.main-link
        href="{{ route('profile.edit') }}"
        title="Perfil"
        icon="fa-solid fa-user"
        :active="request()->routeIs('profile.*')"
    />
</nav>
