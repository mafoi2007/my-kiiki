<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Group;
use App\Models\TeacherAssignment;
use App\Models\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        return view('classes.index', [
            'classes' => SchoolClass::with(['level', 'students'])->withCount('subjects')->latest()->get(),
            'levels' => Level::orderBy('name')->get(),
        ]);
    }

    public function show(SchoolClass $class): View
    {
        $class->load([
            'level',
            'subjects',
            'teacherAssignments.teacher',
            'teacherAssignments.subject',
        ]);

        return view('classes.show', [
            'class' => $class,
            'subjects' => Subject::orderBy('name')->get(),
            'teachers' => User::where('role', 'enseignant')->orderBy('name')->get(),
            'groups' => Group::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:school_classes,code'],
            'level_id' => ['required', 'exists:levels,id'],
        ]);

        SchoolClass::create($data);

        return back()->with('success', 'Classe créée.');
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:school_classes,code,'.$class->id],
            'level_id' => ['required', 'exists:levels,id'],
        ]);

        $class->update($data);

        return back()->with('success', 'Classe mise à jour.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        if ($class->students()->exists()) {
            return back()->withErrors(['class' => 'Impossible de supprimer cette classe car elle contient déjà des élèves.']);
        }
    
        if ($class->subjects()->exists()) {
            return back()->withErrors(['class' => 'Impossible de supprimer cette classe car des matières y sont déjà attribuées.']);
        }

        $class->delete();

        return back()->with('success', 'Classe supprimée.');
    }
    public function assignSubject(Request $request, SchoolClass $class): RedirectResponse
    {
        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'coefficient' => ['required', 'integer', 'min:1', 'max:20'],
            'group_id' => ['required', 'exists:groups,id'],
        ]);
        
        if ($class->subjects()->where('subjects.id', $data['subject_id'])->exists()) {
            return back()->withErrors(['subject_id' => 'Cette matière a déjà été ajoutée à la classe.'])->withInput();
        }

        $class->subjects()->syncWithoutDetaching([
            $data['subject_id'] => [
                'coefficient' => $data['coefficient'],
                'group_id' => $data['group_id'],
            ],
        ]);

        return back()->with('success', 'Matière affectée à la classe.');
    }

    public function detachSubject(SchoolClass $class, Subject $subject): RedirectResponse
    {
        $class->teacherAssignments()->where('subject_id', $subject->id)->delete();
        $class->subjects()->detach($subject->id);

        return back()->with('success', 'Matière retirée de la classe.');
    }

    public function assignTeacher(Request $request, SchoolClass $class): RedirectResponse
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        if (! $class->subjects()->where('subjects.id', $data['subject_id'])->exists()) {
            return back()->withErrors(['subject_id' => 'Cette matière doit d\'abord être affectée à la classe.'])->withInput();
        }

        $teacher = User::findOrFail($data['teacher_id']);
        if ($teacher->role !== 'enseignant') {
            return back()->withErrors(['teacher_id' => 'L\'utilisateur sélectionné n\'est pas un enseignant.'])->withInput();
        }

        TeacherAssignment::updateOrCreate(
            [
                'school_class_id' => $class->id,
                'subject_id' => $data['subject_id'],
            ],
            ['teacher_id' => $data['teacher_id']]
        );

        return back()->with('success', 'Enseignant assigné à la matière.');
    }

    public function teachersPdf(SchoolClass $class): View
    {
        $class->load(['teacherAssignments.teacher', 'teacherAssignments.subject', 'level']);

        return view('classes.teachers-pdf', [
            'class' => $class,
            'assignments' => $class->teacherAssignments,
        ]);
    }
}