<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $table = "sub_category";
    protected $guarded = [];
    // 🔗 Belongs to a Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // 🔗 Mapped Dish Addons
    public function addons()
    {
        return $this->belongsToMany(DishAddon::class, 'dish_addon_mappings', 'sub_category_id', 'dish_addon_id')
                    ->where('dish_addons.status', '!=', 'D')
                    ->withTimestamps();
    }
}
