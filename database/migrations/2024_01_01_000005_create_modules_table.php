<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: modules
 * Source: public.modules (Supabase schema) — exact match
 * Index: idx_modules_course
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->cascadeOnDelete();

            $table->string('title');
            $table->integer('order_index')->default(0);

            $table->timestamps();

            // idx_modules_course
            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
