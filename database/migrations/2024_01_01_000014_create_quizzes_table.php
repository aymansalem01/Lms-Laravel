<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('max_attempts')->default(1);
            $table->boolean('is_published')->default(false);
            $table->integer('time_limit')->nullable();
            $table->timestamps();
            $table->index('course_id');
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'long_answer'])->default('multiple_choice');
            $table->text('question');
            $table->json('options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->integer('points')->default(1);
            $table->integer('order_index')->default(0);
            $table->timestamps();
            $table->index('quiz_id');
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->json('answers')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('max_score', 5, 2)->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->boolean('is_draft')->default(false);
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->index('quiz_id');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
