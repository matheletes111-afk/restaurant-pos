<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sub_category_id',
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

    public function barItem()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
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

    /**
     * Ensure an active 'ML' unit exists for the given restaurant and return it.
     */
    public static function ensureMlUnit($restaurantId)
    {
        $mlUnit = Unit::where('restaurant_id', $restaurantId)
            ->whereRaw('UPPER(TRIM(name)) = ?', ['ML'])
            ->first();

        if ($mlUnit) {
            if ($mlUnit->status !== 'A') {
                $mlUnit->status = 'A';
                $mlUnit->save();
            }
            return $mlUnit;
        }

        return Unit::create([
            'name'          => 'ML',
            'status'        => 'A',
            'restaurant_id' => $restaurantId,
            'created_by'    => auth()->check() ? (auth()->user()->name ?? 'System') : 'System',
        ]);
    }

    /**
     * Synchronize all active Bar Items (from sub_category) into products & inventories with unit = ML.
     */
    public static function syncBarItems($restaurantId)
    {
        if (!$restaurantId) {
            return null;
        }

        $mlUnit = self::ensureMlUnit($restaurantId);

        $barItems = SubCategory::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->where(function ($q) {
                $q->where('food_type', 'BAR')
                  ->orWhereHas('category', function ($cq) {
                      $cq->where('category_type', 'BAR');
                  });
            })
            ->get();

        foreach ($barItems as $barItem) {
            $product = self::withTrashed()
                ->where('restaurant_id', $restaurantId)
                ->where('sub_category_id', $barItem->id)
                ->first();

            if (!$product) {
                $product = self::withTrashed()
                    ->where('restaurant_id', $restaurantId)
                    ->whereNull('sub_category_id')
                    ->whereRaw('LOWER(TRIM(product_name)) = ?', [strtolower(trim($barItem->name))])
                    ->first();
            }

            if (!$product) {
                $product = new self();
                $product->restaurant_id = $restaurantId;
                $product->user_id = $barItem->user_id ?: (auth()->id() ?? null);
            }

            if ($product->trashed()) {
                $product->restore();
            }

            $product->sub_category_id = $barItem->id;
            $product->product_name = $barItem->name;
            $product->unit_id = $mlUnit->id;
            $product->opening_qty = floatval($barItem->opening_qty ?? 0);
            $product->status = 'A';
            $product->save();

            // Sync Inventory record with bar item's current_stock_ml
            $inventory = Inventory::withTrashed()->firstOrNew([
                'product_id'    => $product->id,
                'restaurant_id' => $restaurantId,
            ]);
            if ($inventory->exists && method_exists($inventory, 'trashed') && $inventory->trashed()) {
                $inventory->restore();
            }
            $inventory->opening_qty = floatval($barItem->opening_qty ?? 0);
            $inventory->total_qty = floatval($barItem->current_stock_ml ?? 0);
            if (!$inventory->created_by) {
                $inventory->created_by = auth()->check() ? (auth()->user()->name ?? 'System') : 'System';
            }
            $inventory->save();
        }

        return $mlUnit;
    }
}