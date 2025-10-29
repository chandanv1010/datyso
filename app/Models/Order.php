<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    
    protected $guarded = [];


    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'order_product')->withPivot(['qty', 'price', 'subtotal', 'product_name'])
                ->withTimestamps();;
    }


}
 