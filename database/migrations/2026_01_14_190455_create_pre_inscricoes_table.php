<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pre_inscricoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 80);
            $table->string('discord', 80);
            $table->string('id_fivem', 30)->nullable();
            $table->string('disponibilidade', 60);
            $table->string('experiencia', 60);
            $table->string('mensagem', 800)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_inscricoes');
    }
};
