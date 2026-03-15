<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        return view('subjects.index', ['subjects' => Subject::withCount('classes')->latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);
        $normalizedName = mb_strtoupper(trim($data['name']));

        if (Subject::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])->exists()) {
            return back()->withErrors(['name' => 'Cette matière a déjà été créée.'])->withInput();
        }

        Subject::create(['name' => $normalizedName]);

        return back()->with('success', 'Matière créée.');
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);
        $normalizedName = mb_strtoupper(trim($data['name']));

        if (Subject::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])->whereKeyNot($subject->id)->exists()) {
            return back()->withErrors(['name' => 'Cette matière a déjà été créée.'])->withInput();
        }

        $subject->update(['name' => $normalizedName]);

        return back()->with('success', 'Matière mise à jour.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        if ($subject->classes()->exists()) {
            return back()->withErrors(['subject' => 'Suppression impossible : cette matière est déjà attribuée à une classe.']);
        }

        $subject->delete();

        return back()->with('success', 'Matière supprimée.');
    }
}
