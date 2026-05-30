<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grr_manual_articles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_id')->constrained('grr_manual_sections')->cascadeOnDelete();

            $table->string('article_number');

            $table->longText('body');

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grr_manual_articles');
    }
};
