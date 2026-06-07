<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: live_sessions
 * Source: public.live_sessions (Supabase schema)
 * Matches: LiveSession interface in types/index.ts — exact match
 * Extra: provider column (livekit / whereby / external) — used in Next.js LiveKit integration
 * Index: idx_live_sessions_course
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->cascadeOnDelete();

            $table->string('title');
            $table->timestamp('scheduled_at')->nullable();
            $table->string('room_url')->nullable();
            $table->string('recording_url')->nullable();

            // provider: added to support LiveKit + Whereby + external links
            $table->enum('provider', ['livekit', 'whereby', 'external'])->default('whereby');

            $table->timestamps();

            // idx_live_sessions_course
            $table->index('course_id');
            // Useful for upcoming sessions query
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_sessions');
    }
};
