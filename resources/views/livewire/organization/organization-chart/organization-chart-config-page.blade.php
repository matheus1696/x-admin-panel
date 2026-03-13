<div>
    @php
        $totalOrganizations = $organizationCharts->count();
        $activeOrganizations = $organizationCharts->where('is_active', true)->count();
        $withResponsible = $organizationCharts->filter(fn ($organizationChart) => $organizationChart->responsibleUser !== null)->count();
        $withUsers = $organizationCharts->filter(fn ($organizationChart) => (int) $organizationChart->users_count > 0)->count();
    @endphp

    <x-page.header title="Organograma" subtitle="Organograma da Secretaria de Saude de Caruaru" icon="fa-solid fa-sitemap">
        <x-slot name="button">
            <x-button text="Novo Setor" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>

    <x-page.filter
        title="Filtros"
        showClear="true"
        clearAction="resetFilters"
        description="Refine a estrutura por sigla, nome, status e responsavel para localizar setores com mais precisao."
    >
        <div class="col-span-12 md:col-span-2">
            <x-form.label value="Sigla" />
            <x-form.input wire:model.live.debounce.500ms="filters.acronym" placeholder="Buscar por sigla..." />
        </div>

        <div class="col-span-12 md:col-span-6">
            <x-form.label value="Setor" />
            <x-form.input wire:model.live.debounce.500ms="filters.filter" placeholder="Buscar por setor..." />
        </div>

        <div class="col-span-6 md:col-span-2">
            <x-form.label value="Status" />
            <x-form.select-livewire
                wire:model.live="filters.status"
                name="filters.status"
                :options="[
                    ['value' => 'all', 'label' => 'Todos'],
                    ['value' => 'true', 'label' => 'Ativo'],
                    ['value' => 'false', 'label' => 'Desativado'],
                ]"
            />
        </div>

        <div class="col-span-12 md:col-span-2">
            <x-form.label value="Responsavel" />
            <x-form.select-livewire
                wire:model.live="filters.responsible_user_id"
                name="filters.responsible_user_id"
                :collection="$responsibleFilterUsers"
                value-field="id"
                label-field="name"
                default="Todos os responsaveis"
            />
        </div>
    </x-page.filter>

    <section class="mb-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Setores</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalOrganizations }}</p>
            <p class="mt-1 text-xs text-slate-500">Total no recorte atual.</p>
        </article>

        <article class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700">Ativos</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">{{ $activeOrganizations }}</p>
            <p class="mt-1 text-xs text-emerald-700/80">Setores em uso no organograma.</p>
        </article>

        <article class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-700">Com Responsavel</p>
            <p class="mt-2 text-3xl font-bold text-sky-900">{{ $withResponsible }}</p>
            <p class="mt-1 text-xs text-sky-700/80">Setores com referencia definida.</p>
        </article>

        <article class="rounded-2xl border border-amber-200 bg-amber-50/60 p-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700">Com Usuarios</p>
            <p class="mt-2 text-3xl font-bold text-amber-900">{{ $withUsers }}</p>
            <p class="mt-1 text-xs text-amber-700/80">Setores com pessoas associadas.</p>
        </article>
    </section>

    <x-page.table>
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Setor" />
                <x-page.table-th class="w-72" value="Responsavel" />
                <x-page.table-th class="text-center w-28" value="Status" />
                <x-page.table-th class="text-center w-32" value="Acoes" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($organizationCharts as $organizationChart)
                <tr class="transition-colors hover:bg-slate-50/80">
                    <x-page.table-td>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex shrink-0 items-center rounded-md bg-slate-900 px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-white">
                                    {{ $organizationChart->acronym }}
                                </span>
                                <p class="truncate text-sm font-semibold text-slate-900" title="{{ $organizationChart->acronym }} - {{ $organizationChart->title }}">
                                    {{ $organizationChart->acronym }} - {{ $organizationChart->title }}
                                </p>
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                                <span>Nivel {{ $organizationChart->number_hierarchy }}</span>
                                <span>{{ $organizationChart->users_count }} {{ $organizationChart->users_count === 1 ? 'usuario' : 'usuarios' }}</span>
                                <span>{{ $organizationChart->number_hierarchy === 0 ? 'Setor raiz da estrutura.' : 'Setor interno do organograma.' }}</span>
                            </div>
                        </div>
                    </x-page.table-td>

                    <x-page.table-td>
                        @if ($organizationChart->responsibleUser)
                            <div class="flex items-center gap-3">
                                <div class="size-10 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                                    <img
                                        src="{{ $organizationChart->responsibleUser->avatar ? asset('storage/' . $organizationChart->responsibleUser->avatar) : asset('asset/img/favicon-infosaude-150-150.png') }}"
                                        alt="{{ $organizationChart->responsibleUser->name }}"
                                        class="h-full w-full object-cover"
                                        loading="lazy"
                                    />
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900" title="{{ $organizationChart->responsibleUser->name }}">
                                        {{ $organizationChart->responsibleUser->name }}
                                    </p>
                                    <p class="truncate text-xs text-slate-500" title="{{ $organizationChart->responsibleUser->email }}">
                                        {{ $organizationChart->responsibleUser->email }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-500">
                                Responsavel nao definido.
                            </div>
                        @endif
                    </x-page.table-td>

                    <x-page.table-td class="text-center">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $organizationChart->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $organizationChart->is_active ? 'Ativo' : 'Desativado' }}
                        </span>
                    </x-page.table-td>

                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            <x-button wire:click="status({{ $organizationChart->id }})" icon="fa-solid fa-toggle-on" title="Alterar status do setor" variant="ghost" />
                            <x-button wire:click="openUsers({{ $organizationChart->id }})" icon="fa-solid fa-user-group" title="Associar usuarios ao setor" variant="ghost" />
                            <x-button wire:click="edit({{ $organizationChart->id }})" icon="fa-solid fa-pen" title="Editar setor" variant="ghost" />
                        </div>
                    </x-page.table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center">
                        <div class="mx-auto max-w-md rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-8">
                            <p class="text-sm font-semibold text-slate-700">Nenhum setor encontrado.</p>
                            <p class="mt-1 text-xs text-slate-500">Ajuste os filtros para ampliar a busca no organograma.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-slot>
    </x-page.table>

    <x-modal :show="$showModal" wire:key="organitation-chart-modal">
        @if ($modalKey === 'modal-form-create-organitation-chart')
            <x-slot name="header">
                <h2 class="text-sm font-semibold uppercase text-gray-700">Cadastrar Setor</h2>
            </x-slot>

            <form wire:submit.prevent="store" class="space-y-4">
                @include('livewire.organization.organization-chart._partials.organization-chart-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Salvar" />
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-form-edit-organitation-chart')
            <x-slot name="header">
                <h2 class="text-sm font-semibold uppercase text-gray-700">Editar Setor</h2>
            </x-slot>

            <form wire:submit.prevent="update" class="space-y-4">
                @include('livewire.organization.organization-chart._partials.organization-chart-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky_solid" fullWidth="true" />
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-organization-users')
            <x-slot name="header">
                <h2 class="text-sm font-semibold uppercase text-gray-700">Associar Usuarios ao Setor</h2>
            </x-slot>

            <div class="space-y-4">
                <div>
                    <x-form.label value="Buscar usuario" />
                    <x-form.input wire:model.live.debounce.300ms="userSearch" placeholder="Digite um nome..." />
                </div>

                <div class="max-h-72 space-y-2 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50/40 p-3">
                    @forelse ($users as $user)
                        <label
                            class="flex items-center gap-3 rounded-lg bg-white px-3 py-2 text-sm text-gray-700 shadow-sm"
                            wire:key="organization-user-{{ $user->id }}"
                        >
                            <input
                                type="checkbox"
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                wire:model="organizationUserIds"
                                value="{{ $user->id }}"
                            />
                            <div class="flex flex-col">
                                <span class="font-medium">{{ $user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $user->email }}</span>
                            </div>
                        </label>
                    @empty
                        <div class="text-center text-sm text-gray-400">Nenhum usuario encontrado.</div>
                    @endforelse
                </div>

                @error('organizationUserIds.*')
                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end gap-2 pt-4">
                    <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" type="button" />
                    <x-button type="button" text="Salvar associacao" icon="fa-solid fa-check" wire:click="saveUsers" />
                </div>
            </div>
        @endif
    </x-modal>
</div>
