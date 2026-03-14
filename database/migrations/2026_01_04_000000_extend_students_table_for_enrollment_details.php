<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->string('school_matricule')->unique()->nullable()->after('matricule');
            $table->date('birth_date')->nullable()->after('full_name');
            $table->string('birth_place')->nullable()->after('birth_date');
            $table->char('status', 1)->default('N')->after('school_class_id');
            $table->char('sex', 1)->default('M')->after('status');
            $table->string('father_name')->nullable()->after('sex');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('photo_path')->nullable()->after('mother_name');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->dropColumn([
                'school_matricule',
                'birth_date',
                'birth_place',
                'status',
                'sex',
                'father_name',
                'mother_name',
                'photo_path',
            ]);
        });
    }
};