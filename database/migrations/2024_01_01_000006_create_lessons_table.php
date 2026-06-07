<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: lessons
 * Source: public.lessons (Supabase schema) — exact match
 * Index: idx_lessons_module
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('module_id')
                  ->constrained('modules')
                  ->cascadeOnDelete();

            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->string('file_url')->nullable();
            $table->integer('order_index')->default(0);

            $table->timestamps();

            // idx_lessons_module
            $table->index('module_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
