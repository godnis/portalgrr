<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rh_permissions', function (Blueprint $table) {

            // Permissão para editar HIERARQUIA
            if (!Schema::hasColumn('rh_permissions', 'can_hierarquia')) {
                $table->boolean('can_hierarquia')
                      ->default(false)
                      ->after('passaporte');
            }

            // Permissão para editar CONTROLE DE SAÍDA
            if (!Schema::hasColumn('rh_permissions', 'can_controle_saida')) {
                $table->boolean('can_controle_saida')
                      ->default(false)
                      ->after('can_hierarquia');
            }

            // Observações administrativas
            if (!Schema::hasColumn('rh_permissions', 'observacao')) {
                $table->string('observacao', 255)
                      ->nullable()
                      ->after('can_controle_saida');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rh_permissions', function (Blueprint $table) {

            $cols = [];

            if (Schema::hasColumn('rh_permissions', 'can_hierarquia')) {
                $cols[] = 'can_hierarquia';
            }

            if (Schema::hasColumn('rh_permissions', 'can_controle_saida')) {
                $cols[] = 'can_controle_saida';
            }

            if (Schema::hasColumn('rh_permissions', 'observacao')) {
                $cols[] = 'observacao';
            }

            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
