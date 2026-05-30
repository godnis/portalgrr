<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {

            if (!Schema::hasColumn('relatorios', 'bopm_registros')) {
                $table->longText('bopm_registros')->nullable()->after('bopm');
            }

        });
    }

    public function down(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {

            if (Schema::hasColumn('relatorios', 'bopm_registros')) {
                $table->dropColumn('bopm_registros');
            }

        });
    }
};
