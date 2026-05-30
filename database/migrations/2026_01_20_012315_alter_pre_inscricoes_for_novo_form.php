<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // A tabela já existe no seu SQLite (antiga).
        if (!Schema::hasTable('pre_inscricoes')) {
            return;
        }

        Schema::table('pre_inscricoes', function (Blueprint $table) {

            // Tracking + auditoria
            if (!Schema::hasColumn('pre_inscricoes', 'origem')) {
                $table->string('origem', 255)->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'ip')) {
                $table->string('ip', 60)->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'user_agent')) {
                $table->text('user_agent')->nullable();
            }

            /**
             * ✅ NOVO: separar Nome completo e RG (Cidade BC)
             * - nome_completo: texto
             * - rg: número (guardamos como unsignedBigInteger pra não limitar)
             */
            if (!Schema::hasColumn('pre_inscricoes', 'nome_completo')) {
                $table->string('nome_completo', 120)->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'rg')) {
                $table->unsignedBigInteger('rg')->nullable();
            }

            /**
             * (Opcional) Legado: qra_rg
             * Se você ainda tiver dados antigos ou quer compatibilidade, mantenha.
             */
            if (!Schema::hasColumn('pre_inscricoes', 'qra_rg')) {
                $table->string('qra_rg', 120)->nullable();
            }

            // Campos do formulário
            if (!Schema::hasColumn('pre_inscricoes', 'discord_id')) {
                $table->string('discord_id', 80)->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'motivo_grr_agora')) {
                $table->text('motivo_grr_agora')->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'diferencial_grr')) {
                $table->text('diferencial_grr')->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'estagio_15_dias')) {
                $table->string('estagio_15_dias', 10)->nullable(); // sim/nao
            }
            if (!Schema::hasColumn('pre_inscricoes', 'dias_ativo_semana')) {
                $table->string('dias_ativo_semana', 10)->nullable(); // 1-2,3-4,5-6,7
            }
            if (!Schema::hasColumn('pre_inscricoes', 'ordem_nao_concorda')) {
                $table->string('ordem_nao_concorda', 40)->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'horario_frequente')) {
                $table->string('horario_frequente', 20)->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'como_lida_frustracao')) {
                $table->text('como_lida_frustracao')->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'experiencia_anterior')) {
                $table->text('experiencia_anterior')->nullable();
            }

            // Admin (para aprovar/reprovar)
            if (!Schema::hasColumn('pre_inscricoes', 'status')) {
                $table->string('status', 20)->default('pendente'); // pendente/aprovado/reprovado
            }
            if (!Schema::hasColumn('pre_inscricoes', 'observacao_admin')) {
                $table->text('observacao_admin')->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'revisado_por')) {
                $table->unsignedBigInteger('revisado_por')->nullable();
            }
            if (!Schema::hasColumn('pre_inscricoes', 'revisado_em')) {
                $table->dateTime('revisado_em')->nullable();
            }
        });
    }

    public function down(): void
    {
        // SQLite não lida bem com drop column em migrations antigas.
        // Vamos manter sem rollback.
    }
};
