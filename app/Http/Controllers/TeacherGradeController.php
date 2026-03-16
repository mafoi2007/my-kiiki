<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\TeacherAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherGradeController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $selectedSequence = $request->integer('sequence');
        $selectedClassId = $request->integer('school_class_id');
        $selectedSubjectId = $request->integer('subject_id');

        $baseAssignments = TeacherAssignment::query()
            ->where('teacher_id', $teacher->id)
            ->whereHas('schoolClass.evaluations', fn ($query) => $query->where('is_open', true));

        $sequences = (clone $baseAssignments)
            ->join('evaluations', 'teacher_assignments.school_class_id', '=', 'evaluations.school_class_id')
            ->where('evaluations.is_open', true)
            ->distinct()
            ->orderBy('evaluations.sequence_number')
            ->pluck('evaluations.sequence_number');

        $classes = collect();
        $subjects = collect();
        $students = collect();
        $evaluation = null;
        $grades = collect();
        $trimesterAverageByStudent = collect();

        if ($selectedSequence > 0) {
            $classes = SchoolClass::query()
                ->select('school_classes.*')
                ->join('teacher_assignments', 'teacher_assignments.school_class_id', '=', 'school_classes.id')
                ->join('levels', 'school_classes.level_id', '=', 'levels.id')
                ->join('evaluations', 'evaluations.school_class_id', '=', 'school_classes.id')
                ->where('teacher_assignments.teacher_id', $teacher->id)
                ->where('evaluations.sequence_number', $selectedSequence)
                ->where('evaluations.is_open', true)
                ->distinct()
                ->orderBy('levels.name')
                ->orderBy('school_classes.name')
                ->get();
        }

        if ($selectedClassId > 0 && $selectedSequence > 0) {
            $subjects = TeacherAssignment::query()
                ->with('subject')
                ->where('teacher_id', $teacher->id)
                ->where('school_class_id', $selectedClassId)
                ->whereHas('schoolClass.evaluations', fn ($query) => $query
                    ->where('sequence_number', $selectedSequence)
                    ->where('is_open', true))
                ->get()
                ->pluck('subject')
                ->unique('id')
                ->sortBy('name')
                ->values();

            $evaluation = Evaluation::query()
                ->where('school_class_id', $selectedClassId)
                ->where('sequence_number', $selectedSequence)
                ->where('is_open', true)
                ->first();
        }

        if ($selectedSubjectId > 0 && $evaluation) {
            $isAssigned = TeacherAssignment::query()
                ->where('teacher_id', $teacher->id)
                ->where('school_class_id', $selectedClassId)
                ->where('subject_id', $selectedSubjectId)
                ->exists();

            if ($isAssigned) {
                $students = SchoolClass::findOrFail($selectedClassId)
                    ->students()
                    ->orderBy('full_name')
                    ->get();

                $grades = Grade::query()
                    ->where('evaluation_id', $evaluation->id)
                    ->where('subject_id', $selectedSubjectId)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->get()
                    ->keyBy('student_id');


                    $trimesterAverageByStudent = collect();
                $trimesterSequences = $this->trimesterSequences($evaluation->sequence_number);

                if ($trimesterSequences !== null) {
                    $trimesterEvaluationIds = Evaluation::query()
                        ->where('school_class_id', $selectedClassId)
                        ->whereIn('sequence_number', $trimesterSequences)
                        ->pluck('id');

                    if ($trimesterEvaluationIds->isNotEmpty()) {
                        $trimesterAverageByStudent = Grade::query()
                            ->selectRaw('student_id, AVG(score) as trimester_average')
                            ->whereIn('evaluation_id', $trimesterEvaluationIds)
                            ->where('subject_id', $selectedSubjectId)
                            ->whereIn('student_id', $students->pluck('id'))
                            ->groupBy('student_id')
                            ->get()
                            ->keyBy('student_id');
                    }
                }
            }
        }

        return view('teacher-grades.index', [
            'sequences' => $sequences,
            'classes' => $classes,
            'subjects' => $subjects,
            'students' => $students,
            'grades' => $grades,
            'evaluation' => $evaluation,
            'selectedSequence' => $selectedSequence,
            'selectedClassId' => $selectedClassId,
            'selectedSubjectId' => $selectedSubjectId,
            'trimesterAverageByStudent' => $trimesterAverageByStudent,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();
        $data = $this->validateGradeRequest($request);

        Grade::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'evaluation_id' => $data['evaluation_id'],
                'subject_id' => $data['subject_id'],
            ],
            [
                'teacher_id' => $teacher->id,
                'score' => $data['score'],
            ]
        );

        return back()->with('success', 'Note enregistrée.');
    }

    public function update(Request $request, Grade $grade): RedirectResponse
    {
        $teacher = $request->user();

        if ((int) $grade->teacher_id !== (int) $teacher->id) {
            abort(403);
        }

        $data = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:20'],
        ]);

        $grade->update(['score' => $data['score']]);

        return back()->with('success', 'Note mise à jour.');
    }

    public function destroy(Request $request, Grade $grade): RedirectResponse
    {
        $teacher = $request->user();

        if ((int) $grade->teacher_id !== (int) $teacher->id) {
            abort(403);
        }

        $grade->delete();

        return back()->with('success', 'Note supprimée.');
    }

    private function trimesterSequences(int $sequenceNumber): ?array
    {
        return match ($sequenceNumber) {
            1, 2 => [1, 2],
            3, 4 => [3, 4],
            5, 6 => [5, 6],
            default => null,
        };
    }

    private function validateGradeRequest(Request $request): array
    {
        $teacher = $request->user();

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'evaluation_id' => ['required', 'exists:evaluations,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'score' => ['required', 'numeric', 'min:0', 'max:20'],
        ]);

        $evaluation = Evaluation::query()
            ->with('schoolClass')
            ->whereKey($data['evaluation_id'])
            ->where('is_open', true)
            ->firstOrFail();

        $studentInClass = $evaluation->schoolClass
            ->students()
            ->whereKey($data['student_id'])
            ->exists();

        if (! $studentInClass) {
            abort(403);
        }

        $isAssigned = TeacherAssignment::query()
            ->where('teacher_id', $teacher->id)
            ->where('school_class_id', $evaluation->school_class_id)
            ->where('subject_id', $data['subject_id'])
            ->exists();

        if (! $isAssigned) {
            abort(403);
        }

        return $data;
    }
}