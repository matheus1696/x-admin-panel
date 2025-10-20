<x-sidebar.link href="{{ route('dashboard') }}" icon="fa-solid fa-chart-line" title="Dashboard" :active="request()->routeIs('dashboard')" />

<x-sidebar.dropdown title="Dropdown" :active="request()->routeIs('configurations.*')" icon="fa-solid fa-sliders">
    <x-sidebar.dropdown-link href="{{ route('dashboard') }}" title="Alterar Dados" :active="request()->routeIs('dashboard')" />
    <x-sidebar.dropdown-link href="{{ route('dashboard') }}" title="Alterar Dados" :active="request()->routeIs('dashboard')" />
    <x-sidebar.dropdown-link href="{{ route('dashboard') }}" title="Alterar Dados" :active="request()->routeIs('dashboard')" />
    <x-sidebar.dropdown-link href="{{ route('dashboard') }}" title="Alterar Dados" :active="request()->routeIs('dashboard')" />
    <x-sidebar.dropdown-link href="{{ route('dashboard') }}" title="Alterar Dados" :active="request()->routeIs('dashboard')" />
</x-sidebar.dropdown>

<x-sidebar.dropdown title="Configurações" :active="request()->routeIs('profile.*')" icon="fa-solid fa-sliders">
    <x-sidebar.dropdown-link href="{{ route('profile.edit') }}" title="Alterar Dados" :active="request()->routeIs('profile.edit')" />
</x-sidebar.dropdown>