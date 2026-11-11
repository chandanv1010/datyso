<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('m_contact')) {
            return;
        }

        Schema::table('m_contact', function (Blueprint $table) {
            if (!Schema::hasColumn('m_contact', 'mail_status')) {
                $after = Schema::hasColumn('m_contact', 'ip_address') ? 'ip_address' : 'content';
                $table->string('mail_status', 30)->nullable()->after($after);
            }
            if (!Schema::hasColumn('m_contact', 'mail_error')) {
                $table->text('mail_error')->nullable()->after('mail_status');
            }
            if (!Schema::hasColumn('m_contact', 'mail_sent_at')) {
                $table->timestamp('mail_sent_at')->nullable()->after('mail_error');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('m_contact')) {
            return;
        }

        Schema::table('m_contact', function (Blueprint $table) {
            $dropCols = [];
            foreach (['mail_sent_at', 'mail_error', 'mail_status'] as $col) {
                if (Schema::hasColumn('m_contact', $col)) {
                    $dropCols[] = $col;
                }
            }
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
