<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $classes = SchoolClass::query()
            ->withCount('students')
            ->whereHas('students')
            ->orderBy('name')
            ->get();

        $selectedClass = null;
        $studentsInSelectedClass = collect();

        $selectedClassId = request()->integer('class_id');
        if ($selectedClassId > 0) {
            $selectedClass = SchoolClass::query()
                ->whereHas('students')
                ->find($selectedClassId);

            if ($selectedClass !== null) {
                $studentsInSelectedClass = Student::with('schoolClass')
                    ->where('school_class_id', $selectedClass->id)
                    ->orderBy('full_name')
                    ->get();
            }
        }

        $searchTerm = trim((string) request('search'));
        $searchResults = collect();

        if ($searchTerm !== '') {
            $searchResults = Student::with('schoolClass')
                ->where(function ($query) use ($searchTerm): void {
                    $query
                        ->where('full_name', 'like', "%{$searchTerm}%")
                        ->orWhere('matricule', 'like', "%{$searchTerm}%");
                })
                ->orderBy('full_name')
                ->limit(20)
                ->get();
        }

        $selectedStudentId = request()->integer('student_id');
        $selectedStudent = $selectedStudentId > 0
            ? Student::with('schoolClass')->find($selectedStudentId)
            : null;

        return view('students.index', [
            'classes' => SchoolClass::orderBy('name')->get(),
            'classesWithStudents' => $classes,
            'selectedClass' => $selectedClass,
            'studentsInSelectedClass' => $studentsInSelectedClass,
            'searchTerm' => $searchTerm,
            'searchResults' => $searchResults,
            'selectedStudent' => $selectedStudent,

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

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'matricule' => ['required', 'string', 'max:80', Rule::unique('students', 'matricule')->ignore($student->id)],
            'full_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:N,R'],
            'sex' => ['required', 'in:M,F'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
        ]);

        $data['active'] = (bool) ($data['active'] ?? false);

        $student->update($data);

        return to_route('students.index', ['student_id' => $student->id])->with('success', 'Élève mis à jour.');
    }

    public function moveClass(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'from_school_class_id' => ['required', 'exists:school_classes,id'],
            'to_school_class_id' => ['required', 'exists:school_classes,id', 'different:from_school_class_id'],
        ]);

        if ((int) $student->school_class_id !== (int) $data['from_school_class_id']) {
            return back()->withErrors([
                'from_school_class_id' => 'La classe de départ ne correspond pas à la classe actuelle de cet élève.',
            ]);
        }

        $student->update(['school_class_id' => $data['to_school_class_id']]);

        return to_route('students.index', ['student_id' => $student->id])->with('success', 'Classe de l\'élève mise à jour.');
    }

    public function classStudentsPdf(SchoolClass $class): View
    {
        $class->load(['students' => fn ($query) => $query->orderBy('full_name')]);

        return view('students.class-pdf', [
            'class' => $class,
            'students' => $class->students,
        ]);
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