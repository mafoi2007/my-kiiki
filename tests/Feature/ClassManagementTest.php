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
}
