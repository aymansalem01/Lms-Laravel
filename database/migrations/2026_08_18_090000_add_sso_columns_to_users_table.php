<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('sis_crm_user_id')->nullable()->after('id');
            $table->bigInteger('sis_student_id')->nullable()->after('sis_crm_user_id');
            $table->string('sso_provider')->nullable()->after('sis_student_id');
            $table->index('sis_crm_user_id');
            $table->index('sis_student_id');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['sis_crm_user_id']);
            $table->dropIndex(['sis_student_id']);
            $table->dropIndex(['email']);
            $table->dropColumn(['sis_crm_user_id', 'sis_student_id', 'sso_provider']);
        });
    }
};
