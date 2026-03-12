<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique();
            $table->string('full_name');
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unique(['parent_id', 'student_id']);
        });

        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unique(['school_class_id', 'subject_id']);
            $table->timestamps();
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence_number');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->boolean('is_open')->default(false);
            $table->timestamps();
            $table->unique(['school_class_id', 'sequence_number']);
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->timestamps();
            $table->unique(['student_id', 'evaluation_id', 'subject_id']);
        });

        Schema::create('attendance_periods', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->timestamps();
        });

        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_period_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('hours');
            $table->timestamps();
        });

        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('amount_due', 10, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_paid', 10, 2);
            $table->date('paid_at');
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('fee_items');
        Schema::dropIfExists('absences');
        Schema::dropIfExists('attendance_periods');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('teacher_assignments');
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('students');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('school_classes');
    }
};
