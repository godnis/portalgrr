<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ padrão branco
            if (!Schema::hasColumn('users', 'tema')) {
                $table->string('tema', 10)->default('light')->after('bio'); // ajuste "after" se precisar
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tema')) {
                $table->dropColumn('tema');
            }
        });
    }
};
