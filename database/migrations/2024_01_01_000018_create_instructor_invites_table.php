<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_invites', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token', 64)->unique();
            $table->enum('role', ['student', 'instructor'])->default('instructor');
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('used')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('invite_token', 64)->nullable()->unique()->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('invite_token');
        });
        Schema::dropIfExists('instructor_invites');
    }
};
