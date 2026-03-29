<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_name',
        'unit_id',
        'opening_qty',
        'status',
        'restaurant_id',
        'user_id'
    ];

    protected $casts = [
        'opening_qty' => 'decimal:2',
    ];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_id', 'id')
                    ->where('restaurant_id', $this->restaurant_id);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'A');
    }
}