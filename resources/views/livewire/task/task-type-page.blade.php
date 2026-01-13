<div class="space-y-6">

    {{-- Header --}}
    <x-page.header title="Task Types" subtitle="Manage task type workflows" icon="fa-solid fa-diagram-project">
        <x-slot name="button">
            <x-modal title="Cadastrar Tipo de Tarefa">
                <x-slot name="button">
                    <x-button.btn-link type="button" value="Novo Usuário" icon="fa-solid fa-plus" title="Redefinir Senha do Usuário"/>
                </x-slot>

                <x-slot name="body">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase mb-4">
                        Create Task Type
                    </h2>

                    <form wire:submit.prevent="store" class="space-y-4">

                        <div>
                            <x-form.label value="Title" />
                            <x-form.input wire:model.defer="title" placeholder="Procurement Process" />
                            <x-form.error :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-form.label value="Description" />
                            <x-form.input wire:model.defer="description" placeholder="Describe the workflow..." />
                            <x-form.error :messages="$errors->get('description')" />
                        </div>

                        <div class="flex justify-end">
                            <x-button.btn type="submit" value="Save Task Type" />
                        </div>

                    </form>
                </x-slot>
            </x-modal>
        </x-slot>
    </x-page.header>

    {{-- Flash --}}
    <x-alert.flash />

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs border-b">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Description</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    <th class="px-6 py-3 text-center w-32">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($taskTypes as $taskType)
                    <tr class="hover:bg-gray-50">

                        <td class="px-6 py-3 font-medium text-gray-800">
                            {{ $taskType->title }}
                        </td>

                        <td class="px-6 py-3 text-gray-600">
                            {{ $taskType->description ?? '-' }}
                        </td>

                        <td class="px-6 py-3 text-center">
                            <div class="flex justify-center items-center gap-2">
                                <span class="w-2 h-2 rounded-full 
                                    {{ $taskType->status ? 'bg-green-500' : 'bg-red-500' }}">
                                </span>
                                <span class="text-xs font-medium
                                    {{ $taskType->status ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $taskType->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-3">
                            <div class="flex items-center justify-center gap-2">

                                <!-- Toggle Status -->
                                <button 
                                    wire:click="toggleStatus({{ $taskType->id }})"
                                    class="text-gray-500 hover:text-green-700 transition"
                                    title="Toggle Status"
                                >
                                    <i class="fa-solid fa-power-off"></i>
                                </button>

                                <!-- Edit (próximo passo) -->
                                <x-modal title="Cadastrar Tipo de Tarefa">
                                    <x-slot name="button">
                                        <x-button.btn-table title="Redefinir Senha do Usuário" wire:click="edit({{ $taskType->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </x-button.btn-table>
                                    </x-slot>

                                    <x-slot name="body">
                                        <h2 class="text-sm font-semibold text-gray-700 uppercase mb-4">
                                            Create Task Type
                                        </h2>

                                        <form wire:submit.prevent="update" class="space-y-4">

                                            <div>
                                                <x-form.label value="Title" />
                                                <x-form.input wire:model.defer="title" placeholder="Procurement Process" />
                                                <x-form.error :messages="$errors->get('title')" />
                                            </div>

                                            <div>
                                                <x-form.label value="Description" />
                                                <x-form.input wire:model.defer="description" placeholder="Describe the workflow..." />
                                                <x-form.error :messages="$errors->get('description')" />
                                            </div>

                                            <div class="flex justify-end">
                                                <x-button.btn type="submit" value="Save Task Type" />
                                            </div>

                                        </form>
                                    </x-slot>
                                </x-modal>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                            No task types registered.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
