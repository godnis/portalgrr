<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');        // quem abriu
            $table->unsignedBigInteger('assigned_to')->nullable(); // admin responsável (nivel 9+)

            $table->string('categoria', 40);              // suporte_geral, administrativo, denuncia...
            $table->string('prioridade', 20)->default('normal'); // baixa, normal, alta, urgente

            $table->string('titulo', 120);
            $table->text('descricao');

            $table->string('status', 20)->default('aberto'); // aberto, em_andamento, aguardando_usuario, resolvido, fechado

            // auditoria
            $table->string('ip', 60)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('fechado_em')->nullable();

            $table->timestamps();

            $table->index(['status', 'categoria']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
