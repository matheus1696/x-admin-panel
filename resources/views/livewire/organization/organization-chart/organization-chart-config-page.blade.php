<div>
    
    <!-- Flash Message -->

    <!-- Header -->
    <x-page.header  title="Organograma" subtitle="Organograma da Secretária de Saúde de Caruaru" icon="fa-solid fa-sitemap">
        <x-slot name="button">
            <x-button text="Novo Setor" icon="fa-solid fa-plus" wire:click="create" />
        </x-slot>
    </x-page.header>

    <!-- Filter -->
    <x-page.filter title="Filtros">
        {{-- Sigla do Setor --}}
        <div class="col-span-12 md:col-span-2">
            <x-form.label value="Sigla" />
            <x-form.input wire:model.live.debounce.500ms="filters.acronym" placeholder="Buscar por sigla..." />
        </div>

        {{-- Setor --}}
        <div class="col-span-12 md:col-span-7">
            <x-form.label value="Setor" />
            <x-form.input wire:model.live.debounce.500ms="filters.filter" placeholder="Buscar por setor..." />
        </div>

        {{-- Status --}}
        <div class="col-span-6 md:col-span-3">
            <x-form.label value="Status" />
            <x-form.select-livewire wire:model.live="filters.status" name="filters.status"
                :options="[
                    ['value' => 'all', 'label' => 'Todos'],
                    ['value' => 'true', 'label' => 'Ativo'],
                    ['value' => 'false', 'label' => 'Desativado'],
                ]"
            />
        </div>

        {{-- Responsável --}}
        <div class="col-span-12 md:col-span-4">
            <x-form.label value="Responsável" />
            <x-form.select-livewire
                wire:model.live="filters.responsible_user_id"
                name="filters.responsible_user_id"
                :collection="$responsibleFilterUsers"
                value-field="id"
                label-field="name"
                default="Todos os responsáveis"
            />
        </div>
    </x-page.filter>

    <!-- Table -->
    <x-page.table>
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Título" />
                <x-page.table-th class="text-center w-48" value="Responsável" />
                <x-page.table-th class="text-center w-20" value="Status" />
                <x-page.table-th class="text-center w-20" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($organizationCharts as $organizationChart)
                <tr>
                    <x-page.table-td>
                        <div class="w-48 md:w-full truncate" title="{{ $organizationChart->acronym }} - {{ $organizationChart->title }}">
                            @for ($i = 0; $i < $organizationChart->number_hierarchy; $i++)
                                <span><i class="fa-solid fa-angle-right"></i></span>
                            @endfor                                       
                            <span class="pl-1">{{ $organizationChart->acronym }} - {{ $organizationChart->title }}</span>
                            <span class="ml-2 inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-xs font-medium text-sky-700">
                                {{ $organizationChart->users_count }} {{ $organizationChart->users_count === 1 ? 'usuário' : 'usuários' }}
                            </span>
                        </div>
                    </x-page.table-td>

                    <x-page.table-td class="text-center">
                        <div class="flex items-center justify-center gap-2 text-xs text-slate-700">
                            @if ($organizationChart->responsibleUser)
                                <div class="size-6 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                                    <img
                                        src="{{ $organizationChart->responsibleUser->avatar ? asset('storage/' . $organizationChart->responsibleUser->avatar) : 'https://tse4.mm.bing.net/th/id/OIP.dDKYQqVBsG1tIt2uJzEJHwHaHa?rs=1&pid=ImgDetMain&o=7&rm=3' }}"
                                        alt="{{ $organizationChart->responsibleUser->name }}"
                                        class="h-full w-full object-cover"
                                        loading="lazy"
                                    />
                                </div>
                                <span class="truncate max-w-[160px]" title="{{ $organizationChart->responsibleUser->name }}">
                                    {{ $organizationChart->responsibleUser->name }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">Não definido</span>
                            @endif
                        </div>
                    </x-page.table-td>

                    <x-page.table-td class="text-center">
                        <div class="text-xs font-medium rounded-full py-0.5 px-1 {{ $organizationChart->is_active ? 'bg-green-300 text-green-700' : 'bg-red-300 text-red-700' }}">
                            {{ $organizationChart->is_active ? 'Ativo' : 'Desativado' }}
                        </div>
                    </x-page.table-td>

                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            <x-button wire:click="status({{ $organizationChart->id }})" icon="fa-solid fa-toggle-on" title="Alterar Status" variant="green_text" />
                            <x-button wire:click="openUsers({{ $organizationChart->id }})" icon="fa-solid fa-user-group" title="Associar Usuários" variant="green_text" />
                            <x-button wire:click="edit({{ $organizationChart->id }})" icon="fa-solid fa-pen" title="Editar Tipo de Tarefa" variant="green_text" />
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>

    <!-- Modal -->
    <x-modal :show="$showModal" wire:key="organitation-chart-modal">
        @if ($modalKey === 'modal-form-create-organitation-chart')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Cadastrar Setor</h2>
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
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Editar Setor</h2>
            </x-slot>

            <form wire:submit.prevent="update" class="space-y-4">
                @include('livewire.organization.organization-chart._partials.organization-chart-form')
                <div class="flex justify-end gap-2 pt-4">
                    <x-button type="submit" text="Atualizar" variant="sky_solid" fullWidth="true"/>
                </div>
            </form>
        @endif

        @if ($modalKey === 'modal-organization-users')
            <x-slot name="header">
                <h2 class="text-sm font-semibold text-gray-700 uppercase">Associar Usuários ao Setor</h2>
            </x-slot>

            <div class="space-y-4">
                <div>
                    <x-form.label value="Buscar usuário" />
                    <x-form.input wire:model.live.debounce.300ms="userSearch" placeholder="Digite um nome..." />
                </div>

                <div class="max-h-72 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50/40 p-3 space-y-2">
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
                        <div class="text-sm text-gray-400 text-center">Nenhum usuário encontrado.</div>
                    @endforelse
                </div>

                @error('organizationUserIds.*')
                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end gap-2 pt-4">
                    <x-button text="Cancelar" variant="gray_outline" wire:click="closeModal" type="button" />
                    <x-button type="button" text="Salvar associação" icon="fa-solid fa-check" wire:click="saveUsers" />
                </div>
            </div>
        @endif
    </x-modal>

</div>
