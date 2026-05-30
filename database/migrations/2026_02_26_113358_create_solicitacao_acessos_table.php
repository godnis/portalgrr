<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('solicitacao_acessos', function (Blueprint $table) {
            $table->id();

            $table->string('nome', 80);
            $table->string('sobrenome', 80);
            $table->string('rg', 30);
            $table->string('email', 180);

            // pendente | aprovado | reprovado
            $table->string('status', 20)->default('pendente');

            $table->text('motivo')->nullable();

            $table->unsignedBigInteger('decidido_por')->nullable();
            $table->timestamp('decidido_em')->nullable();

            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 250)->nullable();

            $table->timestamps();

            // 🔒 bloqueios
            $table->unique('rg');
            $table->unique('email');

            // se você quer bloquear nome+sobrenome também:
            $table->unique(['nome', 'sobrenome']);

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacao_acessos');
    }
};
