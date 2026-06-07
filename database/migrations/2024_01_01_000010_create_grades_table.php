<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: grades
 * Source: public.grades (Supabase schema) — exact match
 * Matches: Grade interface in types/index.ts
 * Constraint: score between 0 and 100 (enforced at application level)
 * Index: idx_grades_submission
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            // One grade per submission (unique)
            $table->foreignId('submission_id')
                  ->unique()
                  ->constrained('submissions')
                  ->cascadeOnDelete();

            $table->foreignId('instructor_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // score check (>= 0 and <= 100) — Supabase check constraint
            // Enforced in FormRequest validation on the Laravel side
            $table->decimal('score', 5, 2)->nullable();      // e.g. 97.50

            $table->text('feedback')->nullable();
            $table->timestamp('graded_at')->useCurrent();

            $table->timestamps();

            // idx_grades_submission (FK unique already creates an index)
            $table->index('submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
