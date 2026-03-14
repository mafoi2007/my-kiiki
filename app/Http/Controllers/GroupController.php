<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        return view('groups.index', ['groups' => Group::latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);

        if (Group::where('name', $data['name'])->exists()) {
            return back()->withErrors(['name' => 'Ce groupe a déjà été créé.'])->withInput();
        }

        Group::create($data);

        return back()->with('success', 'Groupe créé.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $group->delete();

        return back()->with('success', 'Groupe supprimé.');
    }
}