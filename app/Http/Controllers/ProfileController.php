<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Http\Requests\Profile\ProfilePasswordUpdateRequest;
use App\Http\Requests\Profile\ProfileUpdateRequest;
use App\Mail\Profile\UserPasswordResetedMail;
use App\Models\Administration\User\Gender;
use App\Models\Administration\User\User;
use App\Models\Configuration\Occupation\Occupation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        $genders = Gender::where('status', true)->get();
        $occupations = Occupation::where('status', true)->get();

        ActivityLogHelper::action('Visualizou a página de edição do seu perfil');

        return view('profile.profile_edit', compact('genders', 'occupations'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = User::find(Auth::user()->id);
        $user->update($request->validated());

        ActivityLogHelper::action('Atualizou seus dados do perfil');

        return redirect()->route('profile.edit')->with('success', 'Alteração de dados realizada com sucesso');
    }

    /**
     * Display the user's profile form.
     */
    public function password(): View
    {
        ActivityLogHelper::action('Visualizou a página de alteração de senha');

        return view('profile.profile_password');
    }

    /**
     * Update the user's profile information.
     */
    public function passwordUpdate(ProfilePasswordUpdateRequest $request): RedirectResponse
    {
        $request['password_default'] = false;
        
        $user = User::find(Auth::user()->id);
        $user->update($request->only('password', 'password_default'));

        // Envia o e-mail de aviso
        Mail::to($user->email)->send(new UserPasswordResetedMail($user));

        ActivityLogHelper::action('Alterou a senha de acesso');

        return redirect()->route('profile.edit')->with('success', 'Alteração de senha realizada com sucesso');
    }
}
