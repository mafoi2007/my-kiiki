<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeacherAssignment;
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
        $this->assertSame('+2370701020304', $teacher->phone);
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

    public function test_cellule_informatique_can_update_user_except_login_and_password(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $teacher = User::factory()->create([
            'role' => 'enseignant',
            'name' => 'Nom Initial',
            'login' => 'login_original',
            'password' => Hash::make('AncienMotDePasse@1234'),
        ]);

        $oldPasswordHash = $teacher->password;

        $response = $this->actingAs($admin)->put(route('users.update', $teacher), [
            'name' => 'nouveau nom',
            'role' => 'censeur',
            'email' => 'new-email@test.ecole',
            'phone' => '+2250102030405',
            'must_change_password' => '1',
            'login' => 'nouveau_login_non_prise_en_compte',
            'password' => 'NouveauMotDePasse@9999',
        ]);

        $response->assertSessionHasNoErrors();

        $teacher->refresh();
        $this->assertSame('NOUVEAU NOM', $teacher->name);
        $this->assertSame('censeur', $teacher->role);
        $this->assertSame('new-email@test.ecole', $teacher->email);
        $this->assertSame('+2250102030405', $teacher->phone);
        $this->assertSame('login_original', $teacher->login);
        $this->assertTrue($teacher->must_change_password);
        $this->assertSame($oldPasswordHash, $teacher->password);
    }

    public function test_user_cannot_be_deleted_when_assigned_to_subject_in_class(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $teacher = User::factory()->create(['role' => 'enseignant']);
        $class = SchoolClass::create(['name' => '3eme A']);
        $subject = Subject::create(['name' => 'Mathématiques']);

        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('users.destroy', $teacher));

        $response->assertSessionHasErrors('user_delete');
        $this->assertDatabaseHas('users', ['id' => $teacher->id]);
    }

    public function test_users_are_sorted_alphabetically_and_searchable_by_name_or_login(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);

        User::factory()->create(['name' => 'ZED USER', 'login' => 'zed_login']);
        User::factory()->create(['name' => 'ALPHA USER', 'login' => 'alpha_login']);
        User::factory()->create(['name' => 'BETA USER', 'login' => 'special_login']);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['ALPHA USER', 'BETA USER', 'ZED USER']);

        $searchResponse = $this->actingAs($admin)->get(route('users.index', ['q' => 'special']));

        $searchResponse->assertOk();
        $searchResponse->assertSee('BETA USER');
        $searchResponse->assertDontSee('ALPHA USER</td>', false);
        $searchResponse->assertDontSee('ZED USER</td>', false);
    }
}
