<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('m_products', function (Blueprint $table) {
            if (!Schema::hasColumn('m_products', 'seo_title')) {
                $table->string('seo_title', 255)->nullable()->after('slug')->comment('SEO Title riêng');
            }
            if (!Schema::hasColumn('m_products', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title')->comment('Meta Description riêng');
            }
            if (!Schema::hasColumn('m_products', 'seo_keywords')) {
                $table->string('seo_keywords', 255)->nullable()->after('seo_description')->comment('Meta Keywords riêng');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_products', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['seo_title', 'seo_description', 'seo_keywords'] as $col) {
                if (Schema::hasColumn('m_products', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
