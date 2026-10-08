<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrderItems;

class OrderManage extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "orders";
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'table_id',
        'order_type',
        'discount',
        'discount_percentage',
        'total_amount',
        'taxable_amount',
        'gst_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'grand_total',
        'round_off',
        'amount_paid',
        'payment_status',
        'payment_method',
        'remarks',
        'order_status',
        'user_id',
        'restaurant_id',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'round_off' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function table()
    {
        return $this->belongsTo(TableManage::class, 'table_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    // Accessor to get formatted discount percentage
    public function getDiscountPercentageFormattedAttribute()
    {
        return $this->discount_percentage ? $this->discount_percentage . '%' : '0%';
    }

    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    /**
     * Recalculate order totals based on order items
     */
    public function recalculateTotals()
    {
        $items = $this->orderItems()->get();
        $originalSubtotal = 0;
        $totalTaxable = 0;
        $totalGst = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        $totalItemDiscount = 0;

        foreach ($items as $item) {
            $basePrice = floatval($item->price);
            $qty = max(1, intval($item->quantity ?? 1));
            $itemDiscPercent = floatval($item->item_discount_percentage ?? 0);
            $discPrice = floatval($item->discounted_price ?: ($basePrice - ($basePrice * $itemDiscPercent / 100)));

            $addonsCost = 0;
            if (!empty($item->addons_list) && is_array($item->addons_list)) {
                foreach ($item->addons_list as $a) {
                    $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                }
            }
            $isAddon = empty($item->subcategory_id);
            $lineOrig = $isAddon ? ($basePrice * $qty) : (($basePrice * $qty) + $addonsCost);
            $lineTax = $isAddon ? ($discPrice * $qty) : (($discPrice * $qty) + $addonsCost);
            $gstRate = floatval($item->gst_rate ?? 0);
            $lineGst = ($lineTax * $gstRate) / 100;
            $lineCgst = ($lineTax * ($gstRate / 2)) / 100;
            $lineSgst = ($lineTax * ($gstRate / 2)) / 100;
            $lineTotal = $lineTax + $lineGst;

            $itemDiscountAmt = ($basePrice * $itemDiscPercent / 100) * $qty;

            // Ensure item attributes are consistent
            $item->taxable_amount = $lineTax;
            $item->gst_amount = $lineGst;
            $item->cgst_amount = $lineCgst;
            $item->sgst_amount = $lineSgst;
            $item->total_amount = $lineTotal;
            $item->discounted_price = $discPrice;
            $item->save();

            $originalSubtotal += $lineOrig;
            $totalTaxable += $lineTax;
            $totalGst += $lineGst;
            $totalCgst += $lineCgst;
            $totalSgst += $lineSgst;
            $totalItemDiscount += $itemDiscountAmt;
        }

        $orderDiscountPercent = floatval($this->discount_percentage ?? 0);
        $orderDiscountAmount = ($totalTaxable * $orderDiscountPercent) / 100;
        $totalDiscountAll = $totalItemDiscount + $orderDiscountAmount;
        $taxableAfterOrderDiscount = $totalTaxable - $orderDiscountAmount;

        $grandTotal = $taxableAfterOrderDiscount + $totalGst;
        $finalTotal = round($grandTotal);
        $roundOff = $finalTotal - $grandTotal;

        $this->total_amount = $originalSubtotal;
        $this->taxable_amount = $totalTaxable;
        $this->gst_amount = $totalGst;
        $this->cgst_amount = $totalCgst;
        $this->sgst_amount = $totalSgst;
        $this->igst_amount = 0;
        $this->discount = $totalDiscountAll;
        $this->grand_total = $finalTotal;
        $this->round_off = $roundOff;
        $this->save();

        return $this;
    }
}