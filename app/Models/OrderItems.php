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
        'discounted_price',
        'item_discount_percentage',
        'taxable_amount',
        'gst_rate',
        'gst_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'total_amount',
        'order_status',
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
        'total_amount' => 'decimal:2',
        'quantity' => 'integer',
        'is_new' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(OrderManage::class, 'order_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
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
}