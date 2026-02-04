<div x-data="{ openCreateTask: false }" >

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header title="Cronograma de Atividades" subtitle="Visualize todas as atividades e os andamentos" icon="fa-solid fa-list-check" >
        <x-slot name="button">
            <x-button text="Nova Tarefa" icon="fa-solid fa-plus" @click="openCreateTask = !openCreateTask" />
        </x-slot>
    </x-page.header>
       
    <!-- Filtros -->
    <x-page.filter title="Filtros">
        <x-slot name="showBasic">

            {{-- Título da Tarefa --}}
            <div class="col-span-12 md:col-span-8">
                <x-form.label value="Título da Tarefa" />
                <x-form.input wire:model.defer="filters.title" placeholder="Buscar por título..." />
            </div>

            {{-- Status --}}
            <div class="col-span-6 md:col-span-2">
                <x-form.label value="Status da Tarefa" />
                <x-form.select-livewire 
                    wire:model.defer="filters.workflow_run_status_id" 
                    name="filters.workflow_run_status_id" 
                    :collection="$taskStatuses" 
                    value-field="id" 
                    label-field="title" 
                />
            </div>

            {{-- Itens por página --}}
            <div class="col-span-6 md:col-span-2">
                <x-form.label value="Itens por página" />
                <x-form.select-livewire wire:model.defer="filters.perPage" name="filters.perPage"
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

    <!-- Tarefas -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        
        <!-- HEADER DA GRID -->
        <div class="grid grid-cols-12 gap-4 px-5 py-2 bg-green-50/80 border-b border-gray-200 text-xs font-semibold text-gray-600">
            <div class="col-span-4">Título</div>
            <div class="col-span-2 text-center">Responsável</div>
            <div class="col-span-1 text-center">Categoria</div>
            <div class="col-span-1 text-center">Prioridade</div>
            <div class="col-span-1 text-center">Status</div>
            <div class="col-span-1 text-center">Início</div>
            <div class="col-span-1 text-center">Prazo</div>
            <div class="col-span-1 text-center">Finalizado</div>
        </div>

        <div x-show="openCreateTask" x-collapse class="border-t bg-white px-4 py-3" >
            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.task._partials.task-form')
            </form>
        </div>

        <div x-data="{ openAsideTask: false, openAsideTaskStep: false, activeItem: null, activeTaskStepItem: null }">
            <div>
                @forelse ($tasks as $task)
                    <livewire:task.task-list :taskId="$task->id" :key="$task->id" />
                @empty
                    <!-- Estado Vazio -->
                    <div class="flex flex-col items-center justify-center py-12 px-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-list-check text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-base font-medium text-gray-700 mb-2">Nenhuma tarefa encontrada</h3>
                        <p class="text-sm text-gray-500">Comece criando sua primeira tarefa</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
