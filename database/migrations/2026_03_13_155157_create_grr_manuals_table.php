<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grr_manuals', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('kicker')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();

            $table->string('status_label')->nullable();
            $table->string('environment_label')->nullable();

            $table->string('alert_title')->nullable();
            $table->text('alert_text')->nullable();

            $table->boolean('is_published')->default(true);
            $table->integer('version')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grr_manuals');
    }
};
