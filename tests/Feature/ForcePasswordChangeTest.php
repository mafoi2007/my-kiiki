<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForcePasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_must_change_password_is_redirected_to_change_page_after_login(): void
    {
        $user = User::factory()->create([
            'login' => 'chef_test',
            'password' => Hash::make('chef_test@1234'),
            'role' => 'chef_etablissement',
            'must_change_password' => true,
        ]);

        $response = $this->post(route('login.attempt'), [
            'login' => $user->login,
            'password' => 'chef_test@1234',
        ]);

        $response->assertRedirect(route('password.change.form'));
    }

    public function test_forced_password_change_requires_strong_password(): void
    {
        $user = User::factory()->create([
            'role' => 'enseignant',
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->post(route('password.change.update'), [
            'password' => 'simple123',
            'password_confirmation' => 'simple123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_forced_password_change_accepts_strong_password_and_unlocks_account(): void
    {
        $user = User::factory()->create([
            'role' => 'enseignant',
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->post(route('password.change.update'), [
            'password' => 'Complexe@2026',
            'password_confirmation' => 'Complexe@2026',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('Complexe@2026', $user->password));
    }
}
