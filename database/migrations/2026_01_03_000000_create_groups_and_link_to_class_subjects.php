<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('school_class_subject', function (Blueprint $table): void {
            $table->foreignId('group_id')->after('subject_id')->constrained('groups')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('school_class_subject', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('group_id');
        });

        Schema::dropIfExists('groups');
    }
};
