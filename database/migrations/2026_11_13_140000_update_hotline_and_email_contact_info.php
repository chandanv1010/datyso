<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cập nhật bảng m_address
        if (Schema::hasTable('m_address')) {
            DB::table('m_address')
                ->whereIn('hotline', ['0963535196', '0963.535.196', '0963 535 196'])
                ->update(['hotline' => '0968945633']);

            DB::table('m_address')
                ->whereIn('phone', ['0963535196', '0963.535.196', '0963 535 196'])
                ->update(['phone' => '0968945633']);

            DB::table('m_address')
                ->whereRaw('LOWER(email) = ?', ['datysojsc@gmail.com'])
                ->update(['email' => 'support@datyso.vn']);
        }

        // 2. Cập nhật nội dung các bảng m_post, m_instruction_manual, m_solution
        $contentTables = ['m_post', 'm_instruction_manual', 'm_solution'];
        foreach ($contentTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'content')) {
                DB::table($table)
                    ->where(function ($query) {
                        $query->where('content', 'like', '%0963%')
                            ->orWhere('content', 'like', '%datysojsc@gmail.com%');
                    })
                    ->chunkById(100, function ($rows) use ($table) {
                        foreach ($rows as $row) {
                            $updatedContent = str_ireplace(
                                ['0963.535.196', '0963535196', '0963 535 196', 'Datysojsc@gmail.com'],
                                ['0968945633', '0968945633', '0968945633', 'support@datyso.vn'],
                                $row->content
                            );
                            if ($updatedContent !== $row->content) {
                                DB::table($table)->where('id', $row->id)->update(['content' => $updatedContent]);
                            }
                        }
                    });
            }
        }

        // 3. Cập nhật bảng systems nếu có chứa hotline hoặc email cũ
        if (Schema::hasTable('systems') && Schema::hasColumn('systems', 'content')) {
            DB::table('systems')
                ->where(function ($query) {
                    $query->where('content', 'like', '%0963%')
                        ->orWhere('content', 'like', '%datysojsc@gmail.com%');
                })
                ->chunkById(100, function ($rows) {
                    foreach ($rows as $row) {
                        $updated = str_ireplace(
                            ['0963.535.196', '0963535196', '0963 535 196', 'Datysojsc@gmail.com'],
                            ['0968945633', '0968945633', '0968945633', 'support@datyso.vn'],
                            $row->content
                        );
                        if ($updated !== $row->content) {
                            DB::table('systems')->where('id', $row->id)->update(['content' => $updated]);
                        }
                    }
                });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('m_address')) {
            DB::table('m_address')->where('hotline', '0968945633')->update(['hotline' => '0963.535.196']);
            DB::table('m_address')->where('phone', '0968945633')->update(['phone' => '0963535196']);
            DB::table('m_address')->where('email', 'support@datyso.vn')->update(['email' => 'Datysojsc@gmail.com']);
        }
    }
};
