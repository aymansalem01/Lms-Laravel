<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: lesson_progress
 * Source: Not in original Supabase schema — inferred from Next.js app usage
 *   (progress tracking per user per lesson, used in /courses/[courseId]/progress)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('lesson_id')
                  ->constrained('lessons')
                  ->cascadeOnDelete();

            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Each user has one progress record per lesson
            $table->unique(['user_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
