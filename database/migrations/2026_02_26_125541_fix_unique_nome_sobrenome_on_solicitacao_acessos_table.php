<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('solicitacao_acessos', function (Blueprint $table) {
            // Remove o UNIQUE nome+sobrenome
            $table->dropUnique('solicitacao_acessos_nome_sobrenome_unique');

            // Cria apenas index normal (não bloqueia)
            $table->index(['nome', 'sobrenome'], 'solic_nome_sobrenome_idx');
        });
    }

    public function down(): void
    {
        Schema::table('solicitacao_acessos', function (Blueprint $table) {
            $table->dropIndex('solic_nome_sobrenome_idx');
            $table->unique(['nome', 'sobrenome']);
        });
    }
};
