<div>

    <!-- Flash Message -->
    <x-alert.flash />

    <!-- Header Padronizado -->
    <x-page.header 
        title="Meus Ambientes" 
        subtitle="Acesse seus ambientes de atividades próprios e compartilhados" 
        icon="fas fa-layer-group"
    >
        <x-slot name="button">
            <x-button text="Novo Ambiente" icon="fas fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>

    <!-- Tabela Padronizada -->
    <x-page.table>
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="w-28 text-center" value="Sigla" />
                <x-page.table-th class="text-center" value="Ambiente" />
                <x-page.table-th class="w-28 text-center" value="Usuários" />
                <x-page.table-th class="w-28 text-center" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($taskHubs as $taskHub)
                <tr>
                    <x-page.table-td class="text-center font-mono">
                        <a href="{{ route('tasks.show', $taskHub->uuid) }}">{{ $taskHub->acronym }}</a>
                    </x-page.table-td>
                    <x-page.table-td>
                        <a href="{{ route('tasks.show', $taskHub->uuid) }}">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900">{{ $taskHub->title }}</span>
                                @if($taskHub->description)
                                    <span class="text-xs text-gray-500">{{ $taskHub->description }}</span>
                                @endif
                            </div>
                        </a>
                    </x-page.table-td>
                    <x-page.table-td>
                        <div class="flex -space-x-2 transition-all duration-200">
                            @foreach ($taskHub->members->take(3) as $member)
                                @if($member->user->avatar)
                                    <img src="{{ asset('storage/' . $member->user->avatar) }}" alt="{{ $member->user->name }}" class="size-8 rounded-full border-2 border-white shadow-sm object-cover object-center hover:scale-110 transition-transform duration-200" title="{{ $member->user->name }}" loading="lazy">
                                @else
                                    <div class="size-7 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-[9px] font-medium border-2 border-white shadow-sm hover:scale-110 transition-transform duration-200 uppercase" title="{{ $member->user->name }}">
                                        {{ Str::substr($member->user->name, 0, 2) }}
                                    </div>
                                @endif
                            @endforeach

                            @if($taskHub->members->count() > 3)
                                <div class="size-8 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-xs font-medium text-gray-600 hover:bg-gray-200 transition-colors duration-200"
                                    title="{{ $taskHub->members->count() - 3 }} membros restantes">
                                    +{{ $taskHub->members->count() - 3 }}
                                </div>
                            @endif
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="text-center">
                        <x-button href="{{ route('tasks.show', $taskHub->uuid) }}" icon="fas fa-arrow-right" variant="green_text" title="Acessar ambiente"
                        />
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <x-page.table-td colspan="3" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-layer-group text-emerald-600 text-lg"></i>
                            </div>
                            <p class="text-sm text-gray-500 mb-3">Nenhum ambiente encontrado</p>
                            <x-button 
                                text="Criar Primeiro Ambiente"
                                icon="fas fa-plus"
                                wire:click="create"
                            />
                        </div>
                    </x-page.table-td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

    <!-- Modal -->
    <x-modal :show="$showModal" maxWidth="max-w-2xl">
        @if ($modalKey === 'modal-task-hub')
            <x-slot name="header">
                {{ $taskHubId ? 'Editar Ambiente' : 'Novo Ambiente' }}
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    
                    <!-- Sigla -->
                    <div class="md:col-span-3">
                        <x-form.label value="Sigla" />
                        <x-form.input 
                            type="text"
                            name="acronym"
                            wire:model.defer="acronym"
                            placeholder="EX: PROJ"
                            maxlength="5"
                            required
                        />
                        <x-form.error for="acronym" />
                    </div>

                    <!-- Título -->
                    <div class="md:col-span-9">
                        <x-form.label value="Nome do Ambiente" />
                        <x-form.input 
                            type="text"
                            name="title"
                            wire:model.defer="title"
                            placeholder="Ex: Projetos 2025"
                            required
                            autofocus
                        />
                        <x-form.error for="title" />
                    </div>

                    <!-- Descrição -->
                    <div class="md:col-span-12">
                        <x-form.label value="Descrição (opcional)" />
                        <x-form.textarea 
                            name="description"
                            wire:model.defer="description"
                            placeholder="Descreva o propósito deste ambiente..."
                            rows="3"
                        />
                        <x-form.error for="description" />
                    </div>

                </div>
                
                <div class="flex justify-end gap-2 pt-2">
                    <x-button 
                        text="Cancelar" 
                        variant="gray_outline" 
                        wire:click="closeModal" 
                    />
                    <x-button 
                        type="submit" 
                        text="{{ $taskHubId ? 'Atualizar' : 'Criar Ambiente' }}" 
                        icon="fas fa-save"
                    />
                </div>
            </form>
        @endif
    </x-modal>

</div>