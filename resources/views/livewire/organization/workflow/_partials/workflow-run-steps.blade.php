<div class="p-4 space-y-2">

    @foreach ($workflowRun->workflowRunSteps as $step)
        <div class="group flex gap-3 bg-white rounded-md px-4 py-3 border hover:bg-gray-50 transition">

            <!-- STATUS DOT -->
            <div class="flex flex-col items-center pt-1">
                <span class="w-2.5 h-2.5 rounded-full
                    {!! $step->workflowRunStepStatus?->color ?? 'bg-gray-300' !!}">
                </span>

                <span class="w-px flex-1 bg-gray-200 mt-2"></span>
            </div>

            <!-- CONTEÚDO -->
            <div class="flex-1">

                <!-- LINHA PRINCIPAL -->
                <div class="flex items-center justify-between gap-4">

                    <div>
                        <h4 class="text-sm font-medium text-gray-800">
                            {{ $step->workflowStep->title }}
                        </h4>

                        <!-- RESPONSÁVEIS -->
                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                            @foreach ($step->users as $user)
                                <span class="px-2 py-0.5 bg-gray-100 rounded-full">
                                    {{ $user->name }}
                                </span>
                            @endforeach

                            @foreach ($step->organizationUnits as $unit)
                                <span class="px-2 py-0.5 bg-gray-100 rounded-full">
                                    {{ $unit->title }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- STATUS -->
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                        {!! $step->workflowRunStepStatus?->color ?? 'bg-gray-200 text-gray-700' !!}">
                        {{ $step->workflowRunStepStatus?->title ?? 'Pendente' }}
                    </span>
                </div>

                <!-- META INFO -->
                <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">

                    @if ($step->deadline_at)
                        <div class="flex items-center gap-1
                            {{ $step->deadline_at->isPast() ? 'text-red-600 font-medium' : '' }}">
                            <i class="fa-regular fa-clock"></i>
                            {{ $step->deadline_at->format('d/m/Y') }}
                        </div>
                    @endif

                    <div class="opacity-0 group-hover:opacity-100 transition">
                        <button class="hover:text-gray-700">
                            <i class="fa-solid fa-ellipsis"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endforeach

</div>
