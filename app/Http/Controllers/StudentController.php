<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        return view('students.index', [
            'students' => Student::with('schoolClass')->latest()->get(),
            'classes' => SchoolClass::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'matricule' => ['required', 'string', 'max:80', 'unique:students,matricule'],
            'full_name' => ['required', 'string', 'max:255'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
        ]);

        Student::create($data);

        return back()->with('success', 'Élève créé.');
    }
}
