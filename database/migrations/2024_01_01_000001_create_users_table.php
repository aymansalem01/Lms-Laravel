<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TABLE: users
 * Source: public.users (Supabase schema)
 * Extra fields from: types/index.ts → User interface
 *   - qualifications, linkedin_url, website_url, years_experience
 *   - is_verified, verified_at, verified_by
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Core identity
            $table->string('name');                                      // full_name in Supabase
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar_url')->nullable();
            $table->text('bio')->nullable();

            // Program & role
            $table->enum('program', [
                'Film Production',
                'Digital Media',
                'Game Design',
                'Audio Engineering',
            ])->nullable();
            $table->enum('role', ['student', 'instructor', 'admin'])->default('student');

            // Instructor credentials (types/index.ts → User)
            $table->json('qualifications')->nullable();                  // string[]
            $table->string('linkedin_url')->nullable();
            $table->string('website_url')->nullable();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();       // FK added in next step (self-ref)

            // i18n
            $table->string('locale', 5)->default('en');                  // 'en' | 'ar'

            $table->rememberToken();
            $table->timestamps();
        });

        // Self-referencing FK: verified_by → users.id
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('verified_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
