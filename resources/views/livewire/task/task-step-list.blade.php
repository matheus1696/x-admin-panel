<div>

    <!-- Flash Message -->
    <x-alert.flash />
    
    <div class="grid grid-cols-12 gap-2 px-5 items-center divide-x">
        
        <!-- TÍTULO -->
        <div class="col-span-4 flex items-center gap-2">                        
            <div class="flex-1 flex items-center gap-2">
                <span class="flex-1 text-gray-700 truncate text-xs pl-10">
                    {{ $step->code }} - {{ $step->title }}
                </span>
            </div>
        </div>

        <!-- ORGANIZACAO -->
        <div class="col-span-2">
            <div class="px-2">
                <x-form.select-livewire wire:model.live="responsable_organization_id" :collection="$organizations" valueField="id" labelAcronym="acronym" labelField="title" :selected="$step->organization_id" variant="inline" />
            </div>
        </div>

        <!-- RESPONSÁVEL -->
        <div class="col-span-2">
            <div class="px-2">
                <x-form.select-livewire wire:model.live="responsable_id" :collection="$users" valueField="id" labelField="name" :selected="$step->user_id" variant="inline" />
            </div>
        </div>

        <!-- PRIORIDADE -->
        <div class="col-span-1">
            <div class="flex justify-center">
                @if($step->priority)
                    <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full truncate max-w-full">
                        {{ $step->priority->title }}
                    </span>
                @else
                    <span class="text-xs text-gray-400 italic">—</span>
                @endif
            </div>
        </div>

        <!-- STATUS -->
        <div class="col-span-1">
            <div class="flex justify-center">
                @if($step->taskStatus)
                    <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full truncate max-w-full">
                        {{ $step->taskStatus->title }}
                    </span>
                @else
                    <span class="text-xs text-gray-400 italic">—</span>
                @endif
            </div>
        </div>

        <!-- DATAS -->

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
</div>
