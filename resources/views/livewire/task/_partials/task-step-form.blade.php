<div class="grid grid-cols-4 md:grid-cols-12 gap-2 items-center">
    <div class="col-span-4">
        <x-form.input
            type="text"
            name="step_title"
            wire:model.defer="step_title"
            placeholder="Título da etapa"
            autofocus
            variant="pills"
        />
    </div>

    <div class="col-span-3 hidden md:grid grid-cols-2 gap-2">
        <div class="hidden md:block">
            <x-form.select-livewire
                name="organization_id"
                wire:model.defer="organization_id"
                :collection="$organizations"
                valueField="id"
                labelField="title"
                default="Setor"
                variant="pills"
            />
        </div>

        <div class="hidden md:block">
            <x-form.select-livewire
                name="step_user_id"
                wire:model.defer="step_user_id"
                :collection="$users"
                valueField="id"
                labelField="name"
                default="Responsável"
                variant="pills"
            />
        </div>
    </div>

    <div class="col-span-3 hidden md:grid grid-cols-2 gap-2">
        <div class="col-span-1">
            <x-form.select-livewire
                name="step_task_priority_id"
                wire:model.defer="step_task_priority_id"
                :collection="$taskPriorities"
                valueField="id"
                labelField="title"
                default="Prioridade"
                variant="pills"
            />
        </div>

        <div class="col-span-1">
            <x-form.select-livewire
                name="task_step_status_id"
                wire:model.defer="task_step_status_id"
                :collection="$taskStepStatuses"
                valueField="id"
                labelField="title"
                default="Status"
                variant="pills"
            />
        </div>
    </div>

    <div class="col-span-1 lg:col-span-2 flex justify-center gap-2">
        <x-button
            type="button"
            icon="fa-solid fa-check"
            variant="green_outline"
            wire:click="storeTaskStep({{ $taskId }})"
        />
        <x-button
            type="button"
            icon="fa-solid fa-close"
            variant="red_outline"
            wire:click="cancelCreateTaskStep()"
            @click="stepFormTaskId = null"
        />
    </div>
</div>
