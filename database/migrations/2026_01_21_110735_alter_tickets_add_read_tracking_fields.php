<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // campos de leitura (user/admin)
            if (!Schema::hasColumn('tickets', 'user_last_read_at')) {
                $table->timestamp('user_last_read_at')->nullable()->after('user_agent');
            }

            if (!Schema::hasColumn('tickets', 'admin_last_read_at')) {
                $table->timestamp('admin_last_read_at')->nullable()->after('user_last_read_at');
            }

            // último horário de mensagem (pra ordenar/avisos)
            if (!Schema::hasColumn('tickets', 'last_message_at')) {
                $table->timestamp('last_message_at')->nullable()->after('admin_last_read_at');
            }
        });
    }

    public function down(): void
    {
        // Em SQLite, drop column costuma ser chato.
        // Como você usa migrate:fresh pra “zerar”, pode deixar vazio.
    }
};
