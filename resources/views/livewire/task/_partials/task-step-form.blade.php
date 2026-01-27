<div class="grid grid-cols-12 gap-2 items-center">
    <!-- TÍTULO -->
    <div class="col-span-3">
        <x-form.input type="text" placeholder="Título da etapa" wire:model.defer="title" autofocus />
    </div>
    
    <div class="col-span-1">
        <x-form.select-livewire
            name="user_id"
            wire:model.defer="user_id"
            :collection="$users"
            value-field="id"
            label-field="name"
        />
    </div>
    
    <div class="col-span-1">
        <x-form.select-livewire
            name="task_category_id"
            wire:model.defer="task_category_id"
            :collection="$taskCategories"
            value-field="id"
            label-field="title"
        />
    </div>
    
    <div class="col-span-1">
        <x-form.select-livewire
            name="task_priority_id"
            wire:model.defer="task_priority_id"
            :collection="$taskPriorities"
            value-field="id"
            label-field="title"
        />
    </div>
    
    <div class="col-span-1">
        <x-form.select-livewire
            name="task_step_status_id"
            wire:model.defer="task_step_status_id"
            :collection="$taskStepStatuses"
            value-field="id"
            label-field="title"
        />
    </div>
    
    <!-- DATA -->
    <div class="col-span-1">
        <x-form.input disabled />
    </div>
    
    <!-- DATA -->
    <div class="col-span-1">
        <x-form.input disabled />
    </div>
    
    <!-- DATA -->
    <div class="col-span-1">
        <x-form.input disabled />
    </div>

    <!-- DATA -->
    <div class="col-span-1">
        <x-form.input type="date" wire:model.defer="deadline_at" />
    </div>

    <!-- AÇÃO -->
    <div class="col-span-1 flex justify-center gap-2">
        <x-button type="submit" icon="fa-solid fa-check" variant="green" />
        <x-button type="button" icon="fa-solid fa-close" variant="red" @click="openCreateStep = false" />
    </div>

</div>
