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

        foreach ($items as $item) {
            $originalSubtotal += $item->price * $item->quantity;
            $totalTaxable += $item->taxable_amount;
            $totalGst += $item->gst_amount;
            $totalCgst += $item->cgst_amount;
            $totalSgst += $item->sgst_amount;
            $totalIgst += $item->igst_amount;
        }

        $discountPercent = floatval($this->discount_percentage ?? 0);
        $totalBeforeDiscount = $totalTaxable + $totalGst;
        $discountAmount = ($totalBeforeDiscount * $discountPercent) / 100;
        $grandTotal = $totalBeforeDiscount - $discountAmount;
        $finalTotal = round($grandTotal);
        $roundOff = $finalTotal - $grandTotal;

        $this->total_amount = $originalSubtotal;
        $this->taxable_amount = $totalTaxable;
        $this->gst_amount = $totalGst;
        $this->cgst_amount = $totalCgst;
        $this->sgst_amount = $totalSgst;
        $this->igst_amount = $totalIgst;
        $this->discount = $discountAmount;
        $this->grand_total = $finalTotal;
        $this->round_off = $roundOff;
        $this->save();

        return $this;
    }
}