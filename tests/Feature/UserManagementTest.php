<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

   public function test_teacher_can_be_created_without_password_and_gets_default_password(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Prof Test',
            'login' => 'prof_test',
            'role' => 'enseignant',
            'password' => '',
        ]);

        $response->assertSessionHasNoErrors();

        $teacher = User::where('login', 'prof_test')->firstOrFail();
        $this->assertTrue(Hash::check('prof_test@1234', $teacher->password));
        $this->assertTrue($teacher->must_change_password);
    }

    public function test_cellule_informatique_can_reset_password_to_default_format(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $teacher = User::factory()->create([
            'role' => 'enseignant',
            'login' => 'ens_amine',
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('users.password.reset', $teacher));

        $response->assertSessionHasNoErrors();
        
         $teacher->refresh();
        $this->assertTrue(Hash::check('ens_amine@1234', $teacher->password));
        $this->assertTrue($teacher->must_change_password);
    }
}
