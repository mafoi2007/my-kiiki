<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Group;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
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
