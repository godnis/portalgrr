<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rh_controle_saidas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('hierarquia_id')->nullable()->index();
            $table->string('cpf', 32)->nullable();
            $table->string('nome', 160)->nullable();
            $table->string('cargo', 100)->nullable();
            $table->string('efetivacao', 30)->nullable();
            $table->string('status', 40)->nullable();

            $table->date('admissao')->nullable();
            $table->date('ultima_promocao')->nullable();
            $table->string('serial', 60)->nullable();
            $table->string('discord_id', 60)->nullable();

            $table->dateTime('saida_em');
            $table->string('motivo_saida', 120);
            $table->string('motivo_detalhe', 255)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_controle_saidas');
    }
};
