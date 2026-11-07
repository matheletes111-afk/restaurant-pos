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

    // Helper method to update inventory (also syncs Bar Inventory in sub_category.current_stock_ml)
    public static function updateStock($productId, $quantity, $type = 'add')
    {
        $product = Product::find($productId);
        $restaurantId = $product ? $product->restaurant_id : (auth()->check() ? auth()->user()->restaurant_id : null);
        $createdBy = auth()->check() ? auth()->user()->name : 'System';

        $inventory = self::firstOrCreate(
            [
                'product_id' => $productId,
                'restaurant_id' => $restaurantId
            ],
            [
                'total_qty' => 0,
                'opening_qty' => 0,
                'created_by' => $createdBy
            ]
        );

        // If this product is linked to a Bar Item (SubCategory), update Bar Inventory (current_stock_ml in ML)
        if ($product && !empty($product->sub_category_id)) {
            $barItem = SubCategory::find($product->sub_category_id);
            if ($barItem) {
                $currentMl = (float) ($barItem->current_stock_ml ?? 0);
                if ($type === 'add') {
                    $newMl = $currentMl + (float) $quantity;
                } else {
                    $newMl = max(0, $currentMl - (float) $quantity);
                }
                $barItem->current_stock_ml = $newMl;
                $barItem->save();

                // Keep general inventory table in sync with Bar Inventory ML stock
                $inventory->total_qty = $newMl;
                $inventory->save();

                return $inventory;
            }
        }

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

    // Get current stock for a product (returns Bar Inventory current_stock_ml if linked to a Bar Item)
    public static function getStock($productId)
    {
        $product = Product::find($productId);
        if ($product && !empty($product->sub_category_id)) {
            $barItem = SubCategory::find($product->sub_category_id);
            if ($barItem) {
                return (float) ($barItem->current_stock_ml ?? 0);
            }
        }

        $restaurantId = $product ? $product->restaurant_id : (auth()->check() ? auth()->user()->restaurant_id : null);
        $inventory = self::where('product_id', $productId)
            ->where('restaurant_id', $restaurantId)
            ->first();

        return $inventory ? $inventory->total_qty : 0;
    }
}
