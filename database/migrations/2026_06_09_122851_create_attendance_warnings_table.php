<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('warning_level'); // 1 = first, 2 = second
            $table->decimal('absence_rate', 5, 2);
            $table->timestamp('generated_at');
            $table->timestamps();
            $table->unique(['course_id', 'student_id', 'warning_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_warnings');
    }
};
