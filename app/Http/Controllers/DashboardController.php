<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        if ($user !== null && $user->isRole('enseignant')) {
            $assignedClassIds = $user->assignments()
                ->distinct('school_class_id')
                ->pluck('school_class_id');

            $assignedSubjectIds = $user->assignments()
                ->distinct('subject_id')
                ->pluck('subject_id');

            return view('dashboard.index', [
                'stats' => [
                    'Utilisateurs' => User::count(),
                    'Classes tenues' => $assignedClassIds->count(),
                    'Matières tenues' => $assignedSubjectIds->count(),
                    'Groupes' => Group::count(),
                    'Élèves tenus' => Student::query()->whereIn('school_class_id', $assignedClassIds)->count(),
                ],
            ]);
        }

        return view('dashboard.index', [
            'stats' => [
                'Utilisateurs' => User::count(),
                'Classes' => SchoolClass::count(),
                'Matières' => Subject::count(),
                'Groupes' => Group::count(),
                'Élèves' => Student::count(),
            ],
        ]);
    }
}
