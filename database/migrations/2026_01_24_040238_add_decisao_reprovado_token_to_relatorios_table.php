<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {
            // ✅ você usa isso nas views, mas não existe na tabela atual
            if (!Schema::hasColumn('relatorios', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('viaturas_fiscalizadas');
            }

            // ✅ decisão (aprovar/reprovar)
            if (!Schema::hasColumn('relatorios', 'decisao_obs')) {
                $table->string('decisao_obs', 400)->nullable()->after('status');
            }

            if (!Schema::hasColumn('relatorios', 'reprovado_por')) {
                $table->foreignId('reprovado_por')->nullable()->after('aprovado_por')->constrained('users');
            }

            // ✅ token idempotente (anti duplicidade por erro de rede)
            if (!Schema::hasColumn('relatorios', 'client_token')) {
                $table->string('client_token', 64)->nullable()->after('user_id');
                $table->unique('client_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {
            if (Schema::hasColumn('relatorios', 'client_token')) {
                $table->dropUnique(['client_token']);
                $table->dropColumn('client_token');
            }

            if (Schema::hasColumn('relatorios', 'reprovado_por')) {
                $table->dropConstrainedForeignId('reprovado_por');
            }

            if (Schema::hasColumn('relatorios', 'decisao_obs')) {
                $table->dropColumn('decisao_obs');
            }

            if (Schema::hasColumn('relatorios', 'observacoes')) {
                $table->dropColumn('observacoes');
            }
        });
    }
};
