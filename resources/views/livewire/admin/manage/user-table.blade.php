<div>
    <x-page.filter title="Filtros de Usuários">
        {{-- Filtros Básicos --}}
        <x-slot name="showBasic">
            <div class="md:col-span-4">
                <x-form.label value="Nome" />
                <x-form.input type="text" placeholder="Buscar por nome..." wire:model.live.debounce.500ms="name"/>
            </div>

            <div class="md:col-span-4">
                <x-form.label value="Email" />
                <x-form.input type="email" placeholder="Buscar por e-mail..." wire:model.live.debounce.500ms="email"/>
            </div>

            <div class="md:col-span-2">
                <x-form.label value="Status" />
                <x-form.select wire:model.live="status">
                    <option value="">Todos</option>
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                </x-form.select>
            </div>

            <div class="md:col-span-2">
                <x-form.label value="Itens por página" />
                <x-form.select wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </x-form.select>
            </div>
        </x-slot>

        {{-- Filtros Avançados --}}
        <x-slot name="showAdvanced">
            <div class="md:col-span-2">
                <x-form.label value="Ordenar por" />
                <x-form.select wire:model.live="sort">
                    <option value="name_asc">Nome (A–Z)</option>
                    <option value="name_desc">Nome (Z–A)</option>
                    <option value="email_asc">Email (A–Z)</option>
                    <option value="email_desc">Email (Z–A)</option>
                </x-form.select>
            </div>
        </x-slot>
    </x-page.filter>

    <x-page.table :pagination="$users">
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Usuário" />
                <x-page.table-th class="hidden lg:table-cell" value="Email" />
                <x-page.table-th class="w-36 text-center" value="Status" />
                <x-page.table-th class="w-36 text-center" value="Ações" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($users as $user)
                <tr>
                    <x-page.table-td>
                        <div class="flex items-center gap-2">
                            <span
                                class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $user->name }}</span>
                            @if ($user->id === auth()->id())
                                <span
                                    class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full font-medium">Você</span>
                            @endif
                        </div>
                    </x-page.table-td>
                    <x-page.table-td class="hidden lg:table-cell" :value="$user->email" />
                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            @if ($user->status)
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-green-700">Ativo</span>
                            @else
                                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-red-700">Inativo</span>
                            @endif
                        </div>
                    </x-page.table-td>
                    <x-page.table-td>
                        <div class="flex items-center justify-center gap-2">
                            @can('edit-users')
                                <x-button.btn-table color="blue" title="Editar Usuário">
                                    <a href="{{ route('users.edit', $user) }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </x-button.btn-table>
                            @endcan
                        </div>
                    </x-page.table-td>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
