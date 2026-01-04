<x-app-layout>
    <div class="w-full md:w-2/3 mx-auto space-y-6 mt-6">
        <x-page.header icon="fa-solid fa-users" title="Alterar Permissão do Usuário" subtitle="Atualize as permissões do usuário: {{ $user->name }}">
            <x-slot name="button">
                @can('user-view')
                    <x-button.btn-link href="{{ route('users.index') }}" value="Voltar para Lista" icon="fa-solid fa-rotate-left" />
                @endcan
            </x-slot>
        </x-page.header>

        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="{{ route('users.permissions.update', $user) }}" method="POST" class="p-6">
                    @csrf @method('PUT')

                    @foreach($roles as $role)
                        @if($role->permissions->count() > 0)
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-semibold text-gray-800">
                                        {{ $role->translation }}
                                    </h3>
                                    <span class="text-xs text-gray-500">
                                        {{ $role->permissions->count() }} permissões
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                                    @foreach($role->permissions as $permission)
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" 
                                                   id="role_{{ $role->id }}_permission_{{ $permission->id }}_{{ $user->uiid }}" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->name }}" 
                                                   class="hidden peer" 
                                                   {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            <label for="role_{{ $role->id }}_permission_{{ $permission->id }}_{{ $user->uiid }}" 
                                                   class="w-full text-[10px] font-bold text-gray-700 border rounded-lg cursor-pointer px-3 py-2 text-center hover:border-green-500 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 transition">
                                                {{ ucfirst($permission->translation) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-4">
                            @endif
                        @endif
                    @endforeach

                    <div class="w-full mt-4 pt-4">
                        <x-button.btn-submit class="w-full" value="Salvar Permissões" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>