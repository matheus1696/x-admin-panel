<div>    
    <x-page.filter title="Filtros de Logs do Sistema">
        {{-- Filtros Básicos --}}
        <x-slot name="showBasic">
            <!-- Data Início -->
            <div class="md:col-span-2">
                <x-form.label value="Data Início" />
                <x-form.input type="date" wire:model.live="dateStart" max="{{ now()->format('Y-m-d') }}" />
            </div>

            <!-- Data Fim -->
            <div class="md:col-span-2">
                <x-form.label value="Data Fim" />
                <x-form.input type="date" wire:model.live="dateEnd" max="{{ now()->format('Y-m-d') }}" />
            </div>

            <!-- Endereço IP -->
            <div class="md:col-span-2">
                <x-form.label value="Endereço IP" />
                <x-form.input type="text" placeholder="Buscar por IP..." wire:model.live.debounce.500ms="ip" />
            </div>

            <!-- Usuário -->
            <div class="md:col-span-2">
                <x-form.label value="Usuário" />
                <x-form.select-livewire wire:model.live="user" name="user" :collection="$users" value-field="uuid" label-field="name" default="Todos os usuários" />
            </div>

            <!-- URL -->
            <div class="md:col-span-2">
                <x-form.label value="URL" />
                <x-form.input type="text" placeholder="Buscar por URL..." wire:model.live.debounce.500ms="url" />
            </div>

            <!-- Itens por página -->
            <div class="md:col-span-2">
                <x-form.label value="Itens por página" />
                <x-form.select-livewire
                    wire:model.live="perPage"
                    name="perPage"
                    :options="[
                        ['value' => 10, 'label' => '10'],
                        ['value' => 25, 'label' => '25'],
                        ['value' => 50, 'label' => '50'],
                        ['value' => 100, 'label' => '100']
                    ]"
                    default="Selecione a quantidade de itens"
                />
            </div>
        </x-slot>
    </x-page.filter>

    <x-page.table :pagination="$logs">
        <x-slot name="thead">
            <tr>
                <x-page.table-th class="w-32 truncate" value="Data e Hora" />
                <x-page.table-th class="w-32 truncate hidden lg:table-cell" value="Endereço IP" />
                <x-page.table-th class="w-32 hidden lg:table-cell" value="Metodo" />
                <x-page.table-th class="w-32" value="Usuário" />
                <x-page.table-th value="Descrição" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($logs as $log)
                <tr>
                    <x-page.table-td class="truncate" :value="$log->created_at->format('d/m/Y H:i:s')" title="{{ $log->created_at->format('d/m/Y H:i:s') }}"/>
                    <x-page.table-td class="truncate hidden lg:table-cell" :value="$log->ip_address" />
                    <x-page.table-td class="truncate hidden lg:table-cell" :value="$log->method" />
                    <x-page.table-td class="truncate" :value="$log->User->name ?? ''" title="{{ $log->User->name ?? '' }}"/>
                    <x-page.table-td class="truncate" :value="$log->description ?? ''" title="{{ $log->description ?? '' }}"/>
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
