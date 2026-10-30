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

    // 🔗 Belongs to a Bar Brand
    public function brand()
    {
        return $this->belongsTo(BarBrand::class, 'brand_id');
    }

    // 🔗 Has Many Bar Item Variations (Pegs, Pours, Sizes)
    public function variations()
    {
        return $this->hasMany(BarItemVariation::class, 'sub_category_id')
                    ->where('status', '!=', 'D')
                    ->orderBy('ml', 'asc');
    }

    // Accessor for discount attribute
    public function getDiscountAttribute()
    {
        return $this->attributes['discount_percentage'] ?? 0;
    }
}
