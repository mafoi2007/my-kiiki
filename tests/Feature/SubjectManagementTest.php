<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectManagementTest extends TestCase
{
    use RefreshDatabase;

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
}