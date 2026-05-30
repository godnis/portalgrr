<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Se a tabela não existir, não faz nada
        if (!Schema::hasTable('pre_inscricoes')) {
            return;
        }

        Schema::table('pre_inscricoes', function (Blueprint $table) {
            if (!Schema::hasColumn('pre_inscricoes', 'nome_completo')) {
                $table->string('nome_completo', 120)->nullable()->after('origem');
            }

            if (!Schema::hasColumn('pre_inscricoes', 'rg')) {
                // Como você está validando como string numérica no controller, manter string evita dor de cabeça
                $table->string('rg', 12)->nullable()->after('nome_completo');
            }
        });
    }

    public function down(): void
    {
        // SQLite é chato pra dropColumn em algumas versões;
        // se quiser rollback real depois, eu te passo o caminho certo.
    }
};
