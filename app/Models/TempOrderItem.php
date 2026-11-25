<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempOrderItem extends Model
{
    protected $table = 'temp_order_items';
    protected $fillable = [
        'temp_order_id',
        'subcategory_id',
        'quantity',
        'price',
        'addons',
        'discounted_price',
        'item_discount_percentage',
        'taxable_amount',
        'gst_rate',
        'gst_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'vat_rate',
        'vat_amount',
        'bar_variation_id',
        'bar_variation_name',
        'bar_variation_ml',
        'total_amount',
        'order_status',
        'is_new',
        'restaurant_id',
        'user_id',
        'is_raw_dish',
        'weight_in_kg',
        'weight_gram',
        'per_kg_price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'item_discount_percentage' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'gst_rate' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'bar_variation_ml' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'quantity' => 'integer',
        'is_new' => 'boolean',
        'addons' => 'array',
        'is_raw_dish' => 'boolean',
        'weight_in_kg' => 'decimal:3',
        'weight_gram' => 'integer',
        'per_kg_price' => 'decimal:2',
    ];

    /**
     * Get clean formatted weight representation (e.g. "1 kg 250 g", "500 g", "2 kg")
     */
    public function getFormattedWeightAttribute()
    {
        if (!$this->is_raw_dish && empty($this->weight_in_kg)) {
            return null;
        }
        $kgVal = floatval($this->weight_in_kg ?? 0);
        if ($kgVal <= 0) {
            return null;
        }
        $wholeKg = floor($kgVal);
        $grams = round(($kgVal - $wholeKg) * 1000);
        if ($wholeKg > 0 && $grams > 0) {
            return "{$wholeKg} kg {$grams} gm";
        } elseif ($wholeKg > 0) {
            return "{$wholeKg} kg";
        } else {
            return "{$grams} gm";
        }
    }

    public function menuItem()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id')->withDefault(function ($subcat, $tempItem) {
            $firstAddon = $tempItem->addons_list[0] ?? null;
            $subcat->name = $firstAddon['name'] ?? 'Add-on';
            $subcat->food_type = $firstAddon['food_type'] ?? 'VEG';
            $subcat->price = $tempItem->price ?? 0;
            return $subcat;
        });
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id')->withDefault(function ($subcat, $tempItem) {
            $firstAddon = $tempItem->addons_list[0] ?? null;
            $subcat->name = $firstAddon['name'] ?? 'Add-on';
            $subcat->food_type = $firstAddon['food_type'] ?? 'VEG';
            $subcat->price = $tempItem->price ?? 0;
            return $subcat;
        });
    }

    /**
     * Get structured array of addons associated with this temporary order item
     */
    public function getAddonsListAttribute()
    {
        $raw = $this->addons;
        if (!empty($raw)) {
            if (is_array($raw)) {
                return $raw;
            }
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && !empty($decoded)) {
                    return $decoded;
                }
            }
        }
        return [];
    }

    public function order()
    {
        return $this->belongsTo(TempOrder::class, 'temp_order_id');
    }

    public function tempOrder()
    {
        return $this->belongsTo(TempOrder::class, 'temp_order_id');
    }
}