<div>

    <x-page.header
        title="Permissoes do Usuario"
        subtitle="Gerencie perfis (roles) e permissoes individuais"
        icon="fa-solid fa-key"
    >
        <x-slot name="button">
            <x-button
                :href="route('administration.manage.users')"
                text="Voltar"
                icon="fa-solid fa-arrow-left"
                variant="gray_outline"
            />
        </x-slot>
    </x-page.header>

    <div class="mb-4 rounded-2xl border border-gray-200 bg-white p-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                <p class="text-xs text-gray-500">{{ $user->email }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-right">
                <p class="text-[11px] uppercase text-emerald-700">Permissoes selecionadas</p>
                <p class="text-sm font-semibold text-emerald-800">{{ $selectedCount }} / {{ $totalCount }}</p>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                Perfis (roles)
            </h3>

            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($allRoles as $role)
                    <label class="cursor-pointer rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 transition hover:border-emerald-400">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model.defer="selectedRoles" value="{{ $role->name }}" />
                            <div>
                                <p class="text-xs font-semibold text-gray-800">{{ $role->translation ?: $role->name }}</p>
                                <p class="text-[11px] text-gray-500">{{ $role->permissions->count() }} permissao(oes)</p>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            <x-form.error for="selectedRoles" />
            <x-form.error for="selectedRoles.*" />
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                Usuario sombra
            </h3>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                <div class="md:col-span-9">
                    <x-form.label value="Copiar perfis e permissoes de" />
                    <x-form.select-livewire
                        wire:model.defer="shadowUserId"
                        name="shadowUserId"
                        :default="'Selecione um usuario'"
                        :options="collect($shadowUsers)->map(fn ($shadowUser) => ['value' => $shadowUser->id, 'label' => $shadowUser->name.' - '.$shadowUser->email])->values()->all()"
                    />
                    <x-form.error for="shadowUserId" />
                </div>
                <div class="md:col-span-3 flex items-end">
                    <x-button
                        type="button"
                        wire:click="copyFromShadowUser"
                        text="Copiar"
                        icon="fa-solid fa-clone"
                        variant="blue_outline"
                        fullWidth="true"
                    />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                Busca e acoes em lote
            </h3>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                <div class="md:col-span-7">
                    <x-form.label value="Buscar permissao" />
                    <x-form.input
                        type="text"
                        wire:model.live.debounce.300ms="permissionSearch"
                        placeholder="Busque por nome tecnico ou traducao"
                    />
                </div>
                <div class="md:col-span-2 flex items-end">
                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-700">
                        <input type="checkbox" wire:model.live="onlySelected" />
                        Somente selecionadas
                    </label>
                </div>
                <div class="md:col-span-3 flex items-end gap-2">
                    <x-button type="button" wire:click="selectVisiblePermissions" text="Marcar visiveis" variant="green_outline" fullWidth="true" />
                    <x-button type="button" wire:click="clearVisiblePermissions" text="Limpar visiveis" variant="red_outline" fullWidth="true" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                Permissoes por grupo (role)
            </h3>

            @forelse ($roles as $role)
                <div class="mb-6">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">
                            {{ $role->translation ?: $role->name }}
                        </h3>
                        <span class="text-xs text-gray-500">
                            {{ $role->permissions->count() }} permissao(oes)
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        @foreach ($role->permissions as $permission)
                            <div class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    id="role_{{ $role->id }}_permission_{{ $permission->id }}_{{ $user->uuid }}"
                                    wire:model.defer="permissions"
                                    name="permissions[]"
                                    value="{{ $permission->name }}"
                                    class="hidden peer"
                                >
                                <label
                                    for="role_{{ $role->id }}_permission_{{ $permission->id }}_{{ $user->uuid }}"
                                    class="w-full cursor-pointer rounded-lg border px-3 py-2 text-center text-[10px] font-bold text-gray-700 transition hover:border-green-500 peer-checked:border-green-600 peer-checked:bg-green-600 peer-checked:text-white"
                                >
                                    {{ ucfirst($permission->translation ?: $permission->name) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if (! $loop->last)
                    <hr class="my-4">
                @endif
            @empty
                <div class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">
                    Nenhuma permissao encontrada com os filtros atuais.
                </div>
            @endforelse

            <x-form.error for="permissions" />
            <x-form.error for="permissions.*" />
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <x-button
                :href="route('administration.manage.users')"
                text="Cancelar"
                variant="gray_outline"
            />
            <x-button type="submit" text="Salvar perfis e permissoes" icon="fa-solid fa-floppy-disk" />
        </div>
    </form>
</div>
