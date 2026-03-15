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
        return view('groups.index', ['groups' => Group::withCount('classes')->latest()->get()]);
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

    public function update(Request $request, Group $group): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);

        if (Group::where('name', $data['name'])->whereKeyNot($group->id)->exists()) {
            return back()->withErrors(['name' => 'Ce groupe a déjà été créé.'])->withInput();
        }

        $group->update($data);

        return back()->with('success', 'Groupe mis à jour.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        if ($group->classes()->exists()) {
            return back()->withErrors(['group' => 'Suppression impossible : ce groupe est déjà attribué à une matière dans une classe.']);
        }

        $group->delete();

        return back()->with('success', 'Groupe supprimé.');
    }
}