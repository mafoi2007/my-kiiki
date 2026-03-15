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
            'email' => 'prof@test.ecole',
            'phone' => '+2250701020304',
            'password' => '',
        ]);

        $response->assertSessionHasNoErrors();

        $teacher = User::where('login', 'prof_test')->firstOrFail();
        $this->assertSame('PROF TEST', $teacher->name);
        $this->assertSame('prof@test.ecole', $teacher->email);
        $this->assertSame('+2250701020304', $teacher->phone);
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

    public function test_cellule_informatique_can_update_user_name_and_it_is_saved_in_uppercase(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $teacher = User::factory()->create([
            'role' => 'enseignant',
            'name' => 'Nom Initial',
        ]);

        $response = $this->actingAs($admin)->patch(route('users.name.update', $teacher), [
            'name' => 'nouveau nom',
        ]);

        $response->assertSessionHasNoErrors();

        $teacher->refresh();
        $this->assertSame('NOUVEAU NOM', $teacher->name);
    }
}
