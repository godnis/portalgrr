<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Model que recebe a notificação (User)
            $table->string('type');

            // morphs = notifiable_id + notifiable_type (já cria o índice automaticamente)
            $table->morphs('notifiable');

            // conteúdo da notificação (JSON)
            $table->json('data');

            // quando foi lida
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // ✅ NÃO criar index manual aqui (morphs já cria)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
