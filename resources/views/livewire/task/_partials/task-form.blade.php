<div class="grid grid-cols-4 md:grid-cols-12 gap-2 items-center">
    <!-- TÍTULO -->
    <div class="col-span-3">
        <x-form.input type="text" name="title" wire:model.defer="title" placeholder="Título da Atividade *" variant='pills' autofocus />
    </div>
    
    <div class="col-span-2 hidden md:block">
        <x-form.select-livewire name="user_id" wire:model.defer="user_id" :collection="$users" value-field="id" label-field="name" variant='pills' />
    </div>

    <div class="col-span-5 hidden md:grid grid-cols-3 gap-2">
        <div class="col-span-1">
            <x-form.select-livewire name="task_category_id" wire:model.defer="task_category_id" :collection="$taskCategories" value-field="id" label-field="title" variant='pills' />
        </div>
        
        <div class="col-span-1">
            <x-form.select-livewire name="task_priority_id" wire:model.defer="task_priority_id" :collection="$taskPriorities" value-field="id" label-field="title" variant='pills' />
        </div>
        
        <div class="col-span-1">
            <x-form.select-livewire name="task_status_id" wire:model.defer="task_status_id" :collection="$taskStatuses" value-field="id" label-field="title" variant='pills' />
        </div>
    </div>

    <!-- AÇÃO -->
    <div class="col-span-1 lg:col-span-2 flex justify-center gap-2">
        <x-button type="submit" icon="fa-solid fa-check" variant="green_outline" />
        <x-button type="button" icon="fa-solid fa-close" variant="red_outline" wire:click="cancelCreateTask()" />
    </div>

</div>
