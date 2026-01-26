<div class="divide-y">

    <div x-show="openCreateStep" x-collapse class="border-t bg-white px-4 py-3" >
        @include('livewire.task._partials.task-step-form', ['task' => $task])
    </div>

    @foreach ($task->taskSteps as $step)
        <div class="grid grid-cols-12 gap-2 items-center px-4 py-2 bg-white hover:bg-gray-50">

            <!-- TÍTULO -->
            <div class="col-span-5">
                <input type="text"
                    class="w-full text-sm border-0 bg-transparent focus:ring-0 focus:outline-none"
                    wire:model.lazy="steps.{{ $step->id }}.uuid"
                />
            </div>

            <!-- DATA LIMITE -->
            <div class="col-span-2">
                <input
                    type="date"
                    class="text-sm border border-gray-200 rounded px-2 py-1 w-full"
                    wire:model.lazy="steps.{{ $step->id }}.deadline_at"
                />
            </div>

            <!-- RESPONSÁVEL -->
            <div class="col-span-3">
                <x-form.select-livewire
                    wire:model.lazy="steps.{{ $step->id }}.user_id"
                    :collection="$users"
                    value-field="id"
                    label-field="name"
                    placeholder="Responsável"
                />
            </div>

            <!-- STATUS -->
            <div class="col-span-1 text-center">
                <x-form.select-livewire
                    wire:model.lazy="steps.{{ $step->id }}.status_id"
                    :collection="$taskStepStatuses"
                    value-field="id"
                    label-field="title"
                />
            </div>

        </div>
    @endforeach
</div>
