<div class="space-y-6">

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header title="Tipos de Tarefa" subtitle="Gerencie os tipos de tarefas do sistema" icon="fa-solid fa-diagram-project">
        <x-slot name="button">
            <x-button.btn type="button" wire:click="create" value="Novo Tipo" icon="fa-solid fa-plus"/>
        </x-slot>
    </x-page.header>
       
    <!-- Filter -->
    <x-page.filter title="Filtros">
        <x-slot name="showBasic">

            {{-- Tipo --}}
            <div class="col-span-12 md:col-span-8">
                <x-form.label value="Tipo" />
                <x-form.input wire:model.live.debounce.500ms="filters.type" placeholder="Buscar por tipo..." />
            </div>

            {{-- Status --}}
            <div class="col-span-6 md:col-span-2">
                <x-form.label value="Status" />
                <x-form.select-livewire wire:model.live="filters.status" name="filters.status"
                    :options="[
                        ['value' => 'all', 'label' => 'Todos'],
                        ['value' => 'true', 'label' => 'Ativo'],
                        ['value' => 'false', 'label' => 'Desativado'],
                    ]"
                />
            </div>

            {{-- Itens por página --}}
            <div class="col-span-6 md:col-span-2">
                <x-form.label value="Itens por página" />
                <x-form.select-livewire wire:model.live="filters.perPage" name="filters.perPage"
                    :options="[
                        ['value' => 10, 'label' => '10'],
                        ['value' => 25, 'label' => '25'],
                        ['value' => 50, 'label' => '50'],
                        ['value' => 100, 'label' => '100']
                    ]"
                />
            </div>

        </x-slot>
    </x-page.filter>

    <!-- Table -->
    <x-page.table :pagination="$taskTypes">
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="text-center w-48" value="Título" />
                <x-page.table-th class="hidden lg:table-cell" value="Descrição" />
                <x-page.table-th class="text-center w-28" value="Status" />
                <x-page.table-th class="text-center w-28" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($taskTypes as $taskType)
                <tr>
                    <x-page.table-td :value="$taskType->title" />
                    <x-page.table-td class="hidden lg:table-cell" :value="$taskType->description ?? '-'" />

                    <x-page.table-td class="text-center">
                        <span class="inline-flex items-center gap-1 text-xs font-medium">
                            <span class="w-2 h-2 rounded-full {{ $taskType->status ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $taskType->status ? 'Ativo' : 'Desativado' }}
                        </span>
                    </x-page.table-td>

                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            <x-button.btn-table wire:click="edit({{ $taskType->id }})">
                                <i class="fa-solid fa-pen"></i>
                            </x-button.btn-table>
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>

    <!-- Modal Form -->
    <x-modal :show="$showModal" close="$wire.closeModal()" wire:key="task-type-modal">
        <x-slot name="header">
            <h2 class="text-sm font-semibold text-gray-700 uppercase">
                {{ $mode === 'create'
                    ? 'Cadastrar Tipo de Tarefa'
                    : 'Editar Tipo de Tarefa' }}
            </h2>

            <button wire:click="closeModal"
                class="text-gray-400 hover:text-gray-600">
                ✕
            </button>
        </x-slot>

        <form wire:submit.prevent="{{ $mode === 'create' ? 'store' : 'update' }}"
            class="space-y-4">

            <div>
                <x-form.label value="Tipo" />
                <x-form.input wire:model.defer="title" placeholder="Processo Licitatório" required/>
                <x-form.error :messages="$errors->get('title')" />
            </div>

            <div>
                <x-form.label value="Descrição" />
                <x-form.input wire:model.defer="description" placeholder="Descrição opcional do tipo de tarefa" rows="4"/>
                <x-form.error :messages="$errors->get('description')" />
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <x-button.btn type="submit" value="{{ $mode === 'create' ? 'Salvar' : 'Atualizar' }}" />
            </div>
        </form>
    </x-modal>
</div>