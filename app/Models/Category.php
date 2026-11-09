<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'm_category';

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function child()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'm_product_category', 'category_id', 'product_id');
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    /**
     * Lấy toàn bộ ID của danh mục này và tất cả các danh mục con cháu đang hoạt động.
     *
     * @return array<int>
     */
    public function getAllChildrenIds(): array
    {
        $ids = [(int) $this->id];
        $children = Category::where('parent_id', $this->id)->where('status', 1)->get();
        foreach ($children as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }
        return array_values(array_unique($ids));
    }
}
