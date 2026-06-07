<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_bank_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'long_answer'])->default('multiple_choice');
            $table->text('question');
            $table->json('options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->integer('points')->default(1);
            $table->timestamps();
            $table->index('course_id');
            $table->index('user_id');
        });

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->foreignId('bank_item_id')->nullable()->after('quiz_id')
                ->constrained('question_bank_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropForeign(['bank_item_id']);
            $table->dropColumn('bank_item_id');
        });
        Schema::dropIfExists('question_bank_items');
    }
};
