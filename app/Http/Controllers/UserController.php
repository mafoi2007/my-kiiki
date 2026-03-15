<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['users' => User::latest()->get(), 'roles' => User::ROLES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'login' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:users,login'],
            'role' => ['required', Rule::in(User::ROLES)],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $data['name'] = mb_strtoupper(trim($data['name']));
        
        $defaultPassword = $data['login'] . '@1234';
        $rawPassword = $data['password'] ?: $defaultPassword;

        if (($data['password'] ?? null) === null) {
            unset($data['password']);
        }

        $data['password'] = Hash::make($rawPassword);
        $data['must_change_password'] = in_array($data['role'], [
            'chef_etablissement',
            'censeur',
            'surveillant_general',
            'econome',
            'enseignant',
        ], true);

        User::create($data);

        return back()->with('success', 'Utilisateur créé. Mot de passe initial: ' . $rawPassword);
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $defaultPassword = $user->defaultPassword();

        $user->forceFill([
            'password' => Hash::make($defaultPassword),
            'must_change_password' => true,
        ])->save();

        return back()->with('success', 'Mot de passe réinitialisé pour ' . $user->login . ' : ' . $defaultPassword); 
    }

    public function updateName(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => mb_strtoupper(trim($data['name'])),
        ]);

        return back()->with('success', 'Nom mis à jour pour ' . $user->login . '.');
    }
}
