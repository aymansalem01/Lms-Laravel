<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: notifications
 * Source: public.notifications (Supabase schema)
 * Matches: Notification interface in types/index.ts
 * Extra fields (richer notifications used in Next.js app):
 *   - type:  categorize notification (new_assignment, grade_posted, etc.)
 *   - title: short headline shown in notification bell
 *   - link:  click-through URL
 * Index: idx_notifications_user
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Extra fields (not in Supabase, used in Next.js notification components)
            $table->string('type')->nullable();              // 'new_assignment' | 'grade_posted' | 'welcome' | ...
            $table->string('title')->nullable();             // short headline

            $table->text('message');                         // Supabase: message text not null
            $table->string('link')->nullable();              // click-through URL

            $table->boolean('is_read')->default(false);      // Supabase: is_read boolean not null default false

            $table->timestamps();

            // idx_notifications_user
            $table->index('user_id');
            // Composite: fetch unread notifications per user efficiently
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
