<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->json('criteria')->nullable();
            $table->json('levels')->nullable();
            $table->json('cells')->nullable();
            $table->timestamps();
            $table->index('course_id');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('rubric_ref')->nullable()->after('max_score')
                  ->constrained('rubrics')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['rubric_ref']);
            $table->dropColumn('rubric_ref');
        });
        Schema::dropIfExists('rubrics');
    }
};
