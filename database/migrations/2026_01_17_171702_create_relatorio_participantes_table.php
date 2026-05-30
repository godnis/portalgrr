<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('relatorio_participantes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('relatorio_id')
                ->constrained('relatorios')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // CHEFE | P2 | P3 | P4
            $table->string('papel', 12)->default('P2');

            $table->timestamps();

            // Evita duplicar o mesmo usuário no mesmo relatório
            $table->unique(['relatorio_id', 'user_id']);

            $table->index('user_id');
            $table->index('relatorio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relatorio_participantes');
    }
};
