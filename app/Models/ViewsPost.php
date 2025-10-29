<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewsPost extends Model
{
    use HasFactory;

    protected $table = 'm_views_post';
    
    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
