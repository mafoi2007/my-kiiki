<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Student;
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

        $studentId = (int) Student::query()->where('matricule', 'NAT-101')->value('id');

        

        $this->actingAs($admin)
            ->delete(route('students.destroy', $studentId))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', [
            'id' => $studentId,
            'active' => false,
        ]);
    }

    public function test_student_can_be_updated_and_moved_to_another_class(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $fromClass = SchoolClass::create(['name' => '4e A', 'code' => '4A']);
        $toClass = SchoolClass::create(['name' => '4e B', 'code' => '4B']);

        $student = Student::create([
            'matricule' => 'NAT-555',
            'school_matricule' => 'NRA26-60001',
            'full_name' => 'Avant Nom',
            'birth_date' => '2011-10-10',
            'birth_place' => 'Abidjan',
            'school_class_id' => $fromClass->id,
            'status' => 'N',
            'sex' => 'M',
            'father_name' => 'Père Avant',
            'mother_name' => 'Mère Avant',
            'active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('students.update', $student), [
                'matricule' => 'NAT-556',
                'full_name' => 'Nouveau Nom',
                'birth_date' => '2011-11-11',
                'birth_place' => 'Bouaké',
                'status' => 'R',
                'sex' => 'F',
                'father_name' => 'Père Nouveau',
                'mother_name' => 'Mère Nouvelle',
                'active' => 1,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'matricule' => 'NAT-556',
            'full_name' => 'Nouveau Nom',
            'birth_place' => 'Bouaké',
            'status' => 'R',
            'sex' => 'F',
            'father_name' => 'Père Nouveau',
            'mother_name' => 'Mère Nouvelle',
        ]);

        $this->actingAs($admin)
            ->put(route('students.move-class', $student), [
                'from_school_class_id' => $fromClass->id,
                'to_school_class_id' => $toClass->id,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'school_class_id' => $toClass->id,
        ]);
    }

    public function test_index_displays_only_classes_that_have_students(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $filledClass = SchoolClass::create(['name' => '3e A', 'code' => '3A']);
        $emptyClass = SchoolClass::create(['name' => '3e B', 'code' => '3B']);

        Student::create([
            'matricule' => 'NAT-700',
            'school_matricule' => 'NRA26-60001',
            'full_name' => 'Eleve Présent',
            'birth_date' => '2011-01-01',
            'school_class_id' => $filledClass->id,
            'status' => 'N',
            'sex' => 'M',
            'mother_name' => 'Parent',
            'active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('students.index'));

        $response->assertOk();
        $response->assertSee('3e A');
        $response->assertDontSee('3e B');
    }
}
