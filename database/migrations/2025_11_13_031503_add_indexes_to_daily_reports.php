<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Add performance indexes
            $table->index(['user_id', 'report_date']);
            $table->index(['status', 'report_date']);
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('users', function (Blueprint $table) {
            // Add indexes for user management
            $table->index('role');
            $table->index('last_activity');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'report_date']);
            $table->dropIndex(['status', 'report_date']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['last_activity']);
            $table->dropIndex(['deleted_at']);
        });
    }
};