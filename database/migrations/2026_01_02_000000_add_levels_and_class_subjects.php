<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('school_classes', function (Blueprint $table): void {
            $table->string('code')->unique()->after('name');
            $table->foreignId('level_id')->nullable()->after('code')->constrained()->nullOnDelete();
        });

        Schema::create('school_class_subject', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('coefficient');
            $table->timestamps();
            $table->unique(['school_class_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_class_subject');

        Schema::table('school_classes', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('level_id');
            $table->dropColumn('code');
        });

        Schema::dropIfExists('levels');
    }
};