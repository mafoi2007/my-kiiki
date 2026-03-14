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
            'birth_date' => ['required', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'status' => ['required', 'in:N,R'],
            'sex' => ['required', 'in:M,F'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'active' => ['nullable', 'boolean'],
        ]);

        $data['school_matricule'] = $this->generateSchoolMatricule();
        $data['active'] = (bool) ($data['active'] ?? true);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('students', 'public');
        }

        unset($data['photo']);

        Student::create($data);

        return back()->with('success', 'Élève créé.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->update(['active' => false]);

        return back()->with('success', 'Élève désactivé.');
    }

    private function generateSchoolMatricule(): string
    {
        $year = now()->format('y');
        $prefix = "NRA{$year}-6";

        $lastForYear = Student::query()
            ->where('school_matricule', 'like', "{$prefix}%")
            ->orderByDesc('school_matricule')
            ->value('school_matricule');

        $sequence = 1;

        if ($lastForYear !== null && preg_match('/^(?:NRA\d{2}-6)(\d{4})$/', $lastForYear, $matches) === 1) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return sprintf('%s%04d', $prefix, $sequence);
    }
}