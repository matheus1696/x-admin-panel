<div>
    <div>
        {{ $name }}
        {{ $email }}
    </div>
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
                                    wire:model.defer="permissions"
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

</div>
