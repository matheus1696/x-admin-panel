<div class="grid grid-cols-12 gap-2 items-center">
    <!-- TÍTULO -->
    <div class="col-span-4">
        <x-form.input type="text" placeholder="Título da etapa" wire:model.defer="newStep.title" autofocus />
    </div>
    
    <div class="col-span-1">
        <x-form.select-livewire
            wire:model.defer="newStep.user_id"
            :collection="$users"
            value-field="id"
            label-field="name"
        />
    </div>
    
    <div class="col-span-1">
        <x-form.select-livewire
            wire:model.defer="newStep.category_id"
            :collection="$taskCategories"
            value-field="id"
            label-field="title"
        />
    </div>
    
    <div class="col-span-1">
        <x-form.select-livewire
            wire:model.defer="newStep.priority_id"
            :collection="$taskPriorities"
            value-field="id"
            label-field="title"
        />
    </div>
    
    <div class="col-span-1">
        <x-form.select-livewire
            wire:model.defer="newStep.status_id"
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
        <x-form.input type="date" wire:model.defer="newStep.deadline_at" />
    </div>

    <!-- AÇÃO -->
    <div class="col-span-1 flex justify-center">
        <x-button icon="fa-solid fa-check" variant="green" wire:click="storeStep({{ $task->id }})" />
    </div>

</div>
