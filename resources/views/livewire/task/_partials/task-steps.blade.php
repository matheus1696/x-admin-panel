<div class="divide-y">

    <div x-show="openCreateStep" x-collapse class="border-t bg-white px-4 py-3" >
        <form wire:submit.prevent="storeStep({{$task->id}})" class="space-y-4">
            @include('livewire.task._partials.task-step-form', ['task' => $task])
        </form>
    </div>

    <div x-show="openSteps" x-collapse class="divide-y">
        @foreach ($task->taskSteps as $step)
            <div class="grid grid-cols-12 gap-2 px-5 py-3 items-center divide-x text-xs">

                <div class="col-span-4 text-start">
                    <span class="pl-8 pr-3 line-clamp-1">{{ $step->code }} - {{ $step->title }}</span>
                </div>

                <!-- RESPONSÁVEL -->
                <div class="col-span-2">
                    <div class="relative flex items-center gap-2 px-2" >
                        <!-- AVATAR / PLACEHOLDER -->
                        <div>
                            @if($task->user)
                                <div class="size-6 rounded-full bg-gradient-to-br from-green-700 to-green-800 
                                            flex items-center justify-center text-white text-sm font-semibold shadow-sm">
                                    {{ substr($task->user->name, 0, 1) }}
                                </div>
                            @else
                                <div class="size-6 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fa-solid fa-user-plus text-gray-400 text-[10px]"></i>
                                </div>
                            @endif
                        </div>

                        <!-- NOME -->
                        <span class="text-xs text-gray-700 truncate mt-1 text-center">
                            {{ $task->user?->name }}
                        </span>
                    </div>
                </div>

                <!-- CATEGORIA -->
                <div class="col-span-1">
                    <div class="flex justify-center">
                        @if($step->taskCategory)
                            <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full truncate max-w-full">
                                {{ $step->taskCategory->title }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400 italic">—</span>
                        @endif
                    </div>
                </div>

                <!-- PRIORIDADE -->
                <div class="col-span-1">
                    <div class="flex justify-center">
                        @if($step->taskPriority)
                            <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full truncate max-w-full">
                                {{ $step->taskPriority->title }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400 italic">—</span>
                        @endif
                    </div>
                </div>

                <!-- STATUS -->
                <div class="col-span-1">
                    <div class="flex justify-center">
                        @if($step->taskStepStatus)
                            <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full truncate max-w-full">
                                {{ $step->taskStepStatus->title }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400 italic">—</span>
                        @endif
                    </div>
                </div>

                <!-- Iniciado em -->
                <div class="col-span-1">
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
                <div class="col-span-1">
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

                <!-- Finalizado em -->
                <div class="col-span-1">
                    <div class="flex flex-col items-center">
                        @if ($step->finished_at)
                            <div class="text-xs text-green-600 font-medium">
                                {{ $step->finished_at->format('d/m/Y') }}
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">—</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
