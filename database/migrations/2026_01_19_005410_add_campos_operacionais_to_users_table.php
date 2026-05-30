<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $cols = Schema::getColumnListing('users');

        Schema::table('users', function (Blueprint $table) use ($cols) {

            if (!in_array('rg', $cols, true)) {
                $table->string('rg', 30)->nullable()->unique()->after('email');
            }

            if (!in_array('cargo', $cols, true)) {
                $table->string('cargo', 80)->nullable()->after('rg');
            }

            if (!in_array('nivel', $cols, true)) {
                $table->unsignedTinyInteger('nivel')->default(1)->after('cargo');
            }

            if (!in_array('status', $cols, true)) {
                $table->string('status', 20)->default('ativo')->after('nivel'); // ativo|suspenso|desligado
            }

            if (!in_array('ativo', $cols, true)) {
                $table->boolean('ativo')->default(true)->after('status');
            }
        });
    }

    public function down(): void
    {
        $cols = Schema::getColumnListing('users');

        Schema::table('users', function (Blueprint $table) use ($cols) {

            // só remove se existir
            if (in_array('ativo', $cols, true)) {
                $table->dropColumn('ativo');
            }

            if (in_array('status', $cols, true)) {
                $table->dropColumn('status');
            }

            if (in_array('nivel', $cols, true)) {
                $table->dropColumn('nivel');
            }

            if (in_array('cargo', $cols, true)) {
                $table->dropColumn('cargo');
            }

            if (in_array('rg', $cols, true)) {
                $table->dropColumn('rg');
            }
        });
    }
};