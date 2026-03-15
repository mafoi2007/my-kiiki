<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
     public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $nestedQuery) use ($search): void {
                    $nestedQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('login', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->orderBy('login')
            ->get();

        return view('users.index', [
            'users' => $users,
            'roles' => User::ROLES,
            'search' => $search,
        ]);
    }

    public function show(User $user): View
    {
        return view('users.show', ['user' => $user]);
    }

    public function edit(User $user): View
    {
        return view('users.edit', ['user' => $user, 'roles' => User::ROLES]);
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

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(User::ROLES)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'must_change_password' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'name' => mb_strtoupper(trim($data['name'])),
            'role' => $data['role'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'must_change_password' => (bool) ($data['must_change_password'] ?? false),
        ]);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour: ' . $user->login . '.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->assignments()->exists()) {
            return back()->withErrors([
                'user_delete' => 'Suppression impossible : cet utilisateur est déjà affecté à une matière dans une classe.',
            ]);
        }

        $login = $user->login;
        $user->delete();

        return back()->with('success', 'Utilisateur supprimé: ' . $login . '.');
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
}
