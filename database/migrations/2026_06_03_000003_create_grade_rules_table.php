<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('category', ['quiz', 'assignment', 'attendance']);
            $table->decimal('weight', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['course_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_rules');
    }
};
