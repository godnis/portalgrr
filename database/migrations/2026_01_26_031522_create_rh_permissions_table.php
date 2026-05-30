<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rh_permissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->unique();

            $table->boolean('edit_hierarquia')->default(false);
            $table->boolean('edit_controle_saida')->default(false);
            $table->boolean('edit_estatistica_efetivo')->default(false);
            $table->boolean('edit_instrutores')->default(false);
            $table->boolean('edit_equipe')->default(false);

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_permissions');
    }
};
