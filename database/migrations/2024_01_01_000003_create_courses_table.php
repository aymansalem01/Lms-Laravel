<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: courses
 * Source: public.courses (Supabase schema)
 * Extra: is_published — used in Next.js app catalog/filtering
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('program')->nullable();                        // 'Film Production' | 'Digital Media' | ...

            $table->foreignId('instructor_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('cover_image_url')->nullable();
            $table->boolean('is_published')->default(false);             // draft vs published

            $table->timestamps();

            // idx_courses_program (Supabase: no explicit index, added for filtering)
            $table->index('program');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
