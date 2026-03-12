<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{  
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('login')->unique();
            $table->enum('role', [
                'cellule_informatique',
                'chef_etablissement',
                'censeur',
                'surveillant_general',
                'econome',
                'enseignant',
                'parent',
            ]);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
