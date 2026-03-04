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
                <x-page.table-th class="w-32 text-center" value="Sigla" />
                <x-page.table-th value="Ambiente" />
                <x-page.table-th class="w-40" value="Colaboração" />
                <x-page.table-th class="w-32 text-center" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($taskHubs as $taskHub)
                <tr class="group">
                    <x-page.table-td class="text-center font-mono">
                        <a href="{{ route('tasks.show', $taskHub->uuid) }}" class="inline-flex flex-col items-center gap-2">
                            <span class="inline-flex min-w-16 items-center justify-center rounded-xl bg-emerald-100 px-3 py-2 text-sm font-bold tracking-[0.2em] text-emerald-800 transition-colors duration-200 group-hover:bg-emerald-200">
                                {{ $taskHub->acronym }}
                            </span>

                            <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.2em] {{ $taskHub->owner_id === auth()->id() ? 'bg-emerald-50 text-emerald-700' : 'bg-sky-50 text-sky-700' }}">
                                {{ $taskHub->owner_id === auth()->id() ? 'Próprio' : 'Compartilhado' }}
                            </span>
                        </a>
                    </x-page.table-td>

                    <x-page.table-td class="whitespace-normal">
                        <a href="{{ route('tasks.show', $taskHub->uuid) }}" class="block rounded-2xl border border-transparent px-1 py-1">
                            <div class="flex flex-col gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-gray-900">{{ $taskHub->title }}</span>
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.2em] text-gray-600">
                                        {{ $taskHub->members->count() }} {{ \Illuminate\Support\Str::plural('membro', $taskHub->members->count()) }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 text-[11px]">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 font-medium text-amber-700">
                                        <i class="fas fa-crown text-[10px]"></i>
                                        {{ $taskHub->owner?->name ?? 'Sem responsável' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </x-page.table-td>

                    <x-page.table-td class="whitespace-normal">
                        <div class="space-y-3">
                            <div class="flex -space-x-2 transition-all duration-200">
                                @foreach ($taskHub->members->take(4) as $member)
                                    @if($member->user?->avatar)
                                        <img src="{{ asset('storage/' . $member->user->avatar) }}" alt="{{ $member->user->name }}" class="size-8 rounded-full border-2 border-white shadow-sm object-cover object-center hover:scale-110 transition-transform duration-200" title="{{ $member->user->name }}" loading="lazy">
                                    @elseif($member->user)
                                        <div class="size-8 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-[10px] font-semibold border-2 border-white shadow-sm hover:scale-110 transition-transform duration-200 uppercase" title="{{ $member->user->name }}">
                                            {{ \Illuminate\Support\Str::substr($member->user->name, 0, 2) }}
                                        </div>
                                    @endif
                                @endforeach

                                @if($taskHub->members->count() > 4)
                                    <div class="size-8 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-xs font-medium text-gray-600 hover:bg-gray-200 transition-colors duration-200"
                                        title="{{ $taskHub->members->count() - 4 }} membros restantes">
                                        +{{ $taskHub->members->count() - 4 }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-page.table-td>

                    <x-page.table-td class="text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <x-button href="{{ route('tasks.show', $taskHub->uuid) }}" icon="fas fa-arrow-right" variant="green_text" title="Acessar ambiente" />
                            <a href="{{ route('tasks.show', $taskHub->uuid) }}" class="text-[11px] font-medium text-emerald-700 transition-colors hover:text-emerald-800">
                                Abrir ambiente
                            </a>
                        </div>
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <x-page.table-td colspan="4" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100">
                                <i class="fas fa-layer-group text-lg text-emerald-600"></i>
                            </div>
                            <p class="mb-3 text-sm text-gray-500">Nenhum ambiente encontrado</p>
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
