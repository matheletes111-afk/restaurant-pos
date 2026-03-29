<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOut extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stockout_no',
        'stockout_date',
        'total_items',
        'remarks',
        'restaurant_id',
        'user_id',
        'status'
    ];

    protected $casts = [
        'stockout_date' => 'date',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(StockOutItem::class, 'stockout_id'); // Specify foreign key
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
    public function scopeByRestaurant($query)
    {
        return $query->where('restaurant_id', auth()->user()->restaurant_id);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'COMPLETED');
    }

    // Calculate total quantity
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }
}