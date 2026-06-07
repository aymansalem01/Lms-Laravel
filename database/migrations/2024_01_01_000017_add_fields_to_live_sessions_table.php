<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('live_sessions', 'duration')) {
                $table->integer('duration')->nullable()->after('provider');
            }
            if (!Schema::hasColumn('live_sessions', 'status')) {
                $table->string('status')->default('scheduled')->after('duration');
            }
            if (!Schema::hasColumn('live_sessions', 'mode')) {
                $table->string('mode')->default('builtin')->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            $table->dropColumn(['duration', 'status', 'mode']);
        });
    }
};
