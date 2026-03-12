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
            'password' => ['required', 'string', 'min:6'],
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return back()->with('success', 'Utilisateur créé.');
    }
}
