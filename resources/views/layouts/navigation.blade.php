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
            title="Gestão de Projetos"
            :active="request()->routeIs('tasks.index')"
        />

        @canany(['process.view', 'process.dashboard.view'])
            <x-sidebar.main-dropdown
                title="Gestão de Processos"
                icon="fa-solid fa-folder-tree"
                :active="request()->routeIs('process.*')"
            >
                @can('process.view')
                    <x-sidebar.dropdown-link
                        href="{{ route('process.index') }}"
                        title="Lista de Processos"
                        icon="fa-solid fa-list"
                        :active="request()->routeIs('process.index') || request()->routeIs('process.show')"
                    />
                @endcan

                @can('process.dashboard.view')
                    <x-sidebar.dropdown-link
                        href="{{ route('process.dashboard') }}"
                        title="Dashboard"
                        icon="fa-solid fa-chart-column"
                        :active="request()->routeIs('process.dashboard')"
                    />
                @endcan
            </x-sidebar.main-dropdown>
        @endcanany

        @canany(['time_clock.register', 'time_clock.view_any', 'time_clock.reports.view', 'time_clock.locations.manage'])
            <x-sidebar.main-dropdown
                title="Controle de Ponto"
                icon="fa-solid fa-clock"
                :active="request()->routeIs('time-clock.*')"
            >
                @can('time_clock.register')
                    <x-sidebar.dropdown-link
                        href="{{ route('time-clock.register') }}"
                        title="Registrar Ponto"
                        icon="fa-solid fa-camera"
                        :active="request()->routeIs('time-clock.register')"
                    />
                @endcan

                @can('time_clock.view_any')
                    <x-sidebar.dropdown-link
                        href="{{ route('time-clock.entries.index') }}"
                        title="Registros"
                        icon="fa-solid fa-table-list"
                        :active="request()->routeIs('time-clock.entries.*')"
                    />
                @endcan

                @can('time_clock.reports.view')
                    <x-sidebar.dropdown-link
                        href="{{ route('time-clock.reports.index') }}"
                        title="Relatorios"
                        icon="fa-solid fa-chart-column"
                        :active="request()->routeIs('time-clock.reports.index')"
                    />
                @endcan

                @can('time_clock.locations.manage')
                    <x-sidebar.dropdown-link
                        href="{{ route('time-clock.locations.index') }}"
                        title="Locais"
                        icon="fa-solid fa-location-dot"
                        :active="request()->routeIs('time-clock.locations.index')"
                    />
                @endcan
            </x-sidebar.main-dropdown>
        @endcanany
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

    <!-- Controle de Ativos -->
    @canany(['assets.view','assets.invoices.manage','assets.transfer','assets.audit','assets.state.change','assets.return','assets.reports.view'])
        <x-sidebar.main-dropdown
            title="Controle de Ativos"
            icon="fa-solid fa-boxes-stacked"
            :active="request()->routeIs('assets.*')"
        >
            @canany(['assets.view','assets.invoices.manage'])
                <x-sidebar.dropdown
                    title="Estoque de Ativos"
                    icon="fa-solid fa-boxes-stacked"
                    :active="request()->routeIs('assets.stock.index') || request()->routeIs('assets.invoices.*') || request()->routeIs('assets.release-orders.*')"
                >
                    @can('assets.view')
                        <x-sidebar.dropdown-link
                            href="{{ route('assets.stock.index') }}"
                            title="Itens em Estoque"
                            :active="request()->routeIs('assets.stock.index')"
                        />
                    @endcan

                    @can('assets.invoices.manage')
                        <x-sidebar.dropdown-link
                            href="{{ route('assets.invoices.index') }}"
                            title="Entrada de Ativos"
                            :active="request()->routeIs('assets.invoices.*')"
                        />
                    @endcan

                    @can('assets.transfer')
                        <x-sidebar.dropdown-link
                            href="{{ route('assets.release-orders.index') }}"
                            title="Liberacao de Ativos"
                            :active="request()->routeIs('assets.release-orders.*')"
                        />
                    @endcan
                </x-sidebar.dropdown>
            @endcanany

            @canany(['assets.audit', 'assets.view'])
                <x-sidebar.dropdown
                    title="Auditoria"
                    icon="fa-solid fa-clipboard-check"
                    :active="request()->routeIs('assets.index') || request()->routeIs('assets.show') || request()->routeIs('assets.audits.campaigns.*') || request()->routeIs('assets.audit-mobile')"
                >
                    @can('assets.view')
                        <x-sidebar.dropdown-link
                            href="{{ route('assets.index') }}"
                            title="Ativos Operacionais"
                            :active="request()->routeIs('assets.index') || request()->routeIs('assets.show')"
                        />
                    @endcan

                    @can('assets.audit')
                        <x-sidebar.dropdown-link
                            href="{{ route('assets.audits.campaigns.index') }}"
                            title="Campanhas de Auditoria"
                            :active="request()->routeIs('assets.audits.campaigns.*')"
                        />

                        <x-sidebar.dropdown-link
                            href="{{ route('assets.audit-mobile') }}"
                            title="Auditoria Individual"
                            :active="request()->routeIs('assets.audit-mobile')"
                        />
                    @endcan
                </x-sidebar.dropdown>
            @endcanany

            @can('assets.reports.view')
                <x-sidebar.dropdown
                    title="Relatórios"
                    icon="fa-solid fa-chart-column"
                    :active="request()->routeIs('assets.reports.*')"
                >
                    <x-sidebar.dropdown-link href="{{ route('assets.reports.assets-by-unit') }}" title="Ativos por Unidade" />
                    <x-sidebar.dropdown-link href="{{ route('assets.reports.assets-by-state') }}" title="Ativos por Estado" />
                    <x-sidebar.dropdown-link href="{{ route('assets.reports.transfers-by-period') }}" title="Transferências" />
                    <x-sidebar.dropdown-link href="{{ route('assets.reports.audits-by-period') }}" title="Auditorias" />
                    <x-sidebar.dropdown-link href="{{ route('assets.reports.purchases-by-period') }}" title="Compras" />
                </x-sidebar.dropdown>
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

            @canany(['administration.manage.suppliers','administration.manage.products','administration.manage.product-types','administration.manage.product-measure-units'])
                <x-sidebar.dropdown
                    title="Produtos"
                    icon="fa-solid fa-box-open"
                    :active="request()->routeIs('administration.manage.suppliers') || request()->routeIs('administration.manage.products') || request()->routeIs('administration.manage.product-types') || request()->routeIs('administration.manage.product-measure-units')"
                >
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
                            title="Lista de Produtos"
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
                </x-sidebar.dropdown>
            @endcanany

        </x-sidebar.main-dropdown>
    @endcanany

    <!-- Configuração do Sistema -->
    @canany([ 'configuration.manage.establishments', 'configuration.manage.occupations', 'configuration.manage.regions', 'configuration.manage.financial-blocks' ])
        <x-sidebar.main-dropdown title="Configuração do Sistema" icon="fa-solid fa-sliders" :active="request()->routeIs('configuration.*')" >

            @can('configuration.manage.occupations')
                <x-sidebar.dropdown-link href="{{ route('configuration.manage.occupations') }}" title="Ocupações (CBO)" icon="fa-solid fa-briefcase" />
            @endcan

            @can('configuration.manage.establishments')
                <x-sidebar.dropdown title="Estabelecimentos" icon="fa-solid fa-hospital" :active="request()->routeIs('configuration.manage.establishments.*')" >
                    <x-sidebar.dropdown-link href="{{ route('configuration.manage.establishments.types') }}" title="Tipos" />
                    <x-sidebar.dropdown-link href="{{ route('configuration.manage.establishments.view') }}" title="Lista" />
                </x-sidebar.dropdown>
            @endcan

            @can('configuration.manage.regions')
                <x-sidebar.dropdown title="Regiões" icon="fa-solid fa-map" :active="request()->routeIs('configuration.manage.regions.*')" >
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

    <!-- Logs de Auditoria -->
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
