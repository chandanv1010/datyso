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
        if (! Schema::hasTable('m_contact')) {
            return;
        }

        Schema::table('m_contact', function (Blueprint $table) {
            if (! Schema::hasColumn('m_contact', 'odoo_synced_at')) {
                $table->timestamp('odoo_synced_at')->nullable()->after('is_supported');
            }
            if (! Schema::hasColumn('m_contact', 'odoo_lead_id')) {
                $table->string('odoo_lead_id', 100)->nullable()->after('odoo_synced_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('m_contact')) {
            return;
        }

        Schema::table('m_contact', function (Blueprint $table) {
            if (Schema::hasColumn('m_contact', 'odoo_lead_id')) {
                $table->dropColumn('odoo_lead_id');
            }
            if (Schema::hasColumn('m_contact', 'odoo_synced_at')) {
                $table->dropColumn('odoo_synced_at');
            }
        });
    }
};
