<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_password_must_have_at_least_8_characters(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Prof Test',
            'login' => 'prof_test',
            'role' => 'enseignant',
            'password' => '1234567',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['login' => 'prof_test']);
    }

    public function test_non_teacher_password_can_have_6_characters(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Parent Test',
            'login' => 'parent_test',
            'role' => 'parent',
            'password' => '123456',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['login' => 'parent_test', 'role' => 'parent']);
    }
}
