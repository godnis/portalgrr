<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Status do oficial
            $table->string('status', 20)->default('ativo')->after('nivel');

            // Controle de suspensão
            $table->timestamp('suspenso_em')->nullable()->after('status');
            $table->unsignedBigInteger('suspenso_por')->nullable()->after('suspenso_em');
            $table->text('motivo_suspensao')->nullable()->after('suspenso_por');

            $table->index('status');
            $table->foreign('suspenso_por')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['suspenso_por']);
            $table->dropIndex(['status']);

            $table->dropColumn([
                'status',
                'suspenso_em',
                'suspenso_por',
                'motivo_suspensao',
            ]);
        });
    }
};
