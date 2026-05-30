<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditoriasTable extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();

            // ✅ request correlation
            $table->uuid('request_id')->nullable()->index();

            // Quem fez (pode ser null em falha de login)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Ator (espelhado pra filtro rápido)
            $table->string('actor_rg')->nullable()->index();
            $table->string('actor_nome')->nullable();

            // O que aconteceu
            $table->string('acao')->index();

            // Em qual entidade (opcional)
            $table->string('entidade_tipo')->nullable();
            $table->unsignedBigInteger('entidade_id')->nullable();

            // Alvo (quando fizer sentido)
            $table->unsignedBigInteger('alvo_user_id')->nullable()->index();
            $table->string('alvo_rg')->nullable()->index();
            $table->string('alvo_nome')->nullable();

            // Request metadata (ótimo pra auditoria)
            $table->string('route_name')->nullable()->index();
            $table->string('method', 12)->nullable()->index();
            $table->text('url')->nullable();

            // Detalhes extras
            $table->json('detalhes')->nullable();

            // Metadados
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['entidade_tipo', 'entidade_id']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
}
