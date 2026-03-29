<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'total_qty',
        'opening_qty',
        'created_by',
        'restaurant_id'
    ];

    protected $casts = [
        'total_qty' => 'decimal:2',
        'opening_qty' => 'decimal:2',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopeByRestaurant($query)
    {
        return $query->where('restaurant_id', auth()->user()->restaurant_id);
    }

    // Helper method to update inventory
    public static function updateStock($productId, $quantity, $type = 'add')
    {
        $inventory = self::firstOrCreate(
            [
                'product_id' => $productId,
                'restaurant_id' => auth()->user()->restaurant_id
            ],
            [
                'total_qty' => 0,
                'opening_qty' => 0,
                'created_by' => auth()->user()->name
            ]
        );

        if ($type === 'add') {
            $inventory->total_qty += $quantity;
        } else {
            $inventory->total_qty -= $quantity;
            if ($inventory->total_qty < 0) {
                $inventory->total_qty = 0;
            }
        }

        $inventory->save();
        
        return $inventory;
    }

    // Get current stock for a product
    public static function getStock($productId)
    {
        $inventory = self::where('product_id', $productId)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->first();
        
        return $inventory ? $inventory->total_qty : 0;
    }
}