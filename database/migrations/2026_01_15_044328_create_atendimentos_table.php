<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();

            $table->string('tipo', 30);          // Denúncia | Solicitação | Sugestão | Elogio
            $table->string('assunto', 80);
            $table->text('mensagem');

            $table->string('nome', 80)->nullable();
            $table->string('contato', 120)->nullable();
            $table->string('prova_url', 255)->nullable();

            $table->string('status', 20)->default('aberto'); // aberto | em_analise | resolvido | arquivado
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
