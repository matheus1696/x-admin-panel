<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\UserPermissonRequest;
use App\Http\Requests\Admin\Manage\UserStoreRequest;
use App\Http\Requests\Admin\Manage\UserUpdateRequest;
use App\Mail\Admin\Manage\UserCreateMail;
use App\Mail\Admin\Manage\UserResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        ActivityLogHelper::action('Página de gerenciamento de usuários');

        return view('admin.manage.users.user_index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        ActivityLogHelper::action('Página do formulário de criação do usuários');

        return view('admin.manage.users.user_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        //
        $password = 'Senha123';
        $request['password'] = $password;
        $user = User::create($request->all())->assignRole('user');

        // Envia o e-mail de boas-vindas
        Mail::to($user->email)->send(new UserCreateMail($user, $password));

        ActivityLogHelper::action('Realizada criação do usuário: '. $user->name);

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
        ActivityLogHelper::action('Página de edição do usuário '. $user->name);

        return view('admin.manage.users.user_edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        //
        $user->update($request->only('matriculation', 'cpf', 'name', 'birth_date', 'gender', 'phone_personal', 'phone_work', 'status'));

        ActivityLogHelper::action('Alteração dos dados do usuário '. $user->name);

        return redirect()
            ->back()
            ->with('success', 'Informações do usuário atualizadas com sucesso!');
    }

    public function permission(UserPermissonRequest $request, User $user)
    {
        $permissions = $request->input('permissions', []);

        // Sincroniza as permissões do usuário
        $user->syncPermissions($permissions);

        // Logout de todas as sessões do usuário
        DB::table('sessions')->where('user_id', $user->id)->delete();

        //
        ActivityLogHelper::action('Alteração das permissões do usuário '. $user->name);

        // Retorna com mensagem de sucesso
        return redirect()
            ->back()
            ->with('success', 'Permissões do usuário atualizadas com sucesso!');
    }

    public function password(User $user)
    {
        $password = 'Senha123';
        $user->password = Hash::make($password);
        $user->password_default = true;
        $user->save();

        // Envia o e-mail de aviso
        Mail::to($user->email)->send(new UserResetPasswordMail($user, $password));

        // Logout de todas as sessões do usuário
        DB::table('sessions')->where('user_id', $user->id)->delete();

        //
        ActivityLogHelper::action('Alteração da senha do usuário '. $user->name);

        // Retorna com mensagem de sucesso
        return redirect()
            ->back()
            ->with('success', 'Redefinição de Senha do usuário realizada com sucesso!');
    }
}
