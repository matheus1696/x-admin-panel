<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\UserPermissonRequest;
use App\Http\Requests\Admin\Manage\UserStoreRequest;
use App\Http\Requests\Admin\Manage\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.manage.users.user_index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.manage.users.user_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        //
        $request['password'] = Hash::make('senha123');
        User::create($request->all())->assignRole('user');

        return redirect()
            ->route('users.index')
            ->with('success', 'Cadastro de usuário realizada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
        return view('admin.manage.users.user_edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        //
        $user->update($request->only('matriculation', 'cpf', 'name', 'birth_date', 'gender', 'phone_personal', 'phone_work', 'status'));

        return redirect()
            ->back()
            ->with('success', 'Informações do usuário atualizadas com sucesso!');
    }

    public function permission(UserPermissonRequest $request, User $user)
    {
        $permissions = $request->input('permissions', []);

        // Sincroniza as permissões do usuário
        $user->syncPermissions($permissions);

        // Retorna com mensagem de sucesso
        return redirect()
            ->back()
            ->with('success', 'Permissões do usuário atualizadas com sucesso!');
    }

    public function password(User $user)
    {
        $user->password = Hash::make('Senha123');
        $user->save();

        // Retorna com mensagem de sucesso
        return redirect()
            ->back()
            ->with('success', 'Redefinição de Senha do usuário realizada com sucesso!');
    }
}
