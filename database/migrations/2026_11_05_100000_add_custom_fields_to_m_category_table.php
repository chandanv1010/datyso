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
        Schema::table('m_category', function (Blueprint $table) {
            if (!Schema::hasColumn('m_category', 'short_desc')) {
                $table->text('short_desc')->nullable()->after('name')->comment('Đoạn mở đầu dưới H1');
            }
            if (!Schema::hasColumn('m_category', 'content')) {
                $table->longText('content')->nullable()->after('short_desc')->comment('Nội dung dài dưới lưới sản phẩm');
            }
            if (!Schema::hasColumn('m_category', 'faq')) {
                $table->longText('faq')->nullable()->after('content')->comment('Câu hỏi thường gặp FAQ');
            }
            if (!Schema::hasColumn('m_category', 'seo_title')) {
                $table->string('seo_title', 255)->nullable()->after('banner')->comment('SEO Title riêng');
            }
            if (!Schema::hasColumn('m_category', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title')->comment('Meta Description riêng');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_category', function (Blueprint $table) {
            $table->dropColumn(['short_desc', 'content', 'faq', 'seo_title', 'seo_description']);
        });
    }
};
