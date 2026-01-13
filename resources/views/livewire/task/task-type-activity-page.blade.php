<div>

    <!-- Flash Message -->
    <x-alert.flash />

    {{-- Formulário --}}
    <form wire:submit.prevent="{{ $activityId ? 'update' : 'store' }}" class="space-y-3">
        <div>
            <x-form.label value="Activity Title" />
            <x-form.input wire:model.defer="title" placeholder="Research Prices"/>
            <x-form.error :messages="$errors->get('title')" />
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <x-form.label value="Days" />
                <x-form.input type="number" wire:model.defer="deadline_days" placeholder="60"/>
                <x-form.error :messages="$errors->get('deadline_days')" />
            </div>
        </div>

        <div class="flex justify-end gap-2">
            @if($activityId)
                <x-button.btn type="submit" value="Update Activity" class="bg-blue-600 text-white"/>
            @else
                <x-button.btn type="submit" value="Add Activity" class="bg-green-600 text-white"/>
            @endif
        </div>
    </form>

    {{-- Lista de atividades --}}
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm border-t border-gray-100 divide-y divide-gray-100">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-2 text-left">Order</th>
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2 text-left">Days</th>
                    <th class="px-4 py-2 text-center w-28">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activities as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium text-gray-800">{{ $activity->order }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $activity->title }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $activity->deadline_days }}</td>
                        <td class="px-4 py-2 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <x-button.btn-table wire:click="edit({{ $activity->id }})" title="Edit Activity">
                                    <i class="fa-solid fa-pen"></i>
                                </x-button.btn-table>
                            </div>
                            
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                            No activities added yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
