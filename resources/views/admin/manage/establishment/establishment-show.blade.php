<x-app-layout>
    <x-page.header icon="fa-solid fa-layer-group" title="Estabelecimento" subtitle="Unidade {{ $establishment->title }}">
        <x-slot name="button">
            <x-button.btn-link href="{{ route('establishments.index') }}" value="Voltar" icon="fa-solid fa-reply" class="bg-gray-600 hover:bg-gray-700"/>
        </x-slot>
    </x-page.header>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mt-5">
        <!-- Header do Card -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                Dados Principais
            </h2>

            <a href="{{ route('establishments.edit', $establishment) }}" class="group py-1 px-2.5 rounded-lg text-gray-400 hover:text-green-700 hover:bg-green-50 transition-all duration-200" title="Editar dados do estabelecimento"> 
                <i class="fa-solid fa-pen-to-square text-sm"></i>
            </a>
        </div>

        <!-- Conteúdo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 p-6 text-sm">

            <!-- Código -->
            <div>
                <p class="text-xs text-gray-500">Código</p>
                <p class="font-semibold text-gray-900">{{ $establishment->code ?? '-' }}</p>
            </div>

            <!-- Nome -->
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-500">Nome do Estabelecimento</p>
                <p class="font-semibold text-gray-900">{{ $establishment->title }}</p>
            </div>

            <!-- Nome Fantasia -->
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-500">Nome Fantasia</p>
                <p class="font-medium text-gray-900">{{ $establishment->surname ?? '-' }}</p>
            </div>

            <!-- Tipo -->
            <div>
                <p class="text-xs text-gray-500">Tipo de Estabelecimento</p>
                <p class="font-medium text-gray-900">{{ $establishment->typeEstablishment->title ?? '-' }}</p>
            </div>

            <!-- Bloco Financeiro -->
            <div>
                <p class="text-xs text-gray-500">Bloco de Financiamento</p>
                <p class="font-medium text-gray-900">{{ $establishment->financialBlock->title ?? '-' }}</p>
            </div>

            <!-- Endereço -->
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-500">Endereço</p>
                <p class="font-medium text-gray-900">{{ $establishment->address }},{{ $establishment->number ?? 's/n' }}, {{ $establishment->district ?? '-' }}, {{ $establishment->RegionCity->title ?? '-' }} / {{ $establishment->RegionCity->RegionState->acronym ?? '-' }}</p>
            </div>

            <!-- Coordenadas -->
            <div>
                <p class="text-xs text-gray-500">Coordenadas</p>
                <p class="font-medium text-gray-900">
                    {{ $establishment->latitude ?? '-' }},
                    {{ $establishment->longitude ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    <div class="py-6 w-full">
        <livewire:admin.manage.establishment.department-table :establishmentId="$establishment->id"/>
    </div>

</x-app-layout>
