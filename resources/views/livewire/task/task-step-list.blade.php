<div>

    <!-- Flash Message -->
    <x-alert.flash />
    
    <div class="grid grid-cols-5 md:grid-cols-12 gap-2 px-5 py-1.5 items-center divide-x border-b hover:bg-green-200/50 transition-colors duration-150">
        
        <!-- TÍTULO -->
        <div class="col-span-3 flex items-center justify-center">                        
            <div class="flex-1 flex items-center gap-2">
                <div>
                    <x-button variant="green_text" :text="$step->code"
                        @click=" openAsideTask = false; openAsideTaskStep = true; 
                        if (activeTaskStepItem === {{ $step->id }})
                            activeTaskStepItem = null; 
                        else 
                            activeTaskStepItem = {{ $step->id }} "
                         />
                </div> -
                <span class="flex-1 text-gray-700 line-clamp-1 text-xs">
                    {{ $step->title }}
                </span>
            </div>
        </div>

        <!-- ORGANIZACAO -->
        <div class="col-span-2 hidden md:block">
            <div>
                <x-form.select-livewire wire:model.live="responsable_organization_id" :collection="$organizations" valueField="id" labelAcronym="acronym" labelField="title" :selected="$step->organization_id" variant="pills" size="xs" />
            </div>
        </div>

        <!-- RESPONSÁVEL -->
        <div class="col-span-2">
            <div>
                <x-form.select-livewire wire:model.live="responsable_id" :collection="$users" valueField="id" labelField="name" :selected="$step->user_id" variant="pills" size="xs" />
            </div>
        </div>

        <div class="col-span-3 hidden md:grid grid-cols-2 items-center justify-center gap-2 divide-x">
            <!-- PRIORIDADE -->
            <div>
                <x-form.select-livewire wire:model.live="list_priority_id" :collection="$taskPriorities" valueField="id" labelField="title" :selected="$step->task_priority_id" variant="pills" size="xs"  borderColor="{{ $step->taskPriority?->color }}" />
            </div>

            <!-- STATUS -->
            <div>
                <x-form.select-livewire wire:model.live="list_task_step_status_id" :collection="$taskStepStatuses" valueField="id" labelField="title" :selected="$step->task_status_id" variant="pills" size="xs" borderColor="{{ $step->taskStepStatus?->color }}"/>
            </div>
        </div>

        <!-- DATAS -->
        <!-- Inicio -->
        <div class="col-span-1 hidden md:block">
            <div class="flex flex-col items-center">
                @if ($step->started_at)
                    <div class="text-xs text-gray-700 font-medium">
                        {{ $step->started_at->format('d/m/Y') }}
                    </div>
                @else
                    <span class="text-xs text-gray-400 italic">—</span>
                @endif
            </div>
        </div>

        <!-- Prazo -->
        <div class="col-span-1 hidden md:block">
            <div class="flex flex-col items-center">
                @if ($step->deadline_at)
                    <div class="text-xs text-gray-700 font-medium">
                        {{ $step->deadline_at->format('d/m/Y') }}
                    </div>
                @else
                    <span class="text-xs text-gray-400 italic">—</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Overlay que escurece o resto da página -->
    <div 
        x-show="openAsideTaskStep && activeTaskStepItem === {{ $step->id }}"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="openAsideTaskStep = false; activeTaskStepItem = null"
        class="fixed inset-0 bg-black bg-opacity-70 z-40"
        aria-hidden="true"
    ></div>

    <div
        x-show="openAsideTaskStep && activeTaskStepItem === {{ $step->id }}"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 z-50 h-screen w-full md:w-1/3 bg-white text-gray-900 overflow-y-auto shadow-lg border-l-2 border-green-700">

        <livewire:task.task-step-aside :stepId="$step->id" :key="'aside-'.$step->id" />
    </div>
</div>
