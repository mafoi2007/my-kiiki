<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
         User::firstOrCreate(
            ['login' => 'admin'],
            [
                'name' => 'Cellule Informatique',
                'role' => 'cellule_informatique',
                'password' => Hash::make('admin1234'),
            ]
        );
    }
}
