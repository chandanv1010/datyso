<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostRate extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'm_post_rate';

    protected $guarded = [];
}
