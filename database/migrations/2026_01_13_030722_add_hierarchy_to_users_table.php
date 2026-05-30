<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHierarchyToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rg')->unique()->after('email');
            $table->string('cargo')->after('rg');
            $table->unsignedTinyInteger('nivel')->after('cargo');
            $table->boolean('ativo')->default(true)->after('nivel');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rg', 'cargo', 'nivel', 'ativo']);
        });
    }
}
