<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            // ✅ Leitura / notificações
            if (!Schema::hasColumn('tickets', 'user_last_read_at')) {
                $table->timestamp('user_last_read_at')->nullable()->after('fechado_em');
            }

            if (!Schema::hasColumn('tickets', 'admin_last_read_at')) {
                $table->timestamp('admin_last_read_at')->nullable()->after('user_last_read_at');
            }

            if (!Schema::hasColumn('tickets', 'last_message_at')) {
                $table->timestamp('last_message_at')->nullable()->after('admin_last_read_at');
            }
        });

        // ✅ Foreign Keys (seguro até no SQLite)
        try {
            Schema::table('tickets', function (Blueprint $table) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('tickets', function (Blueprint $table) {
                $table->foreign('assigned_to')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        try {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropForeign(['assigned_to']);
            });
        } catch (\Throwable $e) {}

        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'last_message_at')) {
                $table->dropColumn('last_message_at');
            }
            if (Schema::hasColumn('tickets', 'admin_last_read_at')) {
                $table->dropColumn('admin_last_read_at');
            }
            if (Schema::hasColumn('tickets', 'user_last_read_at')) {
                $table->dropColumn('user_last_read_at');
            }
        });
    }
};
