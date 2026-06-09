<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_visible_to_all')->default(false);
            $table->timestamps();
        });

        Schema::create('question_bank_course', function (Blueprint $table) {
            $table->foreignId('question_bank_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->primary(['question_bank_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_bank_course');
        Schema::dropIfExists('question_banks');
    }
};
