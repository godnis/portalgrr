<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rh_hierarquia_records', function (Blueprint $table) {

            // vínculo opcional com usuário do sistema
            if (!Schema::hasColumn('rh_hierarquia_records', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->index();
            }

            if (!Schema::hasColumn('rh_hierarquia_records', 'cpf')) {
                $table->string('cpf', 32)->nullable();
            }
            if (!Schema::hasColumn('rh_hierarquia_records', 'nome')) {
                $table->string('nome', 140)->nullable(); // vamos preencher depois e tornar required no app
            }
            if (!Schema::hasColumn('rh_hierarquia_records', 'equipe')) {
                $table->string('equipe', 30)->nullable();
            }
            if (!Schema::hasColumn('rh_hierarquia_records', 'cargo')) {
                $table->string('cargo', 80)->nullable();
            }

            if (!Schema::hasColumn('rh_hierarquia_records', 'efetivacao')) {
                $table->string('efetivacao', 30)->default('efetivo');
            }
            if (!Schema::hasColumn('rh_hierarquia_records', 'status')) {
                $table->string('status', 40)->default('em_exercicio');
            }

            if (!Schema::hasColumn('rh_hierarquia_records', 'admissao')) {
                $table->date('admissao')->nullable();
            }
            if (!Schema::hasColumn('rh_hierarquia_records', 'ultima_promocao')) {
                $table->date('ultima_promocao')->nullable();
            }

            if (!Schema::hasColumn('rh_hierarquia_records', 'serial')) {
                $table->string('serial', 40)->nullable();
            }
            if (!Schema::hasColumn('rh_hierarquia_records', 'discord_id')) {
                $table->string('discord_id', 40)->nullable();
            }

            if (!Schema::hasColumn('rh_hierarquia_records', 'funcao_obs')) {
                $table->string('funcao_obs', 220)->nullable();
            }
            if (!Schema::hasColumn('rh_hierarquia_records', 'instrutor')) {
                $table->boolean('instrutor')->default(false);
            }

            foreach (['pop','clt','cap','ctb','cta','satb','bopm','gmp','doa'] as $c) {
                if (!Schema::hasColumn('rh_hierarquia_records', $c)) {
                    $table->boolean($c)->default(false);
                }
            }

            if (!Schema::hasColumn('rh_hierarquia_records', 'medalhas')) {
                $table->string('medalhas', 200)->nullable();
            }
            if (!Schema::hasColumn('rh_hierarquia_records', 'alinhamento')) {
                $table->string('alinhamento', 200)->nullable();
            }

            if (!Schema::hasColumn('rh_hierarquia_records', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
        });

        // FKs (se não existirem)
        Schema::table('rh_hierarquia_records', function (Blueprint $table) {
            // Tenta adicionar FKs sem quebrar (SQLite é chato, mas geralmente aceita)
            try { $table->foreign('user_id')->references('id')->on('users')->nullOnDelete(); } catch (\Throwable $e) {}
            try { $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete(); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('rh_hierarquia_records', function (Blueprint $table) {
            // Em SQLite, drop de coluna é limitado. Vamos só tentar remover FKs.
            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['updated_by']); } catch (\Throwable $e) {}
        });
    }
};
