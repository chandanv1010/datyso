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
        if (!Schema::hasTable('m_contact')) {
            return;
        }

        Schema::table('m_contact', function (Blueprint $table) {
            if (Schema::hasColumn('m_contact', 'email')) {
                $table->string('email', 150)->nullable()->change();
            }
            if (Schema::hasColumn('m_contact', 'phone')) {
                $table->string('phone', 20)->change();
            }
            if (!Schema::hasColumn('m_contact', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('m_contact', 'province')) {
                $table->string('province', 120)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('m_contact', 'demand')) {
                $table->text('demand')->nullable()->after('province');
            }
            if (!Schema::hasColumn('m_contact', 'source_type')) {
                $table->string('source_type', 50)->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('m_contact', 'source_name')) {
                $table->string('source_name', 255)->nullable()->after('source_type');
            }
            if (!Schema::hasColumn('m_contact', 'source_url')) {
                $table->string('source_url', 500)->nullable()->after('source_name');
            }
            if (!Schema::hasColumn('m_contact', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('source_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('m_contact')) {
            return;
        }

        $columns = array_filter([
            Schema::hasColumn('m_contact', 'category_id') ? 'category_id' : null,
            Schema::hasColumn('m_contact', 'province') ? 'province' : null,
            Schema::hasColumn('m_contact', 'demand') ? 'demand' : null,
            Schema::hasColumn('m_contact', 'source_type') ? 'source_type' : null,
            Schema::hasColumn('m_contact', 'source_name') ? 'source_name' : null,
            Schema::hasColumn('m_contact', 'source_url') ? 'source_url' : null,
            Schema::hasColumn('m_contact', 'ip_address') ? 'ip_address' : null,
        ]);

        if (!empty($columns)) {
            Schema::table('m_contact', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
