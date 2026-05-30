<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private function sqliteIndexExists(string $table, string $indexName): bool
    {
        try {
            $rows = DB::select("PRAGMA index_list('$table')");
            foreach ($rows as $row) {
                // $row->name no sqlite
                if (($row->name ?? null) === $indexName) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // se der qualquer erro, assume que não existe
        }

        return false;
    }

    private function ensureIndex(string $table, string $column): void
    {
        // Nome padrão que o Laravel cria: {table}_{column}_index
        $indexName = "{$table}_{$column}_index";

        // Só checamos isso porque você está em SQLite
        if (!$this->sqliteIndexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->index($column);
            });
        }
    }

    public function up(): void
    {
        // ✅ colunas (só cria se não existir)
        Schema::table('auditorias', function (Blueprint $table) {

            if (!Schema::hasColumn('auditorias', 'request_id')) {
                $table->uuid('request_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('auditorias', 'actor_rg')) {
                $table->string('actor_rg', 30)->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('auditorias', 'actor_nome')) {
                $table->string('actor_nome', 190)->nullable()->after('actor_rg');
            }

            if (!Schema::hasColumn('auditorias', 'alvo_user_id')) {
                $table->unsignedBigInteger('alvo_user_id')->nullable()->after('entidade_id');
            }

            if (!Schema::hasColumn('auditorias', 'alvo_rg')) {
                $table->string('alvo_rg', 30)->nullable()->after('alvo_user_id');
            }

            if (!Schema::hasColumn('auditorias', 'alvo_nome')) {
                $table->string('alvo_nome', 190)->nullable()->after('alvo_rg');
            }

            if (!Schema::hasColumn('auditorias', 'route_name')) {
                $table->string('route_name')->nullable()->after('acao');
            }

            if (!Schema::hasColumn('auditorias', 'method')) {
                $table->string('method', 10)->nullable()->after('route_name');
            }

            if (!Schema::hasColumn('auditorias', 'url')) {
                $table->text('url')->nullable()->after('method');
            }
        });

        // ✅ índices (só cria se ainda não existir)
        // IMPORTANTE: só tenta criar se a coluna existir
        if (Schema::hasColumn('auditorias', 'request_id')) {
            $this->ensureIndex('auditorias', 'request_id');
        }
        if (Schema::hasColumn('auditorias', 'actor_rg')) {
            $this->ensureIndex('auditorias', 'actor_rg');
        }
        if (Schema::hasColumn('auditorias', 'alvo_user_id')) {
            $this->ensureIndex('auditorias', 'alvo_user_id');
        }
        if (Schema::hasColumn('auditorias', 'alvo_rg')) {
            $this->ensureIndex('auditorias', 'alvo_rg');
        }
        if (Schema::hasColumn('auditorias', 'route_name')) {
            $this->ensureIndex('auditorias', 'route_name');
        }
    }

    public function down(): void
    {
        // Em SQLite, rollback de alter table + index pode ser chato/limitado.
        // Como você está usando migrate:fresh pra "zerar", o down aqui pode ser simples.
        // Se quiser mesmo assim tentar, dá pra implementar depois.
    }
};
