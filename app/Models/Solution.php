<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    use HasFactory;

    protected $table = 'm_solution';

    protected $guarded = [];

    /**
     * Tự động thay thế hotline và email cũ trong nội dung giải pháp sang thông tin mới
     */
    public function getContentAttribute($value)
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        return str_ireplace(
            ['0963.535.196', '0963535196', '0963 535 196', 'Datysojsc@gmail.com'],
            ['0968945633', '0968945633', '0968945633', 'support@datyso.vn'],
            $value
        );
    }
}
