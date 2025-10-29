<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDescription extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'm_product_description';

    protected $guarded = [];

    public function product()
{
    return $this->belongsTo(Product::class);
}
}
