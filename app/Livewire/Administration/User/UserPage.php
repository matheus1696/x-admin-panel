<?php

namespace App\Livewire\Administration\User;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\User\Gender;
use App\Models\Configuration\Occupation\Occupation;
use App\Services\Administration\User\UserService;
use App\Validation\Administration\User\UserRules;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserPage extends Component
{
    use WithPagination, WithFlashMessage, Modal;

    protected UserService $userService;

    /** Filters */
    public array $filters = [
        'name' => '',
        'email' => '',
        'status' => 'all',
        'sort' => 'name_asc',
        'perPage' => 25,        
    ];

    /** Form */
    public ?int $userId = null;
    public ?string $matriculation = null;
    public ?string $cpf = null;
    public string $name = '';
    public string $email = '';
    public ?int $occupation_id   = null;
    public ?string $birth_date = null;
    public ?int $gender_id = null;
    public ?string $phone_personal = null;
    public ?string $phone_work = null;
    public array $permissions = [];

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->reset(['userId', 'matriculation', 'cpf', 'name', 'email', 'occupation_id', 'birth_date', 'gender_id', 'phone_personal', 'phone_work', 'permissions']);
    }

    /* CREATE */
    public function create(): void
    {
        $this->resetForm();
        $this->openModal('modal-form-create-user');
    }

    public function store(): void
    {
        $data = $this->validate(UserRules::store());
        $this->userService->store($data);
        $this->resetForm();
        $this->flashSuccess('Usuário adicionado com sucesso.');
        $this->closeModal();
    }
    
    /* EDIT */
    public function edit(int $id): void
    {
        $user = $this->userService->find($id);

        $this->userId          = $user->id;
        $this->matriculation   = $user->matriculation;
        $this->cpf             = $user->cpf;
        $this->name            = $user->name;
        $this->email           = $user->email;
        $this->occupation_id   = $user->occupation_id;
        $this->birth_date      = $user->birth_date;
        $this->gender_id       = $user->gender_id;
        $this->phone_personal  = $user->phone_personal;
        $this->phone_work      = $user->phone_work;

        $this->openModal('modal-form-edit-user');
    }

    public function update(): void
    {
        $data = $this->validate(UserRules::update($this->userId));
        $this->userService->update($this->userId, $data);
        $this->resetForm();
        $this->flashSuccess('Usuário atualizado com sucesso.');
        $this->closeModal();
    }
    
    /* EDIT */
    public function permission(int $id): void
    {
        $user = $this->userService->find($id);

        $this->userId          = $user->id;
        $this->name            = $user->name;
        $this->email           = $user->email;
        $this->permissions     = $user->getPermissionNames()->toArray();

        $this->openModal('modal-form-user-permission');
    }

    public function permissionUpdate(): void
    {
        $data = $this->validate(UserRules::permissionUpdate());
        $this->userService->permissionUpdate($this->userId, ['permissions' => $this->permissions]);
        $this->resetForm();
        $this->flashSuccess('Permissões atualizadas com sucesso.');
        $this->closeModal();
    }

    public function status(int $id): void
    {
        $this->userService->status($id);
        $this->flashSuccess('Status do usuário foi alterado.');
    }

    public function render(): View
    {
        $users = $this->userService->index($this->filters);

        return view('livewire.administration.user.user-page', [
            'users' => $users,
            'occupations' => Occupation::where('status', true)->orderBy('title')->get(),
            'genders' => Gender::where('status', true)->orderBy('title')->get(),
            'roles' => Role::where('name', '!=', 'super-admin')->orderBy('type')->with('permissions')->get(),
        ])->layout('layouts.app');
    }
}
