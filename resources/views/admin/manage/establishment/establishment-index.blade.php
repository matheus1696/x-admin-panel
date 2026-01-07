<x-app-layout>
    <x-page.header icon="fa-solid fa-layer-group" title="Estabelecimento" subtitle="Gerencie os estabelecimento">
        <x-slot name="button">
            <x-button.btn-link href="{{ route('establishments.create') }}" value="Novo Estabelecimento" icon="fa-solid fa-plus" />
        </x-slot>
    </x-page.header>

    <div class="py-6 w-full">
        <livewire:admin.manage.establishment.establishment-table />
    </div>
</x-app-layout>
