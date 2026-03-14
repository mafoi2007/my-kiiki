<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LevelManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_cellule_can_create_level(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);

        $response = $this->actingAs($admin)->post(route('levels.store'), [
            'name' => 'Terminale',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('levels', ['name' => 'Terminale']);
    }

    public function test_cellule_can_update_level_name(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $level = Level::create(['name' => 'Seconde']);

        $response = $this->actingAs($admin)->put(route('levels.update', $level), [
            'name' => '2nde',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('levels', ['id' => $level->id, 'name' => '2nde']);
    }

    public function test_level_with_classes_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $level = Level::create(['name' => '6eme']);
        SchoolClass::create([
            'name' => '6e A',
            'code' => '6A',
            'level_id' => $level->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('levels.destroy', $level));

        $response->assertSessionHasErrors('level');
        $this->assertDatabaseHas('levels', ['id' => $level->id]);
    }

    public function test_level_without_classes_can_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'cellule_informatique']);
        $level = Level::create(['name' => '5eme']);

        $response = $this->actingAs($admin)->delete(route('levels.destroy', $level));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('levels', ['id' => $level->id]);
    }
}