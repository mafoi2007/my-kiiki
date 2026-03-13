<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LevelController extends Controller
{
    public function index(): View
    {
        return view('levels.index', ['levels' => Level::withCount('classes')->latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:levels,name'],
        ]);

        Level::create($data);

        return back()->with('success', 'Niveau créé.');
    }

    public function destroy(Level $level): RedirectResponse
    {
        if ($level->classes()->exists()) {
            return back()->withErrors(['level' => 'Impossible de supprimer ce niveau car des classes y sont rattachées.']);
        }

        $level->delete();

        return back()->with('success', 'Niveau supprimé.');
    }
}
