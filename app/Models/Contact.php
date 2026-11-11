<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'm_contact';

    protected $guarded = [];

    protected $casts = [
        'mail_sent_at' => 'datetime',
    ];

    protected static bool $leadSchemaEnsured = false;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Tự động kiểm tra và bổ sung các cột lead mở rộng cho bảng m_contact
     * (bao gồm cho phép cột email NULL) để hoạt động đồng bộ trên cả Local và Hosting
     * ngay cả khi deploy qua cPanel Git không chạy CLI migrate.
     */
    public static function ensureLeadSchema(bool $force = false): void
    {
        if (static::$leadSchemaEnsured && !$force) {
            return;
        }

        try {
            if (!Schema::hasTable('m_contact')) {
                return;
            }

            $missingColumns = !Schema::hasColumn('m_contact', 'category_id')
                || !Schema::hasColumn('m_contact', 'province')
                || !Schema::hasColumn('m_contact', 'demand')
                || !Schema::hasColumn('m_contact', 'source_type')
                || !Schema::hasColumn('m_contact', 'source_name')
                || !Schema::hasColumn('m_contact', 'source_url')
                || !Schema::hasColumn('m_contact', 'ip_address')
                || !Schema::hasColumn('m_contact', 'mail_status')
                || !Schema::hasColumn('m_contact', 'mail_error')
                || !Schema::hasColumn('m_contact', 'mail_sent_at');

            $emailNotNullable = false;
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                $columnInfo = DB::selectOne(
                    "SELECT IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'm_contact' AND COLUMN_NAME = 'email'"
                );
                if ($columnInfo && strtoupper((string) ($columnInfo->IS_NULLABLE ?? '')) === 'NO') {
                    $emailNotNullable = true;
                }
            } elseif ($driver === 'sqlite') {
                $cols = DB::select("PRAGMA table_info('m_contact')");
                foreach ($cols as $col) {
                    if (($col->name ?? '') === 'email' && (int) ($col->notnull ?? 0) === 1) {
                        $emailNotNullable = true;
                        break;
                    }
                }
            }

            if ($missingColumns || $emailNotNullable) {
                Schema::table('m_contact', function (Blueprint $table) use ($emailNotNullable) {
                    if ($emailNotNullable) {
                        $table->string('email', 150)->nullable()->change();
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
                    if (!Schema::hasColumn('m_contact', 'mail_status')) {
                        $table->string('mail_status', 30)->nullable()->after('ip_address');
                    }
                    if (!Schema::hasColumn('m_contact', 'mail_error')) {
                        $table->text('mail_error')->nullable()->after('mail_status');
                    }
                    if (!Schema::hasColumn('m_contact', 'mail_sent_at')) {
                        $table->timestamp('mail_sent_at')->nullable()->after('mail_error');
                    }
                });
            }

            static::$leadSchemaEnsured = true;
        } catch (\Throwable $e) {
            Log::warning('Could not auto-migrate m_contact lead columns: ' . $e->getMessage());
        }
    }
}
