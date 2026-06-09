<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add question_bank_id (nullable for the data migration step)
        Schema::table('question_bank_items', function (Blueprint $table) {
            $table->foreignId('question_bank_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Migrate existing items: create a "General Bank" per course that has items
        $courses = DB::table('question_bank_items')
            ->select('course_id')
            ->distinct()
            ->pluck('course_id');

        foreach ($courses as $courseId) {
            $bankId = DB::table('question_banks')->insertGetId([
                'name'              => 'General Bank',
                'user_id'           => DB::table('courses')->where('id', $courseId)->value('instructor_id') ?: 1,
                'is_visible_to_all' => false,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Link bank to its course
            DB::table('question_bank_course')->insert([
                'question_bank_id' => $bankId,
                'course_id'        => $courseId,
            ]);

            // Assign all items from this course to the bank
            DB::table('question_bank_items')
                ->where('course_id', $courseId)
                ->update(['question_bank_id' => $bankId]);
        }

        // Now make question_bank_id non-nullable
        Schema::table('question_bank_items', function (Blueprint $table) {
            $table->foreignId('question_bank_id')->nullable(false)->change();
        });

        // Drop course_id FK and column
        Schema::table('question_bank_items', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });

        // Drop is_visible_to_all (now on bank level)
        Schema::table('question_bank_items', function (Blueprint $table) {
            $table->dropColumn('is_visible_to_all');
        });
    }

    public function down(): void
    {
        Schema::table('question_bank_items', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('question_bank_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_visible_to_all')->default(false)->after('points');
        });

        // Restore course_id from bank-courses pivot
        $pivots = DB::table('question_bank_course')->get();
        foreach ($pivots as $pivot) {
            DB::table('question_bank_items')
                ->where('question_bank_id', $pivot->question_bank_id)
                ->whereNull('course_id')
                ->update(['course_id' => $pivot->course_id]);
        }

        Schema::table('question_bank_items', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable(false)->change();
            $table->dropForeign(['question_bank_id']);
            $table->dropColumn('question_bank_id');
        });
    }
};
