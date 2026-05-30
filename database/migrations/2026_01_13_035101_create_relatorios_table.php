<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelatoriosTable extends Migration
{
    public function up(): void
    {
        Schema::create('relatorios', function (Blueprint $table) {
            $table->id();

            // Autor
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Guarnição
            $table->string('qra_chefe'); // CPF
            $table->string('unidade');
            $table->string('motorista');
            $table->string('terceiro')->nullable();
            $table->string('quarto')->nullable();
            $table->string('quinto')->nullable();

            // Tempo
            $table->date('data_patrulhamento');
            $table->time('inicio_patrulhamento');
            $table->time('final_patrulhamento')->nullable();

            // Apreensões
            $table->integer('pistolas')->nullable();
            $table->integer('smg_fuzil')->nullable();
            $table->integer('municoes')->nullable();
            $table->integer('drogas')->nullable();
            $table->integer('dinheiro')->nullable();
            $table->integer('explosivos')->nullable();
            $table->integer('lockpicks')->nullable();

            // Multas / Ações
            $table->integer('abordagens')->nullable();
            $table->integer('multas')->nullable();
            $table->integer('bopm')->nullable();
            $table->integer('viaturas_fiscalizadas')->nullable();

            // Controle
            $table->enum('status', ['pendente', 'aprovado', 'reprovado'])->default('pendente');
            $table->foreignId('aprovado_por')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relatorios');
    }
}
