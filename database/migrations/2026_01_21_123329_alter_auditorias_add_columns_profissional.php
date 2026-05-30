<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auditorias', function (Blueprint $table) {

            if (!Schema::hasColumn('auditorias', 'request_id')) {
                $table->uuid('request_id')->nullable()->after('id')->index();
            }

            if (!Schema::hasColumn('auditorias', 'actor_rg')) {
                $table->string('actor_rg')->nullable()->after('user_id')->index();
            }

            if (!Schema::hasColumn('auditorias', 'actor_nome')) {
                $table->string('actor_nome')->nullable()->after('actor_rg');
            }

            if (!Schema::hasColumn('auditorias', 'alvo_user_id')) {
                $table->unsignedBigInteger('alvo_user_id')->nullable()->after('entidade_id')->index();
            }

            if (!Schema::hasColumn('auditorias', 'alvo_rg')) {
                $table->string('alvo_rg')->nullable()->after('alvo_user_id')->index();
            }

            if (!Schema::hasColumn('auditorias', 'alvo_nome')) {
                $table->string('alvo_nome')->nullable()->after('alvo_rg');
            }

            if (!Schema::hasColumn('auditorias', 'route_name')) {
                $table->string('route_name')->nullable()->after('alvo_nome')->index();
            }

            if (!Schema::hasColumn('auditorias', 'method')) {
                $table->string('method', 12)->nullable()->after('route_name')->index();
            }

            if (!Schema::hasColumn('auditorias', 'url')) {
                $table->text('url')->nullable()->after('method');
            }

            // índices já existentes: acao, entidade_tipo+entidade_id, created_at (ok)
        });
    }

    public function down(): void
    {
        Schema::table('auditorias', function (Blueprint $table) {
            if (Schema::hasColumn('auditorias', 'url')) $table->dropColumn('url');
            if (Schema::hasColumn('auditorias', 'method')) $table->dropColumn('method');
            if (Schema::hasColumn('auditorias', 'route_name')) $table->dropColumn('route_name');

            if (Schema::hasColumn('auditorias', 'alvo_nome')) $table->dropColumn('alvo_nome');
            if (Schema::hasColumn('auditorias', 'alvo_rg')) $table->dropColumn('alvo_rg');
            if (Schema::hasColumn('auditorias', 'alvo_user_id')) $table->dropColumn('alvo_user_id');

            if (Schema::hasColumn('auditorias', 'actor_nome')) $table->dropColumn('actor_nome');
            if (Schema::hasColumn('auditorias', 'actor_rg')) $table->dropColumn('actor_rg');

            if (Schema::hasColumn('auditorias', 'request_id')) $table->dropColumn('request_id');
        });
    }
};
