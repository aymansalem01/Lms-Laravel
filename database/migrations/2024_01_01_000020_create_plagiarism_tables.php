<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_fingerprints', function (Blueprint $table) {
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete()->primary();
            $table->json('fingerprints');
            $table->integer('word_count')->default(0);
            $table->timestamps();
        });

        Schema::create('plagiarism_reports', function (Blueprint $table) {
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete()->primary();
            $table->decimal('overall_similarity', 5, 2)->default(0);
            $table->decimal('ai_probability', 5, 2)->default(0);
            $table->json('matches')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plagiarism_reports');
        Schema::dropIfExists('submission_fingerprints');
    }
};
