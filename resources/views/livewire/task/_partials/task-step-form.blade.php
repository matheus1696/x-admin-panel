<div class="grid grid-cols-12 gap-2 -mt-4">
    <div class="col-span-9 md:col-span-11">
        <x-form.input
            type="text"
            name="step_title"
            wire:model.defer="step_title"
            placeholder="Título da etapa"
            autofocus
            variant="pills"
        />
    </div>

    <div class="col-span-3 md:col-span-1 flex justify-center gap-2">
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

