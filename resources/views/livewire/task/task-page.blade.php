<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header title="Cronograma de Atividades" subtitle="Visualize todas as atividades e os andamentos" icon="fa-solid fa-list-check" >
        <x-slot name="button">
            <x-button text="Nova Tarefa" icon="fa-solid fa-plus" wire:click="create" />
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
    <div class="bg-white border rounded-xl shadow-sm divide-y">

        <!-- HEADER DA GRID -->
        <div class="grid grid-cols-12 gap-3 px-4 py-2 text-[11px] text-gray-500 bg-gray-50 border-b font-medium">
            <div class="col-span-4">Título</div>
            <div class="col-span-1">Responsável</div>
            <div class="col-span-1 text-center">Categoria</div>
            <div class="col-span-1 text-center">Prioridade</div>
            <div class="col-span-1 text-center">Status</div>
            <div class="col-span-1 text-center">Criado em</div>
            <div class="col-span-1 text-center">Iniciado em</div>
            <div class="col-span-1 text-center">Prazo</div>
            <div class="col-span-1 text-center">Finalizado em</div>
        </div>

        @forelse ($tasks as $task)
            <div x-data="{ openSteps: false, openCreateStep: false }">

                <!-- LINHA DA TASK -->
                <div class="grid grid-cols-12 gap-3 px-4 py-3 text-xs items-center hover:bg-gray-50 transition">

                    <!-- TÍTULO -->
                    <div class="col-span-4 flex items-center gap-3 pr-5">                        
                        <div class="w-full flex items-center gap-3">
                            @if ($task->taskSteps->count() > 0)
                                <i
                                    class="fa-solid fa-chevron-right text-gray-400 transition cursor-pointer"
                                    :class="{ 'rotate-90': openSteps }"
                                    @click.stop="openCreateStep = false; openSteps = !openSteps"
                                ></i>
                            @endif

                            <span class="flex-1 font-medium text-gray-800 truncate">
                                {{ $task->title }}
                            </span>
                        </div>

                        <span>
                            <x-button icon="fa-solid fa-plus" variant="green_outline" title="Adicionar etapa" @click.stop="openCreateStep = true; openSteps = !openSteps" />
                        </span>
                    </div>

                    <!-- RESPONSÁVEL -->
                    <div class="col-span-1 text-gray-600 truncate">
                        {{ $task->responsible?->name ?? '—' }}
                    </div>

                    <!-- RESPONSÁVEL -->
                    <div class="col-span-1 text-gray-600 truncate">
                        {{ $task->responsible?->name ?? '—' }}
                    </div>

                    <!-- PRIORIDADE -->
                    <div class="col-span-1 text-center">
                        <span class="px-2 py-0.5 rounded-full bg-gray-200 text-gray-700 text-[11px]">
                            {{ $task->priority?->title ?? 'Normal' }}
                        </span>
                    </div>

                    <!-- STATUS -->
                    <div class="col-span-1 text-center">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                            {!! $task->taskStatus?->color ?? 'bg-gray-200 text-gray-700' !!}">
                            {{ $task->taskStatus?->title ?? 'Rascunho' }}
                        </span>
                    </div>

                    <!-- DATAS -->
                    <div class="col-span-1 flex flex-col text-[11px] text-gray-500 text-center leading-tight">
                        <span>{{ $task->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="col-span-1 flex flex-col text-[11px] text-gray-500 text-center leading-tight">
                        @if ($task->started_at)
                            <span>Início: {{ $task->started_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <div class="col-span-1 flex flex-col text-[11px] text-gray-500 text-center leading-tight">
                        @if ($task->deadline_at)
                            <span>Fim: {{ $task->deadline_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <div class="col-span-1 flex flex-col text-[11px] text-gray-500 text-center leading-tight">
                        @if ($task->finished_at)
                            <span>Fim: {{ $task->finished_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>

                <!-- ETAPAS -->
                <div x-show="openSteps" x-collapse class="bg-gray-50 border-t" >
                    @include('livewire.task._partials.task-steps', ['task' => $task])
                </div>

            </div>
        @empty
            <div class="text-center text-gray-500 py-10 text-sm">
                Nenhuma tarefa encontrada.
            </div>
        @endforelse

    </div>

    <!-- Modal de Criação -->
    <x-modal :show="$showModal" wire:key="workflow-modal">
        @if ($modalKey === 'modal-form-create-task')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Nova Tarefa</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.task._partials.task-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
        @if ($modalKey === 'modal-form-create-task-step')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Adicionar Etapa</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.task._partials.task-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif
    </x-modal>

</div>
