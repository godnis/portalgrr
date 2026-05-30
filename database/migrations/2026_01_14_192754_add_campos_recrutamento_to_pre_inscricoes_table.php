<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pre_inscricoes', function (Blueprint $table) {
            $table->string('qra_rg', 120)->after('id');
            $table->string('discord_id', 80)->after('qra_rg');

            $table->text('motivo_grr_agora')->after('discord_id');
            $table->text('diferencial_grr')->after('motivo_grr_agora');

            $table->string('estagio_15_dias', 10)->after('diferencial_grr');
            $table->string('dias_ativo_semana', 10)->after('estagio_15_dias');

            $table->string('ordem_nao_concorda', 60)->after('dias_ativo_semana');
            $table->string('horario_frequente', 20)->after('ordem_nao_concorda');

            $table->text('como_lida_frustracao')->after('horario_frequente');
            $table->text('experiencia_anterior')->after('como_lida_frustracao');
        });
    }

    public function down(): void
    {
        Schema::table('pre_inscricoes', function (Blueprint $table) {
            $table->dropColumn([
                'qra_rg',
                'discord_id',
                'motivo_grr_agora',
                'diferencial_grr',
                'estagio_15_dias',
                'dias_ativo_semana',
                'ordem_nao_concorda',
                'horario_frequente',
                'como_lida_frustracao',
                'experiencia_anterior',
            ]);
        });
    }
};
