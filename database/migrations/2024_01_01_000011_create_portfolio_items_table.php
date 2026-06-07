<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: portfolio_items
 * Source: public.portfolio_items (Supabase schema) — exact match
 * Matches: PortfolioItem interface in types/index.ts
 *   - media_type check: 'video' | 'audio' | 'image' | 'file'
 * Index: idx_portfolio_student
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('media_url')->nullable();

            // media_type check constraint — mirrors Supabase exactly
            $table->enum('media_type', ['video', 'audio', 'image', 'file'])->nullable();

            $table->boolean('is_public')->default(false);

            $table->timestamps();

            // idx_portfolio_student
            $table->index('student_id');
            // Useful filter index: public items
            $table->index(['student_id', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
