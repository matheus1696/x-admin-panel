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
                <x-form.select wire:model.live="user">
                    <option value="">Todos Usuários</option>
                    @foreach ($users as $user)
                        <option value="{{$user->uuid}}">{{ $user->name }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <!-- URL -->
            <div class="md:col-span-2">
                <x-form.label value="URL" />
                <x-form.input type="text" placeholder="Buscar por URL..." wire:model.live.debounce.500ms="url" />
            </div>

            <!-- Itens por página -->
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
    </x-page.filter>

    <x-page.table :pagination="$logs">
        <x-slot name="thead">
            <tr>
                <x-page.table-th value="Data e Hora" />
                <x-page.table-th value="Endereço IP" />
                <x-page.table-th value="Metodo" />
                <x-page.table-th value="Url" />
                <x-page.table-th value="Usuário" />
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($logs as $log)
                <tr>
                    <x-page.table-td :value="$log->created_at->format('d/m/Y H:i:s')" />
                    <x-page.table-td :value="$log->ip_address" />
                    <x-page.table-td :value="$log->method" />
                    <x-page.table-td :value="$log->url" />
                    <x-page.table-td :value="$log->User->name ?? ''" />
                </tr>
            @endforeach
        </x-slot>
    </x-page.table>
</div>
