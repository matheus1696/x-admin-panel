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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $genders = Gender::where('is_active', true)->get();
        $occupations = Occupation::where('is_active', true)->get();

        ActivityLogHelper::action('Visualizou a pagina de edicao do seu perfil');

        return view('profile.profile_edit', compact('genders', 'occupations'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = User::find(Auth::user()->id);
        $validated = $request->validated();

        if (($validated['email'] ?? null) !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->fill($validated);
        $user->save();

        ActivityLogHelper::action('Atualizou seus dados do perfil');

        return redirect('/profile')->with('success', 'Alteracao de dados realizada com sucesso');
    }

    public function password(): View
    {
        ActivityLogHelper::action('Visualizou a pagina de alteracao de senha');

        return view('profile.profile_password');
    }

    public function passwordUpdate(ProfilePasswordUpdateRequest $request): RedirectResponse
    {
        $request['password_default'] = false;

        $user = User::find(Auth::user()->id);
        $user->update($request->only('password', 'password_default'));

        Mail::to($user->email)->send(new UserPasswordResetedMail($user));

        ActivityLogHelper::action('Alterou a senha de acesso');

        return redirect('/profile')->with('success', 'Alteracao de senha realizada com sucesso');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
