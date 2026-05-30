<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grr_manual_articles', function (Blueprint $table) {
            if (!Schema::hasColumn('grr_manual_articles', 'article_number')) {
                $table->string('article_number', 50)->nullable()->after('section_id');
            }

            if (!Schema::hasColumn('grr_manual_articles', 'title')) {
                $table->string('title')->nullable()->after('article_number');
            }

            if (!Schema::hasColumn('grr_manual_articles', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('sort_order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('grr_manual_articles', function (Blueprint $table) {
            if (Schema::hasColumn('grr_manual_articles', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('grr_manual_articles', 'title')) {
                $table->dropColumn('title');
            }

            if (Schema::hasColumn('grr_manual_articles', 'article_number')) {
                $table->dropColumn('article_number');
            }
        });
    }
};
