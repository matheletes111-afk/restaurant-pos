<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableCategory extends Model
{
    use HasFactory;

    protected $table = 'table_categories';

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'name',
        'description',
        'status',
    ];

    /**
     * Relationship: Tables belonging to this category.
     */
    public function tables()
    {
        return $this->hasMany(TableManage::class, 'table_category_id')->where('status', '!=', 'D');
    }

    /**
     * Relationship: Restaurant.
     */
    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    /**
     * Relationship: User who created this category.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope: Only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'A');
    }
}
