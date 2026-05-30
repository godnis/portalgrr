<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rh_permissoes', function (Blueprint $table) {
            $table->id();

            // usuário que recebe a permissão
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // permissões
            $table->boolean('pode_hierarquia')->default(false);
            $table->boolean('pode_controle_saida')->default(false);

            // auditoria simples
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_permissoes');
    }
};
