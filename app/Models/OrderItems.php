<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderItems extends Model
{
    use HasFactory;
    protected $table = "order_items";
    protected $fillable = [
        'order_id',
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
        'is_bar_stock_deducted',
        'is_new',
        'restaurant_id',
        'user_id',
        'kot_no',
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
        'is_bar_stock_deducted' => 'boolean',
        'is_new' => 'boolean',
        'addons' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(OrderManage::class, 'order_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id')->withDefault(function ($subcat, $orderItem) {
            $firstAddon = $orderItem->addons_list[0] ?? null;
            $subcat->name = $firstAddon['name'] ?? 'Add-on';
            $subcat->food_type = $firstAddon['food_type'] ?? 'VEG';
            $subcat->price = $orderItem->price ?? 0;
            return $subcat;
        });
    }

    public function barVariation()
    {
        return $this->belongsTo(BarItemVariation::class, 'bar_variation_id');
    }

    /**
     * Get structured array of addons associated with this order item
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

    // Accessor to get item total before GST
    public function getItemSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    // Accessor to get discounted price for single item
    public function getDiscountedPricePerItemAttribute()
    {
        return $this->discounted_price ?: $this->price;
    }

    // Accessor to get total taxable amount
    public function getTotalTaxableAttribute()
    {
        return $this->taxable_amount ?: ($this->price * $this->quantity);
    }

    // Accessor to get formatted GST rate
    public function getGstRateFormattedAttribute()
    {
        return $this->gst_rate ? $this->gst_rate . '%' : '0%';
    }

    /**
     * Generate the next progressive KOT number for a restaurant
     */
    public static function generateNextKotNumber($restaurantId)
    {
        $todayDateStr = Carbon::now()->format('ymd');
        $cacheKey = "kot_seq_{$restaurantId}_{$todayDateStr}";
        $cachedSeq = intval(\Illuminate\Support\Facades\Cache::get($cacheKey, 0));

        // Find the maximum KOT sequence currently in the database for today
        $items = self::where('restaurant_id', $restaurantId)
            ->whereNotNull('kot_no')
            ->where('kot_no', 'like', "KOT-{$todayDateStr}-%")
            ->pluck('kot_no');

        $maxDbSeq = 0;
        foreach ($items as $kot) {
            if (preg_match('/KOT-\d{6}-(\d+)/', $kot, $m)) {
                $seq = intval($m[1]);
                if ($seq > $maxDbSeq) {
                    $maxDbSeq = $seq;
                }
            }
        }

        $nextSequence = max($cachedSeq, $maxDbSeq) + 1;

        // Remember the sequence for today so deletions never regress the KOT counter
        \Illuminate\Support\Facades\Cache::put($cacheKey, $nextSequence, Carbon::now()->endOfDay());

        return "KOT-{$todayDateStr}-" . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Deduct bar inventory (ML in SubCategory and linked Inventory total_qty) for this order item.
     * Guaranteed to only run once per item via is_bar_stock_deducted flag.
     */
    public function deductBarStock()
    {
        if ($this->is_bar_stock_deducted) {
            return false;
        }

        $subcatId = $this->subcategory_id;
        if (!$subcatId) {
            return false;
        }

        $barItem = SubCategory::with('category')->find($subcatId);
        if (!$barItem) {
            return false;
        }

        $isBar = !empty($this->bar_variation_id)
            || floatval($this->vat_rate ?? 0) > 0
            || ($barItem->food_type === 'BAR')
            || ($barItem->category && $barItem->category->category_type === 'BAR');

        if (!$isBar) {
            return false;
        }

        // Determine ML volume per serving
        $mlPerServing = floatval($this->bar_variation_ml ?? 0);
        if ($mlPerServing <= 0 && !empty($this->bar_variation_id)) {
            $variation = BarItemVariation::find($this->bar_variation_id);
            if ($variation && floatval($variation->ml) > 0) {
                $mlPerServing = floatval($variation->ml);
            }
        }
        if ($mlPerServing <= 0 && floatval($barItem->bottle_size_ml ?? 0) > 0) {
            $mlPerServing = floatval($barItem->bottle_size_ml);
        }

        if ($mlPerServing <= 0) {
            return false;
        }

        $qty = max(1, intval($this->quantity ?? 1));
        $totalMlToDeduct = $mlPerServing * $qty;
        $restaurantId = $this->restaurant_id ?: $barItem->restaurant_id;

        // Deduct from SubCategory current_stock_ml
        $currentStockMl = floatval($barItem->current_stock_ml ?? 0);
        $newStockMl = max(0, $currentStockMl - $totalMlToDeduct);
        $barItem->current_stock_ml = $newStockMl;
        $barItem->save();

        // Also sync Product and Inventory table
        $product = Product::where('restaurant_id', $restaurantId)
            ->where('sub_category_id', $barItem->id)
            ->first();

        if ($product) {
            $inventory = Inventory::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'restaurant_id' => $restaurantId,
                ],
                [
                    'total_qty' => $newStockMl,
                    'opening_qty' => $product->opening_qty ?? 0,
                    'created_by' => 'System',
                ]
            );
            $inventory->total_qty = $newStockMl;
            $inventory->save();
        } else {
            Product::syncBarItems($restaurantId);
        }

        $this->is_bar_stock_deducted = 1;
        $this->save();

        \Log::info("Bar KOT Order Served: Deducted {$totalMlToDeduct} ML from [{$barItem->id}] {$barItem->name}. Remaining stock: {$newStockMl} ML.");

        return true;
    }
}