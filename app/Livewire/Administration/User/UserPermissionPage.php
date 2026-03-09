<?php

namespace App\Livewire\Administration\User;

use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\User\User;
use App\Services\Administration\User\UserService;
use App\Validation\Administration\User\UserRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserPermissionPage extends Component
{
    use WithFlashMessage;

    protected UserService $userService;

    public User $user;

    /** @var array<string> */
    public array $selectedRoles = [];

    /** @var array<string> */
    public array $permissions = [];

    public string $permissionSearch = '';

    public bool $onlySelected = false;

    public $shadowUserId = null;

    public function boot(UserService $userService): void
    {
        $this->userService = $userService;
    }

    public function mount(int $id): void
    {
        Gate::authorize('administration.manage.users.permissions');

        $this->user = $this->userService->find($id);
        $this->selectedRoles = $this->user->roles->pluck('name')->values()->all();
        $this->permissions = $this->normalizeItems($this->user->getPermissionNames()->toArray());
    }

    public function copyFromShadowUser(): void
    {
        Gate::authorize('administration.manage.users.permissions');

        $shadowUserId = (int) ($this->shadowUserId ?? 0);

        if ($shadowUserId <= 0) {
            $this->addError('shadowUserId', 'Selecione um usuario sombra para copiar permissoes.');

            return;
        }

        if ($shadowUserId === (int) $this->user->id) {
            $this->flashWarning('Selecione um usuario diferente para copiar permissoes.');

            return;
        }

        $shadowUser = $this->userService->find($shadowUserId);

        $this->selectedRoles = $this->normalizeItems($shadowUser->roles->pluck('name')->all());
        $this->permissions = $this->normalizeItems($shadowUser->getPermissionNames()->toArray());

        $this->flashSuccess('Perfis e permissoes copiados do usuario sombra.');
    }

    public function updatedShadowUserId(): void
    {
        $this->resetErrorBag('shadowUserId');
    }

    public function selectVisiblePermissions(): void
    {
        $visible = $this->visiblePermissionNames();
        $this->permissions = $this->normalizeItems(array_merge($this->permissions, $visible));
    }

    public function clearVisiblePermissions(): void
    {
        $visibleLookup = array_flip($this->visiblePermissionNames());

        $this->permissions = $this->normalizeItems(array_values(array_filter(
            $this->permissions,
            fn (string $permission): bool => ! isset($visibleLookup[$permission])
        )));
    }

    public function save(): mixed
    {
        Gate::authorize('administration.manage.users.permissions');

        $data = $this->validate([
            'selectedRoles' => ['nullable', 'array'],
            'selectedRoles.*' => ['string', 'exists:roles,name'],
            ...UserRules::permissionUpdate(),
        ]);

        $this->userService->permissionUpdate($this->user->id, [
            'roles' => $data['selectedRoles'] ?? [],
            'permissions' => $data['permissions'] ?? [],
        ]);

        $this->flashSuccess('Perfis e permissoes atualizados com sucesso.');

        return redirect()->route('administration.manage.users');
    }

    public function render(): View
    {
        $allRoles = $this->loadRoles();
        $filteredRoles = $this->filterRoles($allRoles);
        $allPermissionNames = $allRoles
            ->pluck('permissions')
            ->flatten(1)
            ->pluck('name')
            ->unique()
            ->values();

        $selectedCount = collect($this->permissions)->intersect($allPermissionNames)->count();

        return view('livewire.administration.user.user-permission-page', [
            'allRoles' => $allRoles,
            'roles' => $filteredRoles,
            'shadowUsers' => User::query()
                ->where('id', '!=', $this->user->id)
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'selectedCount' => $selectedCount,
            'totalCount' => $allPermissionNames->count(),
        ])->layout('layouts.app');
    }

    private function loadRoles(): Collection
    {
        return Role::where('name', '!=', 'super-admin')
            ->orderBy('type')
            ->with('permissions')
            ->get();
    }

    private function filterRoles(Collection $roles): Collection
    {
        $search = mb_strtolower(trim($this->permissionSearch));
        $selectedLookup = array_flip($this->permissions);

        return $roles->map(function (Role $role) use ($search, $selectedLookup): Role {
            $permissions = $role->permissions
                ->filter(function ($permission) use ($search, $selectedLookup): bool {
                    if ($this->onlySelected && ! isset($selectedLookup[$permission->name])) {
                        return false;
                    }

                    if ($search === '') {
                        return true;
                    }

                    $label = mb_strtolower((string) ($permission->translation ?? ''));
                    $name = mb_strtolower((string) $permission->name);

                    return str_contains($label, $search) || str_contains($name, $search);
                })
                ->values();

            $role->setRelation('permissions', $permissions);

            return $role;
        })->filter(fn (Role $role): bool => $role->permissions->isNotEmpty())->values();
    }

    /**
     * @return array<string>
     */
    private function visiblePermissionNames(): array
    {
        return $this->filterRoles($this->loadRoles())
            ->pluck('permissions')
            ->flatten(1)
            ->pluck('name')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<mixed>  $items
     * @return array<string>
     */
    private function normalizeItems(array $items): array
    {
        return collect($items)
            ->map(fn ($item) => (string) $item)
            ->filter(fn (string $item): bool => $item !== '')
            ->unique()
            ->values()
            ->all();
    }
}
