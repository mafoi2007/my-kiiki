<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTeacherStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_dashboard_shows_only_assigned_classes_subjects_and_students_counts(): void
    {
        $teacher = User::factory()->create(['role' => 'enseignant']);
        $otherTeacher = User::factory()->create(['role' => 'enseignant']);

        $classA = SchoolClass::create(['name' => '3e A', 'code' => '3A']);
        $classB = SchoolClass::create(['name' => '3e B', 'code' => '3B']);
        $classC = SchoolClass::create(['name' => '3e C', 'code' => '3C']);

        $subjectMath = Subject::create(['name' => 'Mathématiques']);
        $subjectFrench = Subject::create(['name' => 'Français']);
        $subjectSvt = Subject::create(['name' => 'SVT']);

        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $classA->id,
            'subject_id' => $subjectMath->id,
        ]);
        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $classA->id,
            'subject_id' => $subjectFrench->id,
        ]);
        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $classB->id,
            'subject_id' => $subjectMath->id,
        ]);
        TeacherAssignment::create([
            'teacher_id' => $otherTeacher->id,
            'school_class_id' => $classC->id,
            'subject_id' => $subjectSvt->id,
        ]);

        Student::create([
            'matricule' => 'NAT-300',
            'school_matricule' => 'NRA26-60001',
            'full_name' => 'Eleve A',
            'birth_date' => '2012-01-01',
            'school_class_id' => $classA->id,
            'status' => 'N',
            'sex' => 'M',
            'mother_name' => 'Parent A',
            'active' => true,
        ]);
        Student::create([
            'matricule' => 'NAT-301',
            'school_matricule' => 'NRA26-60002',
            'full_name' => 'Eleve B',
            'birth_date' => '2012-01-01',
            'school_class_id' => $classB->id,
            'status' => 'N',
            'sex' => 'F',
            'mother_name' => 'Parent B',
            'active' => true,
        ]);
        Student::create([
            'matricule' => 'NAT-302',
            'school_matricule' => 'NRA26-60003',
            'full_name' => 'Eleve C',
            'birth_date' => '2012-01-01',
            'school_class_id' => $classC->id,
            'status' => 'N',
            'sex' => 'M',
            'mother_name' => 'Parent C',
            'active' => true,
        ]);

        $response = $this->actingAs($teacher)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Classes tenues');
        $response->assertSee('Matières tenues');
        $response->assertSee('Élèves tenus');

        $response->assertSee('<div class="text-muted">Classes tenues</div>', false);
        $response->assertSee('<div class="fs-3 fw-bold">2</div>', false);
        $response->assertSee('<div class="text-muted">Matières tenues</div>', false);
        $response->assertSee('<div class="text-muted">Élèves tenus</div>', false);
        $response->assertDontSee('Nombre total de classes');
    }
}
