<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNovasAcoesToRelatoriosTable extends Migration
{
    public function up(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {
            $table->integer('apoio')->nullable()->after('abordagens');
            $table->integer('incursao')->nullable()->after('apoio');
            $table->integer('negociacao')->nullable()->after('incursao');
            $table->integer('blitz')->nullable()->after('negociacao');
            $table->integer('escolta')->nullable()->after('blitz');
        });
    }

    public function down(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {
            $table->dropColumn([
                'apoio',
                'incursao',
                'negociacao',
                'blitz',
                'escolta',
            ]);
        });
    }
}
