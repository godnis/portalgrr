<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::transaction(function () {

            // cria tabela temporária com status correto
            DB::statement("
                CREATE TABLE relatorios_tmp (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    user_id INTEGER NOT NULL,
                    qra_chefe VARCHAR NOT NULL,
                    unidade VARCHAR NOT NULL,
                    motorista VARCHAR NOT NULL,
                    terceiro VARCHAR,
                    quarto VARCHAR,
                    quinto VARCHAR,
                    data_patrulhamento DATE NOT NULL,
                    inicio_patrulhamento TIME NOT NULL,
                    final_patrulhamento TIME,
                    pistolas INTEGER,
                    smg_fuzil INTEGER,
                    municoes INTEGER,
                    drogas INTEGER,
                    dinheiro INTEGER,
                    explosivos INTEGER,
                    lockpicks INTEGER,
                    abordagens INTEGER,
                    multas INTEGER,
                    bopm INTEGER,
                    viaturas_fiscalizadas INTEGER,
                    status VARCHAR CHECK (status IN ('em_patrulha','pendente','aprovado','reprovado')) NOT NULL DEFAULT 'pendente',
                    aprovado_por INTEGER,
                    created_at DATETIME,
                    updated_at DATETIME,
                    observacoes TEXT,
                    decisao_obs TEXT,
                    reprovado_por INTEGER,
                    client_token VARCHAR,
                    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
                );
            ");

            // copia dados antigos
            DB::statement("
                INSERT INTO relatorios_tmp
                SELECT * FROM relatorios;
            ");

            // remove tabela antiga
            DB::statement("DROP TABLE relatorios;");

            // renomeia
            DB::statement("ALTER TABLE relatorios_tmp RENAME TO relatorios;");
        });
    }

    public function down(): void
    {
        // rollback manual não necessário
    }
};
