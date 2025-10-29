<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'm_product_image';

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
