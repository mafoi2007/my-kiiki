<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_fields_are_validated_when_creating_student(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $class = SchoolClass::create(['name' => '6e A', 'code' => '6A']);

        $response = $this->actingAs($admin)->post(route('students.store'), [
            'matricule' => 'NAT-001',
            'full_name' => 'Jean Test',
            'school_class_id' => $class->id,
        ]);

        $response->assertSessionHasErrors(['birth_date', 'status', 'sex', 'mother_name']);
        $this->assertDatabaseMissing('students', ['matricule' => 'NAT-001']);
    }

    public function test_school_matricule_is_generated_and_student_can_be_soft_deleted_via_active_flag(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $class = SchoolClass::create(['name' => '5e B', 'code' => '5B']);

        $this->actingAs($admin)->post(route('students.store'), [
            'matricule' => 'NAT-100',
            'full_name' => 'Élève Un',
            'birth_date' => '2012-01-01',
            'school_class_id' => $class->id,
            'status' => 'N',
            'sex' => 'M',
            'mother_name' => 'Marie Un',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('students.store'), [
            'matricule' => 'NAT-101',
            'full_name' => 'Élève Deux',
            'birth_date' => '2012-02-02',
            'school_class_id' => $class->id,
            'status' => 'R',
            'sex' => 'F',
            'mother_name' => 'Marie Deux',
        ])->assertSessionHasNoErrors();

        $year = now()->format('y');

        $this->assertDatabaseHas('students', [
            'matricule' => 'NAT-100',
            'school_matricule' => "NRA{$year}-60001",
        ]);

        $this->assertDatabaseHas('students', [
            'matricule' => 'NAT-101',
            'school_matricule' => "NRA{$year}-60002",
            'active' => true,
        ]);

        $studentId = (int) \App\Models\Student::query()->where('matricule', 'NAT-101')->value('id');

        $this->actingAs($admin)
            ->delete(route('students.destroy', $studentId))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', [
            'id' => $studentId,
            'active' => false,
        ]);
    }
}