<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: submissions
 * Source: public.submissions (Supabase schema)
 * Matches: Submission interface in types/index.ts — exact match
 * Extra: status column (used in grading workflow: submitted → graded → returned)
 * Indexes: idx_submissions_assignment, idx_submissions_student
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assignment_id')
                  ->constrained('assignments')
                  ->cascadeOnDelete();

            $table->foreignId('student_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Submission content — mirrors Supabase exactly
            $table->string('file_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->string('link_url')->nullable();
            $table->text('notes')->nullable();

            // Status workflow (not in Supabase, used in Next.js grading)
            $table->enum('status', ['submitted', 'graded', 'returned'])->default('submitted');
            $table->index('status');

            $table->timestamp('submitted_at')->useCurrent();

            $table->timestamps();

            // unique(assignment_id, student_id) — mirrors Supabase constraint
            $table->unique(['assignment_id', 'student_id']);

            // idx_submissions_assignment, idx_submissions_student
            $table->index('assignment_id');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
