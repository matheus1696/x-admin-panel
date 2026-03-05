<nav class="lg:flex-1 space-y-1.5 overflow-hidden pb-2" x-data="{ activeDropdown: null }">

    <!-- Dashboard -->
    @auth
        <x-sidebar.main-link
            href="{{ route('dashboard') }}"
            icon="fa-solid fa-chart-line"
            title="Dashboard"
            :active="request()->routeIs('dashboard')"
        />
        
        <x-sidebar.main-link
            href="{{ route('chart.index') }}"
            title="Organograma"
            icon="fa-solid fa-diagram-project"
            :active="request()->routeIs('chart.index')"
        />
        
        <x-sidebar.main-link
            href="{{ route('tasks.index') }}"
            icon="fa-solid fa-list-check"
            title="Atividades"
            :active="request()->routeIs('tasks.index')"
        />
    @endauth

    <!-- Contatos (Acesso Geral) -->
    <x-sidebar.main-link
        href="{{ route('public.contacts.index') }}"
        icon="fa-solid fa-address-book"
        title="Contatos"
        :active="request()->routeIs('public.contacts.index')"
    />


    <!-- Organização -->
    @canany(['organization.manage.chart','organization.manage.workflow'])
        <x-sidebar.main-dropdown
            title="Organização"
            icon="fa-solid fa-sitemap"
            :active="request()->routeIs('organization.manage.*')"
        >

            @can('organization.manage.chart')
                <x-sidebar.dropdown-link
                    href="{{ route('organization.manage.chart') }}"
                    title="Setores"
                    icon="fa-solid fa-gear"
                    :active="request()->routeIs('organization.manage.chart')"
                />
            @endcan

            @can('organization.manage.workflow')
                <x-sidebar.dropdown-link
                    href="{{ route('organization.manage.workflow') }}"
                    title="Fluxo de Trabalho"
                    icon="fa-solid fa-diagram-project"
                    :active="request()->routeIs('organization.manage.workflow')"
                />
            @endcan

        </x-sidebar.main-dropdown>
    @endcanany


    <!-- Administração -->
    @canany(['administration.manage.users','administration.manage.task','administration.manage.suppliers','administration.manage.products','administration.manage.product-types','administration.manage.product-measure-units'])
        <x-sidebar.main-dropdown
            title="Administração"
            icon="fa-solid fa-gear"
            :active="request()->routeIs('administration.manage.*')"
        >

            @can('administration.manage.users')
                <x-sidebar.dropdown-link
                    href="{{ route('administration.manage.users') }}"
                    title="Usuários & Acessos"
                    icon="fa-solid fa-users"
                    :active="request()->routeIs('administration.manage.users')"
                />
            @endcan

            @can('administration.manage.suppliers')
                <x-sidebar.dropdown-link
                    href="{{ route('administration.manage.suppliers') }}"
                    title="Fornecedores"
                    icon="fa-solid fa-truck-field"
                    :active="request()->routeIs('administration.manage.suppliers')"
                />
            @endcan

            @can('administration.manage.products')
                <x-sidebar.dropdown-link
                    href="{{ route('administration.manage.products') }}"
                    title="Produtos"
                    icon="fa-solid fa-box-open"
                    :active="request()->routeIs('administration.manage.products')"
                />
            @endcan

            @can('administration.manage.product-types')
                <x-sidebar.dropdown-link
                    href="{{ route('administration.manage.product-types') }}"
                    title="Tipos de Produto"
                    icon="fa-solid fa-tags"
                    :active="request()->routeIs('administration.manage.product-types')"
                />
            @endcan

            @can('administration.manage.product-measure-units')
                <x-sidebar.dropdown-link
                    href="{{ route('administration.manage.product-measure-units') }}"
                    title="Unidades de Medida"
                    icon="fa-solid fa-ruler-combined"
                    :active="request()->routeIs('administration.manage.product-measure-units')"
                />
            @endcan

        </x-sidebar.main-dropdown>
    @endcanany


    <!-- Configuração do Sistema -->
    @canany([ 'configuration.manage.establishments', 'configuration.manage.occupations', 'configuration.manage.regions', 'configuration.manage.financial-blocks' ])
        <x-sidebar.main-dropdown title="Configuração do Sistema" icon="fa-solid fa-sliders" :active="request()->routeIs('configuration.*')" >

            @can('configuration.manage.occupations')
                <x-sidebar.dropdown-link href="{{ route('configuration.manage.occupations') }}" title="Ocupações (CBO)" icon="fa-solid fa-briefcase" />
            @endcan

            @can('configuration.manage.establishments')
                <x-sidebar.dropdown title="Estabelecimentos" icon="fa-solid fa-hospital" :active="request()->routeIs('configuration.establishments.*')" >
                    <x-sidebar.dropdown-link href="{{ route('configuration.manage.establishments.types') }}" title="Tipos" />
                    <x-sidebar.dropdown-link href="{{ route('configuration.manage.establishments.view') }}" title="Lista" />
                </x-sidebar.dropdown>
            @endcan

            @can('configuration.manage.regions')
                <x-sidebar.dropdown title="Regiões" icon="fa-solid fa-map" >
                    <x-sidebar.dropdown-link href="{{ route('configuration.manage.regions.countries') }}" title="Países" />
                    <x-sidebar.dropdown-link href="{{ route('configuration.manage.regions.states') }}" title="Estados" />
                    <x-sidebar.dropdown-link href="{{ route('configuration.manage.regions.cities') }}" title="Cidades" />
                </x-sidebar.dropdown>
            @endcan

            @can('configuration.manage.financial-blocks')
                <x-sidebar.dropdown-link
                    href="{{ route('configuration.manage.financial.blocks') }}"
                    title="Blocos Financeiros"
                    icon="fa-solid fa-coins"
                />
            @endcan

        </x-sidebar.main-dropdown>
    @endcanany

    @canany(['assets.view','assets.invoices.manage','assets.stock.receive','assets.transfer','assets.audit','assets.state.change','assets.return','assets.reports.view'])
        <x-sidebar.main-dropdown
            title="Controle de Patrimônio"
            icon="fa-solid fa-boxes-stacked"
            :active="request()->routeIs('assets.*')"
        >
            @can('assets.view')
                <x-sidebar.dropdown-link
                    href="{{ route('assets.index') }}"
                    title="Lista de Ativos"
                    icon="fa-solid fa-box-archive"
                    :active="request()->routeIs('assets.index') || request()->routeIs('assets.show')"
                />
            @endcan

            @can('assets.invoices.manage')
                <x-sidebar.dropdown-link
                    href="{{ route('assets.invoices.index') }}"
                    title="Notas Fiscais"
                    icon="fa-solid fa-file-invoice-dollar"
                    :active="request()->routeIs('assets.invoices.*')"
                />
            @endcan

            @can('assets.audit')
                <x-sidebar.dropdown-link
                    href="{{ route('assets.audit-mobile') }}"
                    title="Auditoria Mobile"
                    icon="fa-solid fa-camera"
                    :active="request()->routeIs('assets.audit-mobile')"
                />
            @endcan

            @can('assets.reports.view')
                <x-sidebar.dropdown
                    title="Relatorios"
                    icon="fa-solid fa-chart-column"
                    :active="request()->routeIs('assets.reports.*')"
                >
                    <x-sidebar.dropdown-link href="{{ route('assets.reports.assets-by-unit') }}" title="Ativos por Unidade" />
                    <x-sidebar.dropdown-link href="{{ route('assets.reports.assets-by-state') }}" title="Ativos por Estado" />
                    <x-sidebar.dropdown-link href="{{ route('assets.reports.transfers-by-period') }}" title="Transferencias" />
                    <x-sidebar.dropdown-link href="{{ route('assets.reports.audits-by-period') }}" title="Auditorias" />
                    <x-sidebar.dropdown-link href="{{ route('assets.reports.purchases-by-period') }}" title="Compras" />
                </x-sidebar.dropdown>
            @endcan
        </x-sidebar.main-dropdown>
    @endcanany

    @can('audit.logs.view')
        <x-sidebar.main-link
            href="{{ route('audit.logs.view') }}"
            title="Auditoria"
            icon="fa-solid fa-shield-halved"
            :active="request()->routeIs('audit.logs.view')"
        />
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
