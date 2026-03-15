<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_subject_name_is_saved_in_uppercase(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);

        $response = $this->actingAs($admin)->post(route('subjects.store'), [
            'name' => 'Mathématiques',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('subjects', ['name' => 'MATHÉMATIQUES']);
    }

    public function test_subject_name_must_be_unique_when_creating(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        Subject::create(['name' => 'Histoire']);

        $response = $this->actingAs($admin)->post(route('subjects.store'), [
            'name' => 'Histoire',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('subjects', 1);
    }

    public function test_subject_name_can_be_updated(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $subject = Subject::create(['name' => 'SVT']);

        $response = $this->actingAs($admin)->put(route('subjects.update', $subject), [
            'name' => 'Sciences physiques',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'SCIENCES PHYSIQUES',
        ]);
    }
}
