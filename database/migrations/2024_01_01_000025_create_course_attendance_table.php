<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->string('status'); // present, absent, late, excused
            $table->timestamps();
            $table->unique(['course_id', 'student_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_attendance');
    }
};
