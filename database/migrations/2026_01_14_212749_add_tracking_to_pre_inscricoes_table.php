<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pre_inscricoes', function (Blueprint $table) {
            // Origem do lead (Discord / Instagram / Site / etc)
            $table->string('origem')->nullable()->after('discord_id');

            // Auditoria básica
            $table->ipAddress('ip')->nullable()->after('origem');
            $table->text('user_agent')->nullable()->after('ip');
        });
    }

    public function down(): void
    {
        Schema::table('pre_inscricoes', function (Blueprint $table) {
            $table->dropColumn([
                'origem',
                'ip',
                'user_agent',
            ]);
        });
    }
};
