<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_class_with_students_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $level = Level::create(['name' => '6eme']);
        $class = SchoolClass::create([
            'name' => '6e A',
            'code' => '6A',
            'level_id' => $level->id,
        ]);

        Student::create([
            'matricule' => 'MAT-001',
            'full_name' => 'Eleve test',
            'school_class_id' => $class->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('classes.destroy', $class));

        $response->assertSessionHasErrors('class');
        $this->assertDatabaseHas('school_classes', ['id' => $class->id]);
    }

    public function test_subject_can_be_assigned_with_coefficient_to_class(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $level = Level::create(['name' => '5eme']);
        $class = SchoolClass::create([
            'name' => '5e B',
            'code' => '5B',
            'level_id' => $level->id,
        ]);
        $subject = Subject::create(['name' => 'Mathématiques']);
        $group = Group::create(['name' => 'Scientifique']);

        $response = $this->actingAs($admin)->post(route('classes.subjects.assign', $class), [
            'subject_id' => $subject->id,
            'coefficient' => 4,
            'group_id' => $group->id,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('school_class_subject', [
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'coefficient' => 4,
            'group_id' => $group->id,
        ]);
    }

    public function test_same_subject_cannot_be_assigned_twice_to_class(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $level = Level::create(['name' => '4eme']);
        $class = SchoolClass::create([
            'name' => '4e C',
            'code' => '4C',
            'level_id' => $level->id,
        ]);
        $subject = Subject::create(['name' => 'SVT']);
        $group = Group::create(['name' => 'Littéraire']);

        $this->actingAs($admin)->post(route('classes.subjects.assign', $class), [
            'subject_id' => $subject->id,
            'coefficient' => 3,
            'group_id' => $group->id,
        ])->assertSessionHasNoErrors();

        $response = $this->actingAs($admin)->post(route('classes.subjects.assign', $class), [
            'subject_id' => $subject->id,
            'coefficient' => 5,
            'group_id' => $group->id,
        ]);

        $response->assertSessionHasErrors('subject_id');
        $this->assertDatabaseCount('school_class_subject', 1);
    }

    public function test_teacher_can_be_assigned_only_to_subjects_of_the_class(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $teacher = User::factory()->create(['role' => 'enseignant']);
        $level = Level::create(['name' => '3eme']);
        $class = SchoolClass::create([
            'name' => '3e A',
            'code' => '3A',
            'level_id' => $level->id,
        ]);
        $subject = Subject::create(['name' => 'Physique']);

        $response = $this->actingAs($admin)->post(route('classes.teachers.assign', $class), [
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
        ]);

        $response->assertSessionHasErrors('subject_id');
        $this->assertDatabaseCount('teacher_assignments', 0);
    }

    public function test_assigning_teacher_to_same_subject_replaces_previous_assignment(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $teacherOne = User::factory()->create(['role' => 'enseignant']);
        $teacherTwo = User::factory()->create(['role' => 'enseignant']);
        $level = Level::create(['name' => '2nde']);
        $class = SchoolClass::create([
            'name' => '2nde C',
            'code' => '2C',
            'level_id' => $level->id,
        ]);
        $subject = Subject::create(['name' => 'Français']);
        $group = Group::create(['name' => 'Langues']);

        $this->actingAs($admin)->post(route('classes.subjects.assign', $class), [
            'subject_id' => $subject->id,
            'coefficient' => 2,
            'group_id' => $group->id,
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('classes.teachers.assign', $class), [
            'teacher_id' => $teacherOne->id,
            'subject_id' => $subject->id,
        ])->assertSessionHasNoErrors();

        $response = $this->actingAs($admin)->post(route('classes.teachers.assign', $class), [
            'teacher_id' => $teacherTwo->id,
            'subject_id' => $subject->id,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('teacher_assignments', 1);
        $this->assertDatabaseHas('teacher_assignments', [
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacherTwo->id,
        ]);
    }

    public function test_non_teacher_user_cannot_be_assigned_to_subject(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $parent = User::factory()->create(['role' => 'parent']);
        $level = Level::create(['name' => '1ere']);
        $class = SchoolClass::create([
            'name' => '1ere B',
            'code' => '1B',
            'level_id' => $level->id,
        ]);
        $subject = Subject::create(['name' => 'Philosophie']);
        $group = Group::create(['name' => 'Humanités']);

        $this->actingAs($admin)->post(route('classes.subjects.assign', $class), [
            'subject_id' => $subject->id,
            'coefficient' => 3,
            'group_id' => $group->id,
        ])->assertSessionHasNoErrors();

        $response = $this->actingAs($admin)->post(route('classes.teachers.assign', $class), [
            'teacher_id' => $parent->id,
            'subject_id' => $subject->id,
        ]);

        $response->assertSessionHasErrors('teacher_id');
        $this->assertDatabaseCount('teacher_assignments', 0);
    }
}