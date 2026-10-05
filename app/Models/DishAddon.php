<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DishAddon extends Model
{
    use HasFactory;

    protected $table = 'dish_addons';

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'name',
        'description',
        'price',
        'food_type',
        'status',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'A');
    }

    public function scopeForRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    public function dishes()
    {
        return $this->belongsToMany(SubCategory::class, 'dish_addon_mappings', 'dish_addon_id', 'sub_category_id')
                    ->where('sub_category.status', '!=', 'D')
                    ->withTimestamps();
    }
}
