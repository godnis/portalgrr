<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ✅ adiciona apenas o que ainda não existe
        Schema::table('relatorios', function (Blueprint $table) {

            if (!Schema::hasColumn('relatorios', 'decisao_obs')) {
                $table->text('decisao_obs')->nullable();
            }

            if (!Schema::hasColumn('relatorios', 'reprovado_por')) {
                // SQLite: evita constrained() aqui
                $table->unsignedBigInteger('reprovado_por')->nullable();
            }

            // ❌ NÃO adiciona observacoes porque já existe no seu banco
            // Se em algum ambiente não existir, você pode reativar abaixo:
            /*
            if (!Schema::hasColumn('relatorios', 'observacoes')) {
                $table->text('observacoes')->nullable();
            }
            */
        });
    }

    public function down(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {

            if (Schema::hasColumn('relatorios', 'decisao_obs')) {
                $table->dropColumn('decisao_obs');
            }

            if (Schema::hasColumn('relatorios', 'reprovado_por')) {
                $table->dropColumn('reprovado_por');
            }

            // não remove observacoes aqui por segurança
        });
    }
};
