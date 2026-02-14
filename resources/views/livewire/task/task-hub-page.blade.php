<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header -->
    <x-page.header 
        title="Meus Ambientes" subtitle="Acesse seus ambientes de atividades próprios e compartilhados" icon="fa-solid fa-layer-group" >
        <x-slot name="button">
            <x-button text="Novo Ambiente" icon="fa-solid fa-plus" wire:click="create"/>
        </x-slot>
    </x-page.header>

    <!-- Card Principal Premium -->
    <div>
        <x-page.table>
            <x-slot name="thead">
                <tr>
                    <x-page.table-th class="w-28 text-center" value="Sigla" />
                    <x-page.table-th value="Ambiente" />
                    <x-page.table-th class="w-28 text-center" value="Ações" />
                </tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach ($taskHubs as $taskHub)
                    <tr>
                        <x-page.table-td :value="$taskHub->acronym" />
                        <x-page.table-td :value="$taskHub->title" />
                        <x-page.table-td>
                            <x-button href="{{ route('tasks.show', $taskHub->uuid) }}" icon="fas fa-plus" variant="green_text" />
                        </x-page.table-td>
                    </tr>
                @endforeach
            </x-slot>
        </x-page.table>
    </div>

    <!-- Modal -->
    <x-modal :show="$showModal" wire:key="task-hub-modal">
        @if ($modalKey === 'modal-task-hub')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">
                    Criar uma novo ambiente
                </h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">

                    <!-- ACRONYM -->
                    <div class="md:col-span-3">
                        <x-form.input 
                            type="text"
                            name="acronym"
                            wire:model.defer="acronym"
                            placeholder="Sigla"
                        />
                    </div>

                    <!-- TÍTULO -->
                    <div class="md:col-span-9">
                        <x-form.input 
                            type="text"
                            name="title"
                            wire:model.defer="title"
                            placeholder="Nome do Ambiente *"
                            autofocus
                        />
                    </div>

                    <!-- DESCRIÇÃO -->
                    <div class="md:col-span-12">
                        <x-form.textarea 
                            name="description"
                            wire:model.defer="description"
                            placeholder="Descrição (opcional)"
                        />
                    </div>

                </div>
                
                <div class="flex justify-between gap-2">
                    <x-button variant="red" text="Cancelar" wire:click="closeModal" variant="gray_outline" />
                    <x-button type="submit" text="Copiar etapas" />
                </div>
            </form>
        @endif
    </x-modal>

</div>
