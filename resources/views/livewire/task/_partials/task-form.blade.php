@php($showActions = $showActions ?? true)

<div class="grid grid-cols-12 gap-4 items-start">
    <div class="col-span-12">
        <x-form.label value="Título" for="title" />
        <x-form.input
            type="text"
            id="title"
            name="title"
            wire:model.defer="title"
            placeholder="Título da tarefa"
            autofocus
        />
        <x-form.error for="title" />
    </div>

    <div class="col-span-6">
        <x-form.label value="Responsável" for="user_id" />
        <x-form.select-livewire
            id="user_id"
            name="user_id"
            wire:model.defer="user_id"
            :collection="$responsibleUsers"
            valueField="id"
            labelField="name"
            default="Selecione o responsável"
        />
        <x-form.error for="user_id" />
    </div>

    <div class="col-span-6">
        <x-form.label value="Categoria" for="task_category_id" />
        <x-form.select-livewire
            id="task_category_id"
            name="task_category_id"
            wire:model.defer="task_category_id"
            :collection="$taskCategories"
            valueField="id"
            labelField="title"
            default="Selecione a categoria"
        />
        <x-form.error for="task_category_id" />
    </div>

    <div class="col-span-6">
        <x-form.label value="Prioridade" for="task_priority_id" />
        <x-form.select-livewire
            id="task_priority_id"
            name="task_priority_id"
            wire:model.defer="task_priority_id"
            :collection="$taskPriorities"
            valueField="id"
            labelField="title"
            default="Selecione a prioridade"
        />
        <x-form.error for="task_priority_id" />
    </div>

    <div class="col-span-6">
        <x-form.label value="Status" for="task_status_id" />
        <x-form.select-livewire
            id="task_status_id"
            name="task_status_id"
            wire:model.defer="task_status_id"
            :collection="$taskStatuses"
            valueField="id"
            labelField="title"
            default="Selecione o status"
        />
        <x-form.error for="task_status_id" />
    </div>

    @if ($showActions)
        <div class="col-span-12 flex justify-center gap-2 pt-2">
            <x-button type="submit" icon="fa-solid fa-check" variant="green_outline" />
            <x-button type="button" icon="fa-solid fa-close" variant="red_outline" wire:click="cancelCreateTask()" />
        </div>
    @endif
</div>

