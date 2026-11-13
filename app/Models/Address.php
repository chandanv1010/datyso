<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'm_address';

    protected $guarded = [];

    /**
     * Tự động chuẩn hóa hotline hiển thị sang số mới 0968945633
     */
    public function getHotlineAttribute($value)
    {
        if (in_array((string) $value, ['0963535196', '0963.535.196', '0963 535 196'], true)) {
            return '0968945633';
        }

        return $value;
    }

    /**
     * Tự động chuẩn hóa số điện thoại hiển thị sang số mới 0968945633
     */
    public function getPhoneAttribute($value)
    {
        if (in_array((string) $value, ['0963535196', '0963.535.196', '0963 535 196'], true)) {
            return '0968945633';
        }

        return $value;
    }

    /**
     * Tự động chuẩn hóa email hiển thị sang email mới support@datyso.vn
     */
    public function getEmailAttribute($value)
    {
        if (strcasecmp((string) $value, 'Datysojsc@gmail.com') === 0) {
            return 'support@datyso.vn';
        }

        return $value;
    }
}
