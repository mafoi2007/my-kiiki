<?php

namespace Tests\Feature;

use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\Level;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherGradeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_update_and_delete_grade_for_assigned_class_subject(): void
    {
        $teacher = User::factory()->create(['role' => 'enseignant']);
        $level = Level::create(['name' => '3eme']);
        $class = SchoolClass::create(['name' => '3e A', 'code' => '3A', 'level_id' => $level->id]);
        $subject = Subject::create(['name' => 'Mathématiques']);
        $student = Student::create(['matricule' => 'MAT-001', 'full_name' => 'Eleve Test', 'school_class_id' => $class->id]);

        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
        ]);

        $evaluation = Evaluation::create([
            'school_class_id' => $class->id,
            'sequence_number' => 1,
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addWeek()->toDateString(),
            'is_open' => true,
        ]);

        $this->actingAs($teacher)->post(route('teacher.grades.store'), [
            'student_id' => $student->id,
            'evaluation_id' => $evaluation->id,
            'subject_id' => $subject->id,
            'score' => 14.5,
        ])->assertSessionHasNoErrors();

        $grade = Grade::first();
        $this->assertNotNull($grade);

        $this->actingAs($teacher)->put(route('teacher.grades.update', $grade), [
            'score' => 16,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('grades', ['id' => $grade->id, 'score' => 16.00]);

        $this->actingAs($teacher)->delete(route('teacher.grades.destroy', $grade))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('grades', 0);
    }

    public function test_teacher_cannot_manage_grade_for_unassigned_subject(): void
    {
        $teacher = User::factory()->create(['role' => 'enseignant']);
        $level = Level::create(['name' => '4eme']);
        $class = SchoolClass::create(['name' => '4e A', 'code' => '4A', 'level_id' => $level->id]);
        $assignedSubject = Subject::create(['name' => 'Anglais']);
        $otherSubject = Subject::create(['name' => 'SVT']);
        $student = Student::create(['matricule' => 'MAT-002', 'full_name' => 'Eleve Deux', 'school_class_id' => $class->id]);

        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
            'subject_id' => $assignedSubject->id,
        ]);

        $evaluation = Evaluation::create([
            'school_class_id' => $class->id,
            'sequence_number' => 2,
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addWeek()->toDateString(),
            'is_open' => true,
        ]);

        $this->actingAs($teacher)->post(route('teacher.grades.store'), [
            'student_id' => $student->id,
            'evaluation_id' => $evaluation->id,
            'subject_id' => $otherSubject->id,
            'score' => 12,
        ])->assertForbidden();

        $this->assertDatabaseCount('grades', 0);
    }

    public function test_teacher_sees_trimester_average_as_mean_of_two_sequences(): void
    {
        $teacher = User::factory()->create(['role' => 'enseignant']);
        $level = Level::create(['name' => '3eme']);
        $class = SchoolClass::create(['name' => '3e A', 'code' => '3A', 'level_id' => $level->id]);
        $subject = Subject::create(['name' => 'Mathématiques']);
        $student = Student::create(['matricule' => 'MAT-003', 'full_name' => 'Eleve Trois', 'school_class_id' => $class->id]);

        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
        ]);

        $seq1 = Evaluation::create([
            'school_class_id' => $class->id,
            'sequence_number' => 1,
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addWeek()->toDateString(),
            'is_open' => true,
        ]);

        $seq2 = Evaluation::create([
            'school_class_id' => $class->id,
            'sequence_number' => 2,
            'starts_at' => now()->addWeeks(2)->toDateString(),
            'ends_at' => now()->addWeeks(3)->toDateString(),
            'is_open' => true,
        ]);

        Grade::create([
            'student_id' => $student->id,
            'evaluation_id' => $seq1->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'score' => 12,
        ]);

        Grade::create([
            'student_id' => $student->id,
            'evaluation_id' => $seq2->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'score' => 16,
        ]);

        $response = $this->actingAs($teacher)->get(route('teacher.grades.index', [
            'sequence' => 2,
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
        ]));

        $response->assertOk();
        $response->assertSee('14.00');
        $response->assertSee('La note trimestrielle est calculée');
    }
}
