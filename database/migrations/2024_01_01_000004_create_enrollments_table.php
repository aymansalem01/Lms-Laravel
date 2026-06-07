<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: enrollments
 * Source: public.enrollments (Supabase schema) — exact match
 * Indexes: idx_enrollments_student, idx_enrollments_course
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->cascadeOnDelete();

            $table->timestamp('enrolled_at')->useCurrent();

            $table->timestamps();

            // unique(student_id, course_id) — mirrors Supabase constraint
            $table->unique(['student_id', 'course_id']);

            // idx_enrollments_student, idx_enrollments_course
            // (FK columns are auto-indexed by Laravel, explicit for clarity)
            $table->index('student_id');
            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
