<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'module_id')) {
                $table->foreignId('module_id')->nullable()->after('course_id')
                      ->constrained('modules')->cascadeOnDelete();
                $table->index('module_id');
            }
        });

        Schema::table('live_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('live_sessions', 'module_id')) {
                $table->foreignId('module_id')->nullable()->after('course_id')
                      ->constrained('modules')->cascadeOnDelete();
                $table->index('module_id');
            }
        });

        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'module_id')) {
                $table->foreignId('module_id')->nullable()->after('course_id')
                      ->constrained('modules')->cascadeOnDelete();
                $table->index('module_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('module_id');
        });
        Schema::table('live_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('module_id');
        });
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('module_id');
        });
    }
};
