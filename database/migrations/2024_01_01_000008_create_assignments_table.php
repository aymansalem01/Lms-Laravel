<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: assignments
 * Source: public.assignments (Supabase schema)
 * Matches: Assignment interface in types/index.ts
 *   - rubric: text (kept as-is, inline rubric text from Supabase)
 *   - max_score: added for grading (used in grading components)
 * Index: idx_assignments_course
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->text('rubric')->nullable();              // original Supabase: rubric text (inline)
            $table->integer('max_score')->default(100);      // added: needed by grading panel

            $table->timestamps();

            // idx_assignments_course
            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
