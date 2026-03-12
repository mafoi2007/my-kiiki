<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        return view('classes.index', ['classes' => SchoolClass::latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:school_classes,name']]);
        SchoolClass::create($data);

        return back()->with('success', 'Classe créée.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return back()->with('success', 'Classe supprimée.');
    }
}
