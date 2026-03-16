<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_cellule_informatique_can_create_and_activate_sequence_for_a_class(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $level = Level::create(['name' => '6eme']);
        $class = SchoolClass::create(['name' => '6e A', 'code' => '6A', 'level_id' => $level->id]);

        $this->actingAs($admin)->post(route('evaluations.store'), [
            'school_class_id' => $class->id,
            'sequence_number' => 1,
            'starts_at' => '2026-09-10',
            'ends_at' => '2026-10-20',
            'is_open' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('evaluations', [
            'school_class_id' => $class->id,
            'sequence_number' => 1,
            'is_open' => true,
        ]);
    }

    public function test_sequence_number_must_be_between_one_and_six(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $level = Level::create(['name' => '5eme']);
        $class = SchoolClass::create(['name' => '5e A', 'code' => '5A', 'level_id' => $level->id]);

        $this->actingAs($admin)->post(route('evaluations.store'), [
            'school_class_id' => $class->id,
            'sequence_number' => 7,
            'starts_at' => '2026-09-10',
            'ends_at' => '2026-10-20',
            'is_open' => 1,
        ])->assertSessionHasErrors('sequence_number');

        $this->assertDatabaseCount('evaluations', 0);
    }

    public function test_enseignant_cannot_access_sequence_management(): void
    {
        $teacher = User::factory()->create(['role' => 'enseignant']);

        $this->actingAs($teacher)->get(route('evaluations.index'))->assertForbidden();
    }
}
