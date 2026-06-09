<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rubrics', function (Blueprint $table) {
            $table->foreignId('instructor_id')->nullable()->after('course_id')
                  ->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rubrics', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');
        });
    }
};
