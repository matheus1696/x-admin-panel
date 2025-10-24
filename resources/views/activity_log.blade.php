<x-app-layout>
    <x-page.header icon="fa-solid fa-users" title="Logs do Sistema" subtitle="Monitore todos os acessos do sistema" />

    <div class="py-6 w-full overflow-x-auto">
        <livewire:activity-log-table />
    </div>
</x-app-layout>
