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
        return view('subjects.index', ['subjects' => Subject::latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);

        if (Subject::where('name', $data['name'])->exists()) {
            return back()->withErrors(['name' => 'Cette matière a déjà été créée.'])->withInput();
        }

        Subject::create($data);

        return back()->with('success', 'Matière créée.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return back()->with('success', 'Matière supprimée.');
    }
}
