<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Add indexes untuk columns yang sering di-query
            $table->index('user_id');
            $table->index('report_date');
            $table->index(['user_id', 'report_date']);
            $table->index('status');
            $table->index(['report_date', 'status']);
            $table->index('created_at');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index untuk user management
            $table->index('email');
            $table->index('name');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['report_date']);
            $table->dropIndex(['user_id', 'report_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['report_date', 'status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['name']);
            $table->dropIndex(['created_at']);
        });
    }
};