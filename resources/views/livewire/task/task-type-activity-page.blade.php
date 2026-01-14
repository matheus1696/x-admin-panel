<div>

    <!-- Flash Message -->
    <x-alert.flash />

    {{-- Formulário --}}
    <form wire:submit.prevent="{{ $activityId ? 'update' : 'store' }}" class="pb-5 border-b mb-5">
        <div class="grid grid-cols-6 items-end gap-3">
            <div class="col-span-3">
                <x-form.label value="Atividade" />
                <x-form.input wire:model.defer="title" placeholder="Título da Atividade"/>
                <x-form.error :messages="$errors->get('title')" />
            </div>
            <div>
                <x-form.label value="Dias" />
                <x-form.input type="number" wire:model.defer="deadline_days" placeholder="60"/>
                <x-form.error :messages="$errors->get('deadline_days')" />
            </div>
            <div class="col-span-2 py-0.5 text-xs text-white">
                @if($activityId)
                    <div class="flex gap-1">
                        <x-button.btn type="submit" value="Alterar Atividade" class="bg-blue-600 truncate"/>
                        <x-button.btn type="button" wire:click="closedUpdate" value="X" class="bg-red-600 truncate"/>
                    </div>
                @else
                    <div class="flex gap-1">
                        <x-button.btn type="submit" value="Nova Atividade" class="w-full bg-green-600 truncate"/>
                    </div>
                @endif
                
            </div>
        </div>
    </form>

    {{-- Lista de atividades --}}
    <x-page.table>
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="text-center">Ordem</x-page.table-th>
                <x-page.table-th>Atividade</x-page.table-th>
                <x-page.table-th class="text-center">Dias</x-page.table-th>
                <x-page.table-th class="text-center w-28">Ações</x-page.table-th>
            </tr>
        </x-slot>
        <x-slot name="tbody">
            @forelse ($activities as $activity)
                <tr class="hover:bg-gray-50">
                    <x-page.table-td class="text-center">{{ $activity->order }}</x-page.table-td>
                    <x-page.table-td>{{ $activity->title }}</x-page.table-td>
                    <x-page.table-td class="text-center">{{ $activity->deadline_days }}</x-page.table-td>
                    <x-page.table-td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            <x-button.btn-table wire:click="edit({{ $activity->id }})" title="Editar Atividade">
                                <i class="fa-solid fa-pen"></i>
                            </x-button.btn-table>
                            @if ( $activity->order != 1)
                                <x-button.btn-table wire:click="orderUp({{ $activity->id }})" title="Subir Atividade">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </x-button.btn-table>
                            @endif
                        </div>
                        
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                        No activities added yet.
                    </td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

</div>
